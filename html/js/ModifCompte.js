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


    function updateFileName() {
        const fileInput = document.getElementById('fichier1'); // Champ de fichier
        const fileName = document.getElementById('fileName'); // Zone où afficher le nom
        const label = document.getElementById('customFileLabel'); // Label du bouton

        if (fileInput.files.length > 0) {
            // Si un fichier est sélectionné, afficher son nom
            fileName.textContent = fileInput.files[0].name;
            label.textContent = "Changer la photo"; // Met à jour le texte du bouton
        } else {
            // Si aucun fichier n'est sélectionné
            fileName.textContent = "";
            label.textContent = "📷 Ajouter une photo de profil"; // Remet le texte original
        }
    }

    const form = document.getElementById('form'); // Élément du formulaire
    const password = document.getElementById('Mot_de_P'); // Champ mot de passe
    const confirmPassword = document.getElementById('Confirm_Mot_de_P'); // Champ confirmation
    const regexMdp = /^(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).+$/; // Regex pour validation

    // Écoute de la soumission du formulaire
    form.addEventListener('submit', function (e) {
        // Vérifie si les champs sont remplis
        if (!password.value || !confirmPassword.value) {
            e.preventDefault(); // Empêche le formulaire de s'enregistrer
            alert('Tous les champs sont obligatoires.'); // Affiche une alerte
            return; // Sort de la fonction
        }

        // Vérifie si les mots de passe correspondent
        if (password.value !== confirmPassword.value) {
            e.preventDefault(); // Empêche le formulaire de s'enregistrer
            alert('Les mots de passe ne correspondent pas.'); // Affiche une alerte
            return; // Sort de la fonction
        }

        // Vérifie si le mot de passe respecte les critères (chiffre et caractère spécial)
        if (!regexMdp.test(password.value)) {
            e.preventDefault(); // Empêche la soumission du formulaire
            alert("Le mot de passe doit contenir au moins un chiffre et un caractère spécial.");
            return; // Sort de la fonction
        }
    });


    
const showCheckbox = document.getElementById('showCheckbox');
const hideCheckbox = document.getElementById('hideCheckbox');
const typeDomaineInput = document.getElementById('typeDomaine');
const extraFields = document.getElementById('extraFields');

// Fonction pour vérifier et forcer qu'une case est cochée
function checkCheckboxes() {
    if (!showCheckbox.checked && !hideCheckbox.checked) {
        // Si aucune case n'est cochée, on coche par défaut la case "Domaine Privé"
        showCheckbox.checked = true;
        typeDomaineInput.value = 'privé';
        extraFields.classList.remove('hidden');
    }
}

// Mise à jour des champs et sections en fonction des cases cochées
showCheckbox.addEventListener('change', function () {
    if (this.checked) {
        typeDomaineInput.value = 'privé';
        hideCheckbox.checked = false;
        extraFields.classList.remove('hidden');
    } else {
        typeDomaineInput.value = '';
        extraFields.classList.add('hidden');
    }
    checkCheckboxes();  // Vérifie l'état des cases
});

hideCheckbox.addEventListener('change', function () {
    if (this.checked) {
        typeDomaineInput.value = 'public';
        showCheckbox.checked = false;
        extraFields.classList.add('hidden');
    } else {
        typeDomaineInput.value = '';
    }
    checkCheckboxes();  // Vérifie l'état des cases
});

// Vérifie initialement si une case est cochée au chargement de la page
checkCheckboxes();
