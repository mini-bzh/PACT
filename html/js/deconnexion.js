
function deconnexion() 
{
    // Permet de faire des requêtes sans recharger la page
    let xhr = new XMLHttpRequest(); // Initialisation
    xhr.open("POST", "../composants/ajax/gestionDeconnexion.php", true); // Via la méthode POST et transmet à gestionDeconnexion.php
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    // Gérer la réponse du serveur
    xhr.onload = function() {  // Quand la réponse du serveur est reçue, la fonction est lancée
        if (xhr.status === 200) { // Cas de succès

            console.log(document.cookie);
            
            document.cookie = "poucesAvis=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;SameSite=Lax";
            document.cookie = "offresVues=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;SameSite=Lax";

            console.log("Déconnexion réussie");
            window.location.href = "../pages/accueil.php";
        } else { // Cas d'échec
            console.error("Erreur lors de la déconnexion.");
        }
    };

    xhr.send("action=executer"); // Envoie la requête avec $_POST['action'] = executer
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