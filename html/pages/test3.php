<?php
// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');
    
// connexion a la BdD

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$ouverture = $dbh->query("select * from tripskell._ouverture where idoffre=2;")->fetchAll();

echo "ouverture <br>";
print_r($ouverture);


echo "<br> horaire <br>";
foreach ($ouverture as $key => $value) {
    $horaire = $dbh -> query("select * from tripskell._horaire as h join tripskell._ouverture as o on h.id_hor=". $ouverture[$key]["id_hor"] ." where o.idOffre=2 and o.id_hor=". $ouverture[$key]["id_hor"] ." and o.id_jour='". $ouverture[$key]["id_jour"] ."';")->fetchAll();
    print_r($horaire);
    echo "<br>";
}

$stmt = $dbh->prepare("select * from tripskell._possede");
$stmt->execute();
$result = $stmt->fetchAll();


echo "<br> contentOffre <br>";
$contentOffre = $dbh->query("select * from tripskell.offre_visiteur where idoffre=2;")->fetchAll()[0];

/*foreach ($result as $row)
{
    print_r($row);
    ?><br><br><?php
}*/
