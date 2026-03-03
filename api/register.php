<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erreur' => 'Méthode non autorisée']);
    exit;
}

require_once 'config.php';
session_start();

$data     = json_decode(file_get_contents('php://input'), true);
$prenom   = trim($data['prenom']   ?? '');
$nom      = trim($data['nom']      ?? '');
$email    = trim($data['email']    ?? '');
$role     = trim($data['role']     ?? '');
$password = $data['password'] ?? '';
$confirm  = $data['confirm']  ?? '';

// Validation
if (!$prenom || !$nom || !$email || !$role || !$password || !$confirm) {
    http_response_code(400);
    echo json_encode(['erreur' => 'Tous les champs sont obligatoires']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['erreur' => 'Email invalide']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['erreur' => 'Le mot de passe doit contenir au moins 6 caractères']);
    exit;
}

if ($password !== $confirm) {
    http_response_code(400);
    echo json_encode(['erreur' => 'Les mots de passe ne correspondent pas']);
    exit;
}

$rolesAutorises = ['etudiant', 'tuteur', 'jury'];
if (!in_array($role, $rolesAutorises, true)) {
    http_response_code(400);
    echo json_encode(['erreur' => 'Rôle non autorisé']);
    exit;
}

try {
    $db = getDB();

    // Vérifier si email déjà utilisé
    $check = $db->prepare('SELECT id FROM utilisateur WHERE email = ?');
    $check->execute([$email]);
    if ($check->fetch()) {
        http_response_code(409);
        echo json_encode(['erreur' => 'Cet email est déjà utilisé']);
        exit;
    }

    // Hasher le mot de passe
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // Insérer l'utilisateur
    $stmt = $db->prepare(
        'INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role)
         VALUES (:nom, :prenom, :email, :mdp, :role)'
    );
    $stmt->execute([
        ':nom'    => $nom,
        ':prenom' => $prenom,
        ':email'  => $email,
        ':mdp'    => $hash,
        ':role'   => $role,
    ]);

    $newId = (int) $db->lastInsertId();

    // Démarrer session automatiquement
    $_SESSION['user_id']   = $newId;
    $_SESSION['user_role'] = $role;
    $_SESSION['user_nom']  = $prenom . ' ' . $nom;

    echo json_encode([
        'succes'      => true,
        'message'     => 'Compte créé avec succès !',
        'utilisateur' => [
            'id'     => $newId,
            'nom'    => $nom,
            'prenom' => $prenom,
            'email'  => $email,
            'role'   => $role,
        ],
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erreur' => 'Erreur serveur']);
}
?>
