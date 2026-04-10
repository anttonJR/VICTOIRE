<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Connexion MySQL (mêmes infos que my_db.php / MAMP)
$servername = "localhost";
$username   = "root";
$password   = "root";

// Connexion sans sélectionner de BDD pour pouvoir la créer si besoin
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Connexion impossible : ' . $conn->connect_error]);
    exit;
}

// Créer la base de données si elle n'existe pas
$conn->query("CREATE DATABASE IF NOT EXISTS `victoires` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db('victoires');

// Créer la table des votes si elle n'existe pas
$conn->query("
    CREATE TABLE IF NOT EXISTS `votes` (
        `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
        `email`      VARCHAR(255)    NOT NULL,
        `artiste`    VARCHAR(100)    NOT NULL,
        `categorie`  VARCHAR(50)     NOT NULL,
        `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

// Récupérer les données POST
$email     = trim($_POST['email']     ?? '');
$artiste   = trim($_POST['artiste']   ?? '');
$categorie = trim($_POST['categorie'] ?? '');

// Validation basique
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Adresse e-mail invalide.']);
    $conn->close();
    exit;
}

if ($artiste === '' || $categorie === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Données manquantes.']);
    $conn->close();
    exit;
}

// Vérifier si cet email a déjà voté dans cette catégorie
$stmt = $conn->prepare("SELECT id FROM `votes` WHERE email = ? AND categorie = ?");
$stmt->bind_param('ss', $email, $categorie);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $conn->close();
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'Cet e-mail a déjà voté dans cette catégorie.']);
    exit;
}
$stmt->close();

// Insérer le vote
$stmt = $conn->prepare("INSERT INTO `votes` (`email`, `artiste`, `categorie`) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $email, $artiste, $categorie);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Vote enregistré !']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'enregistrement.']);
}

$stmt->close();
$conn->close();
