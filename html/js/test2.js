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

let lundi = document.getElementById("btnL");        //pour des tests


//récupération des éléments nécéssaires pour les horaires
let champJours1 = document.getElementById("heures1");

let heureDebut1 = document.querySelector("#heures1 #heure-debut");
let heureFin1 = document.querySelector("#heures1 #heure-fin");

let nomJour1 = document.getElementById("nomJour1");

// variable qui contiendra l'id du bouton du jour séléctionné
let jourSelectionne;


//création et initialisation d'une map associant à chaque id d'un bouton jour les horaires qui lui sont associées
const mapJoursHoraires = new Map();
["btnL", "btnMa", "btnMe", "btnJ", "btnV", "btnS", "btnD"].forEach(jour => {
    mapJoursHoraires.set(jour, ["", ""]);
});

//appelle la fonction jourClique quand l'utilisateur clique sur un bouton jour
mapJoursElements.forEach((value, key, map)=>{
    mapJoursElements.get(key).addEventListener("click", jourClique);
})

heureDebut1.addEventListener("keyup", ()=>{horaireEntree(heureDebut1)});
heureFin1.addEventListener("keyup", ()=>{horaireEntree(heureFin1)});


function jourClique()
{
    element = event.currentTarget;
    element.classList.toggle("jourOuvert");

    if(element.classList.contains("jourOuvert"))
    {
        champJours1.classList.add("horairesVisibles");
        nomJour1.innerText = mapNomsJours.get(element.id);

        jourSelectionne = element.id;
        heureDebut1.value = mapJoursHoraires.get(jourSelectionne)[0];
        heureFin1.value = mapJoursHoraires.get(jourSelectionne)[1];
    }
    else
    {
        champJours1.classList.remove("horairesVisibles");
    }
}


function horaireEntree(element)
{
    if(element == heureDebut1)
    {
        mapJoursHoraires.set(jourSelectionne, [heureDebut1.value, mapJoursHoraires.get(jourSelectionne)[1]]);
    }
    else if(element == heureFin1)
    {
        mapJoursHoraires.set(jourSelectionne, [mapJoursHoraires.get(jourSelectionne)[0], heureFin1.value]);
    }

    console.log(mapJoursHoraires);
}