<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config.php';
session_start();

if (empty($_SESSION['utilisateur'])) {
    http_response_code(401);
    echo json_encode(['erreur' => 'Non authentifié.']);
    exit;
}

$user   = $_SESSION['utilisateur'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = getDB();

    if ($method === 'GET') {
        if ($user['role'] !== 'etudiant') {
            http_response_code(403);
            echo json_encode(['erreur' => 'Réservé aux étudiants.']);
            exit;
        }

        $stmt = $pdo->prepare(
            'SELECT cr.*, p.titre AS projet_titre
             FROM compte_rendu cr
             JOIN projet p ON p.id = cr.id_projet
             WHERE cr.id_etudiant = ?
             ORDER BY cr.date_depot DESC'
        );
        $stmt->execute([$user['id']]);

        echo json_encode($stmt->fetchAll());
        exit;
    }

    if ($method === 'POST') {
        if ($user['role'] !== 'etudiant') {
            http_response_code(403);
            echo json_encode(['erreur' => 'Réservé aux étudiants.']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['titre']) || empty($data['contenu']) || empty($data['id_projet'])) {
            http_response_code(400);
            echo json_encode(['erreur' => 'Titre, contenu et projet requis.']);
            exit;
        }

        // Verify the project belongs to this student
        $check = $pdo->prepare('SELECT id FROM projet WHERE id = ? AND id_etudiant = ?');
        $check->execute([$data['id_projet'], $user['id']]);
        if (!$check->fetch()) {
            http_response_code(403);
            echo json_encode(['erreur' => 'Projet non trouvé ou accès refusé.']);
            exit;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO compte_rendu (id_projet, id_etudiant, titre, contenu, statut)
             VALUES (?, ?, ?, ?, \'en_attente\')'
        );
        $stmt->execute([$data['id_projet'], $user['id'], $data['titre'], $data['contenu']]);
        $idCr = $pdo->lastInsertId();

        http_response_code(201);
        echo json_encode(['id' => $idCr, 'message' => 'Compte rendu soumis avec succès.']);
        exit;
    }

    http_response_code(405);
    echo json_encode(['erreur' => 'Méthode non autorisée.']);
} catch (PDOException $e) {
    error_log('comptes_rendus.php PDOException: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['erreur' => 'Erreur serveur.']);
}
?>
