let prix = document.getElementById("prix-minimal");
let num_adr = document.getElementById("num");
let code_postal = document.getElementById("codePostal");
let ageminimum = document.getElementById("ageminimum");
let nbAttraction = document.getElementById("nbAttraction");
let capacite = document.getElementById("capacite");
let agemin = document.getElementById("agemin");

prix.onkeydown = (event) => {
    if ((isNaN(event.key) && event.key !== 'Backspace') || event.key === ' ') {
        event.preventDefault();
    }
};


num_adr.onkeydown = (event) => {
    if ((isNaN(event.key) && event.key !== 'Backspace') || event.key === ' ') {
        event.preventDefault();
    }
};


code_postal.onkeydown = (event) => {
    if ((isNaN(event.key) && event.key !== 'Backspace') || event.key === ' ') {
        event.preventDefault();
    }
};


ageminimum.onkeydown = (event) => {
    if ((isNaN(event.key) && event.key !== 'Backspace') || event.key === ' ') {
        event.preventDefault();
    }
};


nbAttraction.onkeydown = (event) => {
    if ((isNaN(event.key) && event.key !== 'Backspace') || event.key === ' ') {
        event.preventDefault();
    }
};


capacite.onkeydown = (event) => {
    if ((isNaN(event.key) && event.key !== 'Backspace') || event.key === ' ') {
        event.preventDefault();
    }
};

agemin.onkeydown = (event) => {
    if ((isNaN(event.key) && event.key !== 'Backspace') || event.key === ' ') {
        event.preventDefault();
    }
};


/* --------------------------------- Taille max d'une image  --------------------------------- */


let inputImg = document.querySelectorAll('#formCreaOffre input[type="file"]');
inputImg.forEach(element => {
    element.addEventListener('change', function(event) {
        let file = event.target.files[0];
        if (file) {
            // Vérifier la taille du fichier (par exemple 5 Mo maximum)
            let maxSize = 1 * 1024 * 1024; // 1 Mo
            if (file.size > maxSize) {
                alert("L'image est trop lourde. La taille maximale est de 1 Mo par image.");
                event.target.value = ''; // Réinitialiser l'input file
            }
        }
    });
});

//afficher les messages d'erreur si besoin
let btnConfirm = document.querySelectorAll(".btnConfirmer"); // Sélectionne tous les boutons avec la classe "btnConfirmer"

btnConfirm.forEach(btn => {
    btn.addEventListener("click", function(event) {
        imageVide(event);
    });
});

function imageVide(event) {
    let isEmpty = true;

    inputImg.forEach(input => {
        if (input.files.length > 0) {
            isEmpty = false; // Si au moins un fichier est sélectionné, on valide
        }
    });

    if (isEmpty) {
        alert("Veuillez enregistrer une image pour votre offre");
        event.preventDefault(); // Empêche l'action si aucune image n'est sélectionnée
    }
}


/* --------------------------------- partie horaires  --------------------------------- */ 


/* fonction qui permet de changer le bouton au moment de cliquer dessus*/
function jourClique(event) {
    let boutonClique = event.currentTarget; // Récupère le bouton cliqué
    let parent = boutonClique.parentElement; // On récupère le parent du bouton cliqué
    let texteOuvert = parent.querySelector(".ouvert"); // Sélectionne l'élément interne
    let texteFermer = parent.querySelector(".fermer"); // Sélectionne l'élément interne
    heures1 = texteOuvert.querySelector(".heures1"); // Sélectionne l'élement heures1
    heures2 = texteOuvert.querySelector(".heures2"); // Sélectionne l'élement heures2
    //let texteBouton = boutonClique.querySelector("button"); // Sélectionne l'élément interne

    if (texteOuvert) { 
        texteOuvert.classList.toggle("horairesCacher"); // Ajoute ou enlève la classe pour cacher/afficher
        texteFermer.classList.toggle("horairesAfficher"); // Ajoute ou enlève la classe pour cacher/afficher
        boutonClique.classList.toggle("jourOuvert"); // On change le bouton

        if (texteFermer.classList.contains("horairesAfficher")) {
            // si la partie  fermer (.fermer) est afficher alors
            // tout les champs de saisies sont vides
            heures1.querySelector(".heure-debut").value = "";
            heures1.querySelector(".heure-fin").value = "";
            heures2.querySelector(".heure-debut").value = "";
            heures2.querySelector(".heure-fin").value = "";
        }

    }
}

// Ajout des event listeners aux boutons
document.querySelectorAll(".btnHoraire").forEach(bouton => {
    bouton.addEventListener("click", jourClique);
});

/* --------------------------------- ajout deuxième horaire  --------------------------------- */

function toggleHoraire2(event)                       //toggle l'affichage des champs pour ajouter un 2e couple d'horaires
{
    let boutonClique = event.currentTarget;
    let parent = boutonClique.parentElement;
    let grandParent = parent.parentElement;
    let heureCacher = grandParent.querySelector(".heures2"); // On récupère les heures cacher 

    heureCacher.classList.toggle("horairesAfficher"); // on affiche la zone cacher 
    heureCacher.classList.toggle("horairesCacher"); // on enlève ce qui la cache
    if (boutonClique.textContent == "+") { // si le contenue du bouton est "+" on le change de + à - 
        boutonClique.textContent = "-";
    }
    else { // ici on recache le champs de saisie d'heure ne plus 
        boutonClique.textContent = "+"; // on remplace le contenue par "+" et on met les valeurs à vide
        heureCacher.querySelector(".heure-debut").value = "";
        heureCacher.querySelector(".heure-fin").value = "";
    }
}

