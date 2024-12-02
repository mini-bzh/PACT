/* ------------------------- gestion menu déroulant contexte visite ------------------------- */
let menuDeroulant = document.getElementById("menuContexte");
let btnDeplierMenu = document.querySelectorAll("#menuContexte .conteneurSVGtexte")[0];
let inputContexte = document.getElementById("inputContexte");
let optionsContexte = document.querySelectorAll("#conteneurOptionsContexte p");
let titreSelect = document.querySelectorAll("#menuContexte .conteneurSVGtexte p")[0];

btnDeplierMenu.addEventListener("click", toggleMenuContexte);

function toggleMenuContexte()
{
    menuDeroulant.classList.toggle("deplie");
}

optionsContexte.forEach(option => {
    option.addEventListener("click", optionSelectionnee);
});

function optionSelectionnee()
{
    titreSelect.textContent = event.target.textContent;
    inputContexte.value = event.target.textContent;

    toggleMenuContexte();
}

/* ------------------------- gestion ajout image ------------------------- */
function updateFileName() {
    const fileInput = document.getElementById('fichier1'); // Champ de fichier
    const fileName = document.getElementById('fileName'); // Zone où afficher le nom
    const label = document.getElementById('customFileLabel'); // Label du bouton

    console.log(label);

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


/* ------------------------- vérification validité avant submit ------------------------- */
let btnSubmit = document.querySelectorAll(".btnConfirmer")[0];
let champDate = document.getElementById("dateExperience");
btnSubmit.addEventListener("click", verifAvantSubmit);

const dateActuelle = new Date();

// Extraire l'année, le mois et le jour
let annee = dateActuelle.getFullYear();
let mois = String(dateActuelle.getMonth() + 1).padStart(2, '0'); // Les mois commencent à 0, donc +1
let jour = String(dateActuelle.getDate()).padStart(2, '0');

// Construire la date au format aaaa-mm-jj
let dateAjs = `${annee}-${mois}-${jour}`;

console.log(champDate.value);

function verifAvantSubmit()
{
    let valide = true;
    let messageErreur = "";

    if(titreSelect.textContent == "Séléctionner un contexte")
    {
        valide = false;
    }

    console.log(champDate.value);

}