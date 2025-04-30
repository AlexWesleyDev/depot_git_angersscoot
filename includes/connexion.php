<?php
$host = '192.168.127.30'; // IP du serveur MySQL
$dbname = 'angersscoot_BDD';
$user = 'Utilisateur_admin';           // à adapter si besoin
$mdpass = 'hL4(IM8b6W)585]U';              // à adapter aussi

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $mdpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion à la base de données impossible : " . $e->getMessage());
}
?>
