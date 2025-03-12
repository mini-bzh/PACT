function suppressionCompte() {
    let password = document.getElementById("pswSupCompte").value;

    fetch("../composants/ajax/verif_mdp_sup.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "password=" + encodeURIComponent(password)
    })
    
    .then(response => response.json()) // Convertir la réponse en JSON
    .then(data => {
        if (data.success) {
            // Permet de faire des requêtes sans recharger la page
            let xhr = new XMLHttpRequest(); // Initialisation
            xhr.open("POST", "../composants/ajax/gestionSuppressionCompte.php", true); // Via la méthode POST et transmet à gestionDeconnexion.php
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            // Gérer la réponse du serveur
            xhr.onload = function() {  // Quand la réponse du serveur est reçue, la fonction est lancée
                if (xhr.status === 200) { // Cas de succès
    
                    console.log(document.cookie);
                    
                    document.cookie = "poucesAvis=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;SameSite=Lax";
    
                    console.log("Suppression réussie");
                    window.location.href = "../pages/accueil.php";
                } else { // Cas d'échec
                    console.error("Erreur lors de la suppression du compte.");
                }
            };
    
            xhr.send("action=supprimerCompte"); // Envoie la requête avec $_POST['action'] = supprimerCompte
        } else {
            // Afficher le message d'erreur en enlevant la classe displayNone
            document.getElementById("textNonValide").classList.remove("displayNone");
            document.getElementById("pswSupCompte").value = "";
        }
    })
    .catch(error => console.error("Erreur :", error));

    
}

// Fonction d'affichage de la pop-up de suppression
function confSup() {
    let pop = document.querySelector('.popUpSup');
    pop.style.display = 'flex';
    document.body.classList.add('no-scroll');
}

function fermeConfSup() {
    let pop = document.querySelector('.popUpSup');
    pop.style.display = 'none';
    document.body.classList.remove('no-scroll');
}

let mdp = document.getElementById("pswSupCompte");

let btnSup = document.getElementsByClassName("btnValiderSup")[0];

let textError3 = document.getElementById("textNonValide");

function checkInputs() {
    textError3.classList.add("displayNone");
    if (mdp.value.trim() !== "") {
      btnSup.disabled = false;
      btnSup.style.backgroundColor = "red";
    } else {
      btnSup.disabled = true;
      btnSup.style.backgroundColor = "#b9b9b9";
    }
}

mdp.addEventListener("input", checkInputs);