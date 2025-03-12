function suppressionOffre() {
    let password = document.getElementById("pswSupOffre").value;
    let idOffre = document.getElementById("idOffre").value; // L'ID de l'offre à supprimer, récupéré via un champ caché ou autre méthode

    // Vérification du mot de passe
    fetch("../composants/ajax/verif_mdp_sup_offre.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "password=" + encodeURIComponent(password)
    })
    .then(response => response.json()) // Convertir la réponse en JSON
    .then(data => {
        console.log(data);
        if (data.success) {
            // Suppression de l'offre
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "../composants/ajax/gestionSuppressionOffre.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log("Suppression réussie");
                    window.location.href = "../pages/gestionOffres.php"; // Redirection vers la page d'accueil ou liste des offres
                } else {
                    console.error("Erreur lors de la suppression de l'offre.");
                }
            };

            xhr.send("action=supprimerOffre&idOffre=" + encodeURIComponent(idOffre));
        } else {
            // Afficher un message d'erreur si le mot de passe est incorrect
            document.getElementById("textNonValideOffre").classList.remove("displayNone");
            document.getElementById("pswSupOffre").value = "";
        }
    })
    .catch(error => console.error("Erreur :", error));
}

// Fonction pour afficher la pop-up de suppression d'offre
function confSupOffre(idOffre) {
    let pop = document.querySelector('.popUpSupOffre');
    pop.style.display = 'flex';
    document.body.classList.add('no-scroll');
    document.getElementById("idOffre").value = idOffre; // Passer l'ID de l'offre à la pop-up
}

function fermeConfSupOffre() {
    let pop = document.querySelector('.popUpSupOffre');
    pop.style.display = 'none';
    document.body.classList.remove('no-scroll');
}

let mdpOffre = document.getElementById("pswSupOffre");
let btnSupOffre = document.getElementsByClassName("btnValiderSupOffre")[0];
let textErrorOffre = document.getElementById("textNonValideOffre");

function checkInputsOffre() {
    textErrorOffre.classList.add("displayNone");
    if (mdpOffre.value.trim() !== "") {
        btnSupOffre.disabled = false;
        btnSupOffre.style.backgroundColor = "red";
    } else {
        btnSupOffre.disabled = true;
        btnSupOffre.style.backgroundColor = "#b9b9b9";
    }
}

mdpOffre.addEventListener("input", checkInputsOffre);
