<?php
session_start();

if ((isset($_POST['action'])) && ($_POST['action'] === 'executer')) {  // Si on reçoit via POST l'ordre d'executer (depuis deconnexion.js)
    $_SESSION['idCompte'] = null;                                      // On déconnecte l'utilisateur
}


?>