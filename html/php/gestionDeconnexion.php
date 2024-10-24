<?php
session_start();

if ((isset($_POST['action'])) && ($_POST['action'] === 'executer')) {
    $_SESSION['idCompte'] = null;
}


?>