// Fonction pour afficher la pop-up
function afficherPopUpMdp() {
    document.getElementById("popUpMdp").style.display = "flex";
}

// Fonction pour fermer la pop-up
function fermerPopUpMdp() {
    document.getElementById("popUpMdp").style.display = "none";
}

// Fonction pour vérifier le mot de passe
function verifierMotDePasse() {
    const inputMdp = document.getElementById("passwordInput").value;

    // Exemple de vérification côté client (ne pas utiliser en production sans back-end sécurisé)
    if (inputMdp === "votre_mot_de_passe_test") {
        alert("Mot de passe correct !");
        fermerPopUpMdp();
        // Effectuer l'action sensible ici (par exemple, suppression de compte)
    } else {
        alert("Mot de passe incorrect. Veuillez réessayer.");
    }
}

// Fonction pour afficher la pop-up et bloquer la page
function afficherPopUpMdp() {
    const overlay = document.getElementById("overlay");
    overlay.style.display = "flex"; // Afficher la pop-up avec le fond bloquant
    document.body.classList.add("no-scroll"); // Désactiver le scroll
}

// Fonction pour fermer la pop-up et débloquer la page
function fermerPopUpMdp() {
    const overlay = document.getElementById("overlay");
    overlay.style.display = "none"; // Masquer la pop-up
    document.body.classList.remove("no-scroll"); // Réactiver le scroll
}
