<?php
    session_start(); // recuperation de la sessions

    // recuperation des parametre de connection a la BdD
    include('/var/www/html/php/connection_params.php');
    
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    // cree $comptePro qui est true quand on est sur un compte pro et false sinon
    include('/var/www/html/php/verif_compte_pro.php');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis</title>
    <link rel="stylesheet" href="/style/pages/avis.css">
</head>
<body class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($comptePro)
            {
                echo "fondPro";
            }
            else
            {
                echo "fondVisiteur";
            }
    ?>>
        
    <?php include "/var/www/html/composants/header/header.php";        //import header (navbar)
    ?>
    <main>
        <h1>Coming soon !</h1>
    </main>
    <?php                                                   //import footer
            include "/var/www/html/composants/footer/footer.php";
    ?>
</body>
</html>