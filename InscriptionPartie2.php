<?php
// FACULTATIF  VOIR LES ERREURS
error_reporting(E_ALL);
ini_set('display_errors', 1);
//
session_start();
require_once("includes/connexion.php");

// Vérification des données de l'étape 1 (login, mdp, cgu)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"], $_POST["mdp"], $_POST["cgu"])) {
    $_SESSION["login_inscription"] = htmlspecialchars($_POST["login"]);
    $_SESSION["mdp_inscription"] = password_hash($_POST["mdp"], PASSWORD_BCRYPT);
} elseif (!isset($_SESSION["login_inscription"], $_SESSION["mdp_inscription"])) {
    // Si on arrive ici sans avoir passé l'étape 1
    header("Location: ../index.html");
    exit;
}

// Traitement après soumission du formulaire final
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nom"], $_POST["prenom"])) {
    $nom = htmlspecialchars($_POST["nom"]);
    $prenom = htmlspecialchars($_POST["prenom"]);
    $login = $_SESSION["login_inscription"];
    $mdp = $_SESSION["mdp_inscription"];

    // Vérifie si l’identifiant existe déjà
    $stmt = $pdo->prepare("SELECT IDUTIL FROM UTILISATEUR WHERE LOGINUTIL = ?");
    $stmt->execute([$login]);

    if ($stmt->rowCount() > 0) {
        echo "❌ Cet identifiant est déjà utilisé. Veuillez en choisir un autre.";
        session_destroy();
        exit;
    }

    // Insertion dans UTILISATEUR (IDROLE = 1 => Chargeur)
    $stmt = $pdo->prepare("INSERT INTO UTILISATEUR (NOMUTIL, PRENOMUTIL, LOGINUTIL, MDPUTIL, ACTIFUTIL, IDROLE)
                           VALUES (?, ?, ?, ?, 0, 1)");
    $stmt->execute([$nom, $prenom, $login, $mdp]);

    $idUtilisateur = $pdo->lastInsertId();

    // Fonction d'upload des documents
    function uploadDoc($name, $idUser, $typeId) {
        $dir = "../uploads/";
        if (!isset($_FILES[$name]) || $_FILES[$name]['error'] !== UPLOAD_ERR_OK) return false;
        $filename = basename($_FILES[$name]['name']);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, ["pdf", "jpg", "jpeg", "png"])) return false;
        $newName = uniqid() . "_" . $filename;
        $path = $dir . $newName;
        if (!file_exists($dir)) { mkdir($dir, 0755, true); }
        if (move_uploaded_file($_FILES[$name]["tmp_name"], $path)) {
            global $pdo;
            $stmt = $pdo->prepare("INSERT INTO DOCUMENT (NOMDOCU, LIENDOCU, IDUTIL, IDTYPEDOCU)
                                   VALUES (?, ?, ?, ?)");
            $stmt->execute([$filename, $path, $idUser, $typeId]);
            return true;
        }
        return false;
    }

    // Upload des 3 documents (IDTYPEDOCU : 1=CNI, 2=Domicile, 3=Auto-entreprise)
    $ok1 = uploadDoc("cni", $idUtilisateur, 1);
    $ok2 = uploadDoc("domicile", $idUtilisateur, 2);
    $ok3 = uploadDoc("autoentreprise", $idUtilisateur, 3);

    if ($ok1 && $ok2 && $ok3) {
        echo "<h2>✅ Votre inscription a bien été prise en compte !</h2>";
        echo "<p>Votre compte est en attente de validation par l'administrateur.</p>";
        echo "<button onclick=\"window.location.href='../index.html'\">Retour à l'accueil</button>";
        session_destroy();
    } else {
        echo "❌ Erreur lors de l'upload des documents. Veuillez réessayer.";
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Étape 2</title>

    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="img/logo.png" />

    <!-- lien vers le js interne au site -->
    <script src="./js/accueil.js"></script>
    

    <!-- Liens vers le css interne au site -->
        
    <!--
    <link href="css/inscription.css" rel="stylesheet">
    <link rel="stylesheet" href="css/formulaireConnexion.css">
    <link rel="stylesheet" href="css/formulaireInscription.css">
    -->
   
    
    <!-- Lien de police de la lengende du logo : ANGERSCOOT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Boldonse&display=swap" rel="stylesheet">
    <!-- Lien de police des paragraphes de l'accueil -->
    <link href=" https://fonts.cdnfonts.com/css/codec-pro " rel="stylesheet">


    <!-- Lien ICONE Afficher/MASQUER le mot de passe saisi -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>AngersSCOOT</title>

</head>
<body>
    <h1>Inscription - Étape 2 : Informations complémentaires</h1>
    <form method="post" enctype="multipart/form-data">
        <label>Nom :</label><br>
        <input type="text" name="nom" required><br><br>

        <label>Prénom :</label><br>
        <input type="text" name="prenom" required><br><br>

        <label>Carte d'identité (PDF/JPG/PNG) :</label><br>
        <input type="file" name="cni" required><br><br>

        <label>Justificatif de domicile :</label><br>
        <input type="file" name="domicile" required><br><br>

        <label>Justificatif auto-entreprise :</label><br>
        <input type="file" name="autoentreprise" required><br><br>

        <button type="submit">Finaliser l'inscription</button>
    </form>
</body>
</html>