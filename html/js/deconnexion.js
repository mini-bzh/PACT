function deconnexion() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "gestionDeconnexion.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Gérer la réponse du serveur
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log("Déconnexion réussie");
            window.location.href = "https://tripskell.ventsdouest.dev/pages/accueil.php";
        } else {
            console.error("Erreur lors de la déconnexion.");
        }
    };

    xhr.send("action=executer");
}