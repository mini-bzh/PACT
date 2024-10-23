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



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        foreach($dbh->query("SELECT * from tripskell._offre") as $row) {
            echo "<pre>"; // pour la version navigateur (présentation brute)
            ?>
                <p><?php echo $row["titreoffre"]?></p>
            <?php
            echo "</pre>";
        }
    ?>
</body>
</html>