<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);


    $profil = null;
    if(key_exists("user", $_GET))
    {
        $profil =$_GET["user"];
    }

    $driver = "pgsql";

    $server = "postgresdb";
    $dbname = "postgres";

    $user = "sae";
    $pass = "ashton-izzY-c0mplet";

    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        // Récupérer les paramètres envoyés via POST
        print_r($_POST);
        $idOffre = $_POST["idOffre"];

        $stmt = $dbh->prepare("SELECT enLigne from tripskell.offre_pro where idoffre = ".$idOffre);

        $stmt->bindParam('idoffre', $idOffre, PDO::PARAM_STR);
        
        $stmt->execute();
        $result = $stmt->fetchAll();        
        
        echo $result;
    } 
    else 
    {
        echo "erreur !";
    }
?>