// Ajout des event listeners aux boutons
document.querySelectorAll(".btnAjoutHoraire").forEach(bouton => {
    bouton.addEventListener("click", toggleHoraire2);
});


/* ----------------------------------------------- categorie et tag ----------------------------------------------- */

let categorie = document.getElementById("categorie");
categorie.addEventListener("change", changeCategoriesTags);
function changeCategoriesTags() {
    if (categorie.value == "visite") {
        // catégorie
        document.getElementById("champsVisite").style.display = "contents";
        document.getElementById("champsSpectacle").style.display = "none";
        document.getElementById("champsActivite").style.display = "none";
        document.getElementById("champsPA").style.display = "none";
        document.getElementById("champsRestauration").style.display = "none";
        //tags
        document.getElementById("tagsVisite").style.display = "contents";
        document.getElementById("tagsSpectacle").style.display = "none";
        document.getElementById("tagsActivite").style.display = "none";
        document.getElementById("tagsPA").style.display = "none";
        document.getElementById("tagsRestauration").style.display = "none";

    } else if (categorie.value == "spectacle") {
        // catégorie
        document.getElementById("champsSpectacle").style.display = "contents";
        document.getElementById("champsActivite").style.display = "none";
        document.getElementById("champsPA").style.display = "none";
        document.getElementById("champsRestauration").style.display = "none";
        document.getElementById("champsVisite").style.display = "none";
        //tags
        document.getElementById("tagsSpectacle").style.display = "contents";
        document.getElementById("tagsVisite").style.display = "none";
        document.getElementById("tagsActivite").style.display = "none";
        document.getElementById("tagsPA").style.display = "none";
        document.getElementById("tagsRestauration").style.display = "none";
    } else if (categorie.value == "activite") {
        // catégorie
        document.getElementById("champsActivite").style.display = "contents";
        document.getElementById("champsPA").style.display = "none";
        document.getElementById("champsRestauration").style.display = "none";
        document.getElementById("champsVisite").style.display = "none";
        document.getElementById("champsSpectacle").style.display = "none";
        //tags
        document.getElementById("tagsActivite").style.display = "contents";
        document.getElementById("tagsVisite").style.display = "none";
        document.getElementById("tagsSpectacle").style.display = "none";
        document.getElementById("tagsPA").style.display = "none";
        document.getElementById("tagsRestauration").style.display = "none";
    } else if (categorie.value == "parcDattraction") {
        // catégorie
        document.getElementById("champsPA").style.display = "contents";
        document.getElementById("champsRestauration").style.display = "none";
        document.getElementById("champsVisite").style.display = "none";
        document.getElementById("champsSpectacle").style.display = "none";
        document.getElementById("champsActivite").style.display = "none";
        //tags
        document.getElementById("tagsPA").style.display = "contents";
        document.getElementById("tagsVisite").style.display = "none";
        document.getElementById("tagsSpectacle").style.display = "none";
        document.getElementById("tagsActivite").style.display = "none";
        document.getElementById("tagsRestauration").style.display = "none";
    } else if (categorie.value == "restauration") {
        // catégorie
        document.getElementById("champsRestauration").style.display = "contents";
        document.getElementById("champsVisite").style.display = "none";
        document.getElementById("champsSpectacle").style.display = "none";
        document.getElementById("champsActivite").style.display = "none";
        document.getElementById("champsPA").style.display = "none";
        //tags
        document.getElementById("tagsRestauration").style.display = "contents";
        document.getElementById("tagsVisite").style.display = "none";
        document.getElementById("tagsSpectacle").style.display = "none";
        document.getElementById("tagsActivite").style.display = "none";
        document.getElementById("tagsPA").style.display = "none";
    } else if (categorie.value == "") {
        // catégorie
        document.getElementById("champsRestauration").style.display = "non";
        document.getElementById("champsVisite").style.display = "none";
        document.getElementById("champsSpectacle").style.display = "none";
        document.getElementById("champsActivite").style.display = "none";
        document.getElementById("champsPA").style.display = "none";
        //tags
        document.getElementById("tagsRestauration").style.display = "none";
        document.getElementById("tagsVisite").style.display = "none";
        document.getElementById("tagsSpectacle").style.display = "none";
        document.getElementById("tagsActivite").style.display = "none";
        document.getElementById("tagsPA").style.display = "none";
    } else {
        alert('Erreur sur les catégories')
    }
}

let option = document.getElementById("option");
option.addEventListener("change", saisieDateOption);
function saisieDateOption() {
    if ((option.value == "AlaUne") || (option.value == "EnRelief")) {
        document.getElementById("dateOption").style.display = "contents";
    }else{
        document.getElementById("dateOption").style.display = "none"; 
    }
}

function getLastMonday() {
    let today = new Date();
    let dayWeek = today.getDay();
    let daysToLastMonday = (dayWeek === 0 ? 6 : dayWeek - 1);
    let lastMonday = new Date(today);
    lastMonday.setDate(today.getDate() - daysToLastMonday); // Reculer jusqu'à lundi
    return lastMonday.toISOString().split("T")[0]; // Format ISO (AAAA-MM-JJ)
}

document.getElementById("date_debut_opt").min = getLastMonday();
// document.getElementById("date_fin_abo").min = new Date().toISOString().split("T")[0];