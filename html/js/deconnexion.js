function deconnexion() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../php/gestionDeconnexion.php", true);
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


// Fonction d'affichage de la pop-up
function confDeco() {
    let pop = document.querySelector('.popUpDeco');
    pop.style.display = 'flex';
    document.body.classList.add('no-scroll');
}

function fermeConfDeco() {
    let pop = document.querySelector('.popUpDeco');
    pop.style.display = 'none';
    document.body.classList.remove('no-scroll');
}