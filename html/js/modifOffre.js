/* -------------------------------- partie catégories  -------------------------------- */

if (categorie_offre == 'visite') {
    document.getElementById('champsVisite').style.display = 'block';
    document.getElementById('tagsVisite').style.display = 'flex';
} else if (categorie_offre == 'restauration') {
    document.getElementById('champsRestauration').style.display = 'block';
    document.getElementById('tagsRestauration').style.display = 'flex';
} else if (categorie_offre ==  'parcattraction') {
    document.getElementById('champsPA').style.display = 'block';
    document.getElementById('tagsPA').style.display = 'flex';
} else if (categorie_offre == 'spectacle') {
    document.getElementById('champsSpectacle').style.display = 'block';
    document.getElementById('tagsSpectacle').style.display = 'flex';
} else if (categorie_offre == 'activite') {
    document.getElementById('champsActivite').style.display = 'block';
    document.getElementById('tagsActivite').style.display = 'flex';
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


/* ----------------------------------------------- vérifications avant submit ----------------------------------------------- */
let formulaire = document.getElementsByTagName("form")[0];
formulaire.addEventListener("submit", verifHorairesCorrectes);

function verifHorairesCorrectes()               //lorsque l'utilisateur veut submit, vérifie la validité des horaires entrées
{
    let jour;
    let horairesJour;
    let joursHorairesVides = "";                //contiendra les jours déclarés ouverts qui n'ont pas d'horaires
    let joursHorairesInvalides = "";            //contiendra les jours dont l'horaire d'ouverture est antérieure à la date de fermeture
    let joursHorairesIncoherentes = "";         //contiendra les jours dont les 2 horaires sont imbriquées ou dans le mauvais ordre
    
    mapJoursHoraires.forEach((value, key) => {
        jour = mapJoursHoraires.get(key);
        if (mapJoursElements.get(key).classList.contains("jourOuvert")) {       //si un jour est ouvert mais n'a pas d'horaire
            horairesJour = mapJoursHoraires.get(key);
            if (horairesJour[0].length == 0 || horairesJour[1].length == 0) {
                if (joursHorairesVides.length == 0) {
                    joursHorairesVides += mapNomsJours.get(key);
                }
                else {
                    joursHorairesVides += ", " + mapNomsJours.get(key);
                }
            }
            else if (horairesJour[0] > horairesJour[1]) {                       //si la 1ère horaire est supérieure à la deuxième
                if (joursHorairesInvalides.length == 0) {
                    joursHorairesInvalides += mapNomsJours.get(key);
                }
                else {
                    joursHorairesInvalides += ", " + mapNomsJours.get(key);
                }
            }

            if (horairesJour[2].length != 0 || horairesJour[3].length != 0) {   //si le champ du 2e couple d'horaires est ouvert mais qu'au moins un champ est vide
                if (horairesJour[2].length != 0 ^ horairesJour[3].length != 0) {
                    if (!joursHorairesVides.includes(mapNomsJours.get(key))) {
                        if (joursHorairesVides.length == 0) {
                            joursHorairesVides += mapNomsJours.get(key);
                        }
                        else {
                            joursHorairesVides += ", " + mapNomsJours.get(key);
                        }
                    }
                }
                if (horairesJour[1] > horairesJour[2]) {                        //si le 2e couple d'horaire commence avant que le 1er ne se termine
                    if (joursHorairesIncoherentes.length == 0) {
                        joursHorairesIncoherentes += mapNomsJours.get(key);
                    }
                    else {
                        joursHorairesIncoherentes += ", " + mapNomsJours.get(key);
                    }
                }
            }
        }
    });

    //afficher les messages d'erreur si besoin
    if (joursHorairesVides.length != 0) {
        alert("Veuillez entrer des horaires pour " + joursHorairesVides);
        event.preventDefault();
    }
    if (joursHorairesInvalides.length != 0) {
        alert("Veuillez modifier les horaires pour que la date d'ouveture soit antérieure à la date de fermeture pour " + joursHorairesInvalides);
        event.preventDefault();
    }
    if (joursHorairesIncoherentes.length != 0) {
        alert("Les deux horaires sont incohérentes pour " + joursHorairesIncoherentes);
        event.preventDefault();
    }


    //si aucune erreur n'est détectée, envoyer une map des jours ouverts et de leurs horaires au script changeHoraireOffre.php
    if (joursHorairesVides.length == 0 && joursHorairesInvalides.length == 0 && joursHorairesIncoherentes.length == 0) {

        let i = 0;
        for(let key of mapJoursHoraires.keys())
        {
            tabInputsJour[i].value = JSON.stringify(mapJoursHoraires.get(key));     //ajoute les horaires du jour dans les champs input, converties en string avec JSON
            i++;
        }

        console.log(tabInputsJour);
    }
    else
    {
        alert("erreur dans l'envoi des horaires");
        event.preventDefault();
    }
}