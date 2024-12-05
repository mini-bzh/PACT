/* -------------------------------- partie catégories  -------------------------------- */

if (categorie_offre == 'visite') {
    document.getElementById('champsVisite').style.display = 'block';
    document.getElementById('tagsVisite').style.display = 'block';
} else if (categorie_offre == 'restauration') {
    document.getElementById('champsRestauration').style.display = 'block';
    document.getElementById('tagsRestauration').style.display = 'block';
} else if (categorie_offre ==  'parcattraction') {
    document.getElementById('champsPA').style.display = 'block';
    document.getElementById('tagsPA').style.display = 'block';
} else if (categorie_offre == 'spectacle') {
    document.getElementById('champsSpectacle').style.display = 'block';
    document.getElementById('tagsSpectacle').style.display = 'block';
} else if (categorie_offre == 'activite') {
    document.getElementById('champsActivite').style.display = 'block';
    document.getElementById('tagsActivite').style.display = 'block';
}

/* --------------------------------- partie horaires  --------------------------------- */


//création et initialisation d'une map associant à chaque id d'un bouton jour l'élément du DOM correspondant
const mapJoursElements = new Map();
["btnL", "btnMa", "btnMe", "btnJ", "btnV", "btnS", "btnD"].forEach(btnJour => {
    mapJoursElements.set(btnJour, document.getElementById(btnJour));
});

//création et initialisation d'une map associant à chaque id d'un bouton jour le nom complet du jour
const mapNomsJours = new Map([
    ["btnL", "lundi"],
    ["btnMa", "mardi"],
    ["btnMe", "mercredi"],
    ["btnJ", "jeudi"],
    ["btnV", "vendredi"],
    ["btnS", "samedi"],
    ["btnD", "dimanche"]
]);

function getKeyByValue(map, value)          //retourne la clé associée à la valeur value si trouvée, undefined sinon
{
    for (const [key, val] of map.entries()) {
        if (val === value) {
            return key; // Retourne la clé correspondante
        }
    }
    return undefined; // Retourne undefined si aucune clé ne correspond
}


//récupération des éléments nécéssaires pour les horaires
let champJours1 = document.getElementById("heures1");
let champJours2 = document.getElementById("heures2");


let btnAjoutHoraire = document.getElementById("btnAjoutHoraire");   

let heureDebut1 = document.querySelector("#heures1 .heure-debut");
let heureFin1 = document.querySelector("#heures1 .heure-fin");

let heureDebut2 = document.querySelector("#heures2 .heure-debut");
let heureFin2 = document.querySelector("#heures2 .heure-fin");

let nomJour1 = document.getElementById("nomJour1");

// variable qui contiendra l'id du bouton du jour séléctionné
let jourSelectionne;

let tabInputsJour = document.getElementsByClassName("inputJour");       //récupère les input cachés dans le formulaire qui contiendront les horaires des jours



//création et initialisation d'une map associant à chaque id d'un bouton jour les horaires qui lui sont associées
const mapJoursHoraires = new Map();
["btnL", "btnMa", "btnMe", "btnJ", "btnV", "btnS", "btnD"].forEach(jour => {
    mapJoursHoraires.set(jour, ["", "", "", ""]);
});

//ajouter données déjà présentes dans la bdd dans mapJoursHoraires
let element;
let idBtnEmement;

for (let index = 0; index < tabInputsJour.length; index++) 
{
    element = tabInputsJour[index];
    if(element.value != "")
    {
        mapJoursHoraires.set(getKeyByValue(mapNomsJours, element.name), JSON.parse(element.value));
    }
}

//colorer les boutons des jours déjà ouverts
mapJoursElements.forEach((map, key, value)=>{
    if(mapJoursHoraires.get(key)[0].length != 0)
    {
        mapJoursElements.get(key).classList.add("jourOuvert");
    }
});


//appelle la fonction jourClique quand l'utilisateur clique sur un bouton jour
mapJoursElements.forEach((value, key, map) => {
    mapJoursElements.get(key).addEventListener("click", jourClique);
})

heureDebut1.addEventListener("keyup", () => { horaireEntree(heureDebut1)});
heureFin1.addEventListener("keyup", () => { horaireEntree(heureFin1) });

heureDebut2.addEventListener("keyup", () => { horaireEntree(heureDebut2) });
heureFin2.addEventListener("keyup", () => { horaireEntree(heureFin2) });


function aDeuxHoraires(idJour) {
    let horairesjour = mapJoursHoraires.get(idJour);
    return horairesjour[2].length != 0 && horairesjour[3].length != 0;
}

function jourClique() {
    element = event.currentTarget;
    element.classList.toggle("jourOuvert");

    jourSelectionne = element.id;


    if (element.classList.contains("jourOuvert")) {
        champJours1.classList.add("horairesVisibles");
        nomJour1.innerText = mapNomsJours.get(element.id);

        heureDebut1.value = mapJoursHoraires.get(jourSelectionne)[0];
        heureFin1.value = mapJoursHoraires.get(jourSelectionne)[1];
        heureDebut2.value = mapJoursHoraires.get(jourSelectionne)[2];
        heureFin2.value = mapJoursHoraires.get(jourSelectionne)[3];

        if (aDeuxHoraires(jourSelectionne)) {
            champJours2.classList.add("horairesVisibles");
            btnAjoutHoraire.textContent = "-"
        }
        else {
            champJours2.classList.remove("horairesVisibles");
            btnAjoutHoraire.textContent = "+";
        }
    }
    else {
        champJours1.classList.remove("horairesVisibles");
        champJours2.classList.remove("horairesVisibles");
    }
}


function horaireEntree(element)                 //met à jour la map mapJoursHoraires
{
    let horairesJour;
    horairesJour = mapJoursHoraires.get(jourSelectionne);

    if (element == heureDebut1) {
        horairesJour[0] = heureDebut1.value;

    }
    else if (element == heureFin1) {
        horairesJour[1] = heureFin1.value;

    }
    else if (element == heureDebut2) {
        horairesJour[2] = heureDebut2.value;

    }
    else if (element == heureFin2) {
        horairesJour[3] = heureFin2.value;
    }
    mapJoursHoraires.set(jourSelectionne, horairesJour);

    console.log(mapJoursHoraires);
}

/* ----------------------------------------------- ajout 2e horaire ----------------------------------------------- */

btnAjoutHoraire.addEventListener("click", toggleHoraire2);


function toggleHoraire2()                       //toggle l'affichage des champs pour ajouter un 2e couple d'horaires
{
    champJours2.classList.toggle("horairesVisibles");
    if (btnAjoutHoraire.textContent == "+") {
        btnAjoutHoraire.textContent = "-";
    }
    else {
        btnAjoutHoraire.textContent = "+";
        heureDebut2.value = "";
        heureFin2.value = "";

        mapJoursHoraires.set(jourSelectionne, [mapJoursHoraires.get()])
    }
}


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