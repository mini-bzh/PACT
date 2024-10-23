<?php
    $profil = null;
    if(key_exists("user", $_GET))
    {
        $profil =$_GET["user"];
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis</title>
    <link rel="stylesheet" href="../style/pages/avis.css">
</head>
<body class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($profil == "pro")
            {
                echo "fondPro";
            }
            else
            {
                echo "fondVisiteur";
            }
    ?>>
        
    <?php include "../composants/header/header.php";        //import header (navbar)
    ?>
    <main>
        <h1>Coming soon !</h1>
    </main>
    <?php                                                   //import footer
            include "../composants/footer/footer.php";
    ?>
</body>
</html>