<?php

$driver = "pgsql";

$server = "postgresdb";
$dbname = "postgres";

$user = "sae";
$pass = "ashton-izzY-c0mplet";

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$stmt = $dbh->prepare("SELECT * from tripskell.pro_prive");

$username = $_POST['userName'];
$password = $_POST['userPSW'];

$stmt = $dbh->prepare("SELECT * from tripskell.pro_prive");

$stmt->execute();
$result = $stmt->fetchAll();

$stmt2 = $dbh->prepare("SELECT * from tripskell.pro_public");

$stmt2->execute();
$result2 = $stmt->fetchAll();

$correspond = false;

if (($correspond === false) && ($result)) {
    if ($password === $result['mot_de_passe']) {
        $correspond = true;
    }
}

if (($correspond === false) && ($result2)) {
    if ($password === $result2['mot_de_passe']) {
        $correspond = true;
    }
}

if ($correspond === true) {
    print_r("\n\nok\n\n");
}


?>