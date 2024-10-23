<?php
    $user = null;
    if(key_exists("user", $_GET))
    {
        $user =$_GET["user"];
    }
echo "HW";

$driver = "pgsql";

$server = "postgresdb";
$dbname = "postgres";

$user = "sae";
$pass = "ashton-izzY-c0mplet";

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);


foreach($dbh->query("SELECT * from tripskell._offre") as $row) {
        echo "<pre>"; // pour la version navigateur (présentation brute)
        print_r($row);
        echo "</pre>";
}
?>