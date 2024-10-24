<?php
session_start();

print_r("ok4");

if ((isset($_POST['action'])) && ($_POST['action'] === 'executer')) {
    $_SESSION['idCompte'] = null;
}


?>