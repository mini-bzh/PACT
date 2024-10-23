<?php
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

    $dbh->query("SELECT ")
?>