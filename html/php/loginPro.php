<?php

$driver = "pgsql";

$server = "postgresdb";
$dbname = "postgres";

$user = "sae";
$pass = "ashton-izzY-c0mplet";

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$username = $_POST['userName'];
$password = $_POST['userPSW'];

$stmt = $dbh->prepare("SELECT * from tripskell.pro_prive where raison_social = :username");

$stmt->bindParam(':username', $username, PDO::PARAM_STR);

$stmt->execute();
$result = $stmt->fetchAll();

$stmt2 = $dbh->prepare("SELECT * from tripskell.pro_public where raison_social = :username");

$stmt2->bindParam(':username', $username, PDO::PARAM_STR);

$stmt2->execute();
$result2 = $stmt->fetchAll();

$correspond = false;

if (($correspond === false) && ($result)) {
    if ($password === $result[0]['mot_de_passe']) {
        $correspond = true;
    }
}

if (($correspond === false) && ($result2)) {
    if ($password === $result2[0]['mot_de_passe']) {
        $correspond = true;
    }
}


if ($correspond === true) {
    print_r("\n\nok\n\n");
} else {
    header('Location: https://tripskell.ventsdouest.dev/pages/connexion.php?user-tempo=pro');
    exit();
}


?>

