<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/pages/compte.css">
</head>
<body>

<!------ HEADER  ------>
<?php
    echo file_get_contents('../composants/header/headerPC-Tab.php');
?>


<!------ MAIN  ------>
<main>

    <div class="pageChoixCo">
        <div class="textBulle">
            <p>Veuillez séléctionner une option de connexion</p>
        </div>

        <div>
<?php
            echo file_get_contents('../composants/btnConnexion/btnCoMembre.php');
            echo file_get_contents('../composants/btnConnexion/btnCoPro.php');
?>
        </div>

        <hr>

        <div class="textBulle">
            <p>Pas encore de compte ?<br>
               Créez le !</p>
        </div>

        <div>
            <div class="fakeDiv"></div>
<?php
            echo file_get_contents('../composants/btnConnexion/btnNouvCo.php');
?>
        </div>

    </div>


</main>


<!------ FOOTER  ------>

<?php
    echo file_get_contents('../composants/footer/footer.php');
?>

</body>
</html>