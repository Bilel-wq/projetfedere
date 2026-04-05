USE gestion_pfe;

INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES
('Benali',  'Ahmed',  'etudiant@univ.dz',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('Zohra',   'Fatima', 'etudiant2@univ.dz',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('Kadi',    'Youcef', 'etudiant3@univ.dz',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('Meziane', 'Sara',   'tuteur@univ.dz',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tuteur'),
('Hadj',    'Karim',  'coordinateur@univ.dz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'coordinateur'),
('Bensaid', 'Nadia',  'jury@univ.dz',         '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jury');

INSERT INTO projet (titre, description, type_projet, statut, technologies, id_etudiant, id_tuteur) VALUES
('Application Mobile de Suivi des PFE', 'Développement d\'une application mobile permettant aux étudiants de suivre l\'avancement de leurs projets.', 'academique', 'valide', 'Flutter,Firebase,Dart', 1, 4),
('Système de Gestion des Absences', 'Plateforme web pour automatiser la gestion des absences des étudiants.', 'entreprise', 'en_cours', 'Django,React,PostgreSQL', 2, 4),
('IA pour la Détection de Plagiat', 'Outil basé sur l\'IA pour détecter le plagiat dans les rapports PFE.', 'academique', 'propose', 'Python,NLP,TensorFlow', 3, NULL);

INSERT INTO compte_rendu (id_projet, id_etudiant, titre, contenu, statut, commentaire_tuteur, date_depot) VALUES
(1, 1, 'Compte rendu #1 — Analyse des besoins', 'Réunion avec le tuteur, rédaction du cahier des charges et identification des fonctionnalités principales de l\'application.', 'valide', 'Bon travail, analyse claire et bien structurée. Continuez ainsi.', '2026-01-15 10:30:00'),
(1, 1, 'Compte rendu #2 — Conception de la base de données', 'Modélisation UML (diagramme de classes, MCD), choix de Firebase comme backend. Maquettes réalisées sur Figma.', 'valide', 'Conception correcte. Pensez à détailler les cardinalités dans le MCD.', '2026-02-05 14:00:00'),
(1, 1, 'Compte rendu #3 — Développement du module authentification', 'Implémentation de l\'authentification via Firebase Auth. Gestion des rôles étudiant et tuteur.', 'rejete', 'La gestion des erreurs est insuffisante. Veuillez revoir la validation des formulaires et les messages d\'erreur côté utilisateur.', '2026-02-28 09:15:00'),
(1, 1, 'Compte rendu #4 — Correction module authentification', 'Correction des problèmes signalés : validation des formulaires améliorée, messages d\'erreur complets, tests unitaires ajoutés.', 'en_attente', NULL, '2026-03-10 11:00:00');
