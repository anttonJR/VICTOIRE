<?php

// gestion des sessions (doit etre appelee avant tout affichage)
session_start();

// gestion des cookies
$cookieName = 'workshop_last_visit';
$lastVisit = $_COOKIE[$cookieName] ?? null;
$currentVisit = date('Y-m-d H:i:s');

setcookie($cookieName, $currentVisit, [
    'expires' => time() + (60 * 60 * 24 * 30), // 30 jours
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (!isset($_SESSION['visit_count'])) {
    $_SESSION['visit_count'] = 0;
}
$_SESSION['visit_count']++;

// RAPPEL pour accdéder à la base de données:
// http://localhost:8888/phpMyAdmin5


// ouverture de la connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "workshop";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

// requete SQL (lecture)
$sql = "SELECT vote FROM `test` WHERE login = 'jp' and pass = 'coucou'";
$result = $conn->query($sql);
echo($result->num_rows); // nb de lignes dans le résultat de la requete
echo ($result->fetch_assoc()["vote"]); // affiche le vote de jp


// requete SQL (ecriture)
$sql = "INSERT INTO `test` (`login`, `pass`, `vote`) VALUES ('test2', 'test2', '1')";
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// fermeture de la connexion à la base de données
$conn->close();

echo "<br>Derniere visite (cookie): " . htmlspecialchars($lastVisit ?? 'premiere visite', ENT_QUOTES, 'UTF-8');
echo "<br>Pages vues pendant la session: " . $_SESSION['visit_count'];
echo "<br>ID de session: " . session_id();
