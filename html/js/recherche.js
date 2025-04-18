// Fonction pour récupérer les données JSON depuis le fichier PHP
function getData() {
    var xhr = new XMLHttpRequest(); // Création d'une instance XMLHttpRequest
    xhr.open("GET", "../composants/ajax/listeTagsOffres.php", false); // false rend la requête synchrone
    xhr.setRequestHeader("Content-Type", "application/json"); // Définition de l'en-tête Content-Type
    xhr.send(); // Envoi de la requête
    
    if (xhr.status == 200) {
        // Si la requête réussit (status 200), on retourne les données JSON
        return JSON.parse(xhr.responseText);
    } else {
        console.error("Erreur lors de la récupération des données");
        return [];
    }
}

// Fonction pour transformer les données en dictionnaire avec les nomtag associés à chaque idoffre
function transformDataToDictionary(data) {
    var dictionary = {}; // Initialisation du dictionnaire vide

    // On parcourt chaque élément du tableau
    data.forEach(function(item) {
        // Si la clé 'idoffre' n'existe pas encore, on la crée
        if (!dictionary[item.idoffre]) {
        dictionary[item.idoffre] = [];
        }
        
        // On ajoute le 'nomtag' à la liste associée à 'idoffre'
        // On ajoute seulement si ce nomtag n'est pas déjà présent dans la liste
        if (!dictionary[item.idoffre].includes(item.nomtag)) {
        dictionary[item.idoffre].push(item.nomtag);
        }
    });

    return dictionary;
}


function initOffres()
/*renvoie la map mapOffreInfos : sa clé est un id offre et sa valeur est une map dont la clé est un string du nom d'une information de l'offre et
la valeur de l'information (exemple : "titre" => "Fort la Latte", "prix" => 15)*/
{
    let elementsOffres = document.querySelectorAll(".lienApercuOffre");

    let mapOffresInfos = new Map();

    elementsOffres.forEach(element => {

        let mapTempo = new Map();                       //map temporaire qui stock les informations de l'élément

        mapTempo.set("id", element.id);                 //l'id de l'offre
        mapTempo.set("visibilite", true);               //indique si l'élément doit être montré par la recherche
        mapTempo.set("element", element);               //l'élément dans le DOM
        mapTempo.set("titre", document.querySelectorAll("#" + element.id + " .apercuOffre h3")[0].textContent);     //titre de l'offre
        mapTempo.set("categorie", document.querySelector("#" + element.id + " .cat").textContent);
        mapTempo.set("ville", document.querySelector("#" + element.id + " .ville").textContent);
        mapTempo.set("adresse", document.querySelector("#" + element.id + " .adresse").textContent);
        mapTempo.set("note", parseFloat(document.querySelector("#" + element.id + " .note").textContent));
        // mapTempo.set("date", document.querySelector("#" + element.id + " #date").textContent);

        // Récupérer les données des tags
        let data = getData();

        // Transformer les données des tags en dictionnaire
        let dicTag = transformDataToDictionary(data);


        if (mapTempo.get("id").substring(5) in dicTag){
            mapTempo.set("tags", dicTag[mapTempo.get("id").substring(5)]);
        } else {
            mapTempo.set("tags", []);
        }
        
        let prix = document.querySelectorAll("#" + element.id + " .text-overlay span")[0].textContent;
        prix = prix.substring(0, prix.length-1);
        mapTempo.set("prix", parseInt(prix));   //prix

        let ouv = document.querySelector("#" + element.id + " .ouvertFerme");
        mapTempo.set("ouverture", ouv.classList.contains("ouvert"));        //si l'offre est ouverte ou non
        mapTempo.set("image",document.querySelector("#" + element.id + " .conteneurImage img"));

        mapOffresInfos.set(element.id, mapTempo);
    });
    return mapOffresInfos;
}


function updateAffichageOffres()
/*masque les offres qui doivent être affichées par la recherche et les filtrer, masque les autres*/
{
    mapOffresInfos.forEach((map, key, value)=>{
        if((verifFiltre(key)) && (verifTags(key)))
        {
            mapOffresInfos.get(key).get("element").classList.remove("displayNone");
            mapOffresInfos.get(key).set("visibilite",true);
        }
        else
        {
            mapOffresInfos.get(key).get("element").classList.add("displayNone");
            mapOffresInfos.get(key).set("visibilite",false);
        }
    });
    updateMap();
}

let mapOffresInfos = initOffres();

// ============================ RECHERCHER ===========================


let barreRecherche = document.getElementById("searchbar");      //récupère la barre de recherche dans le DOM
barreRecherche.addEventListener("keyup", rechercher);           //déclenche la fonction rechercher quand une touche est appuyée (quand qqn écrit dans la barre)


function rechercher()
/*repère les offres dont le titre contient le texte écrit dans la barre de recherhe, et les affiche. N'affiche pas les autres*/
{
    let texte = barreRecherche.value.toLowerCase();             //récupère le texte de la barre de recherche et le converti en minuscule
    
    mapOffresInfos.forEach((map, key, value)=>{
        if(texte.length >= 1)                                   //si la barre de recherche n'est pas vide
        {

            if(mapOffresInfos.get(key).get("titre").toLowerCase().includes(texte))      //si le titre de l'offre contient le texte de la barre
            {
                mapOffresInfos.get(key).set("visibilite", true);
                mapOffresInfos.get(key).get("element").classList.remove("displayNone");
            }
            else
            {
                mapOffresInfos.get(key).set("visibilite", false);
                mapOffresInfos.get(key).get("element").classList.add("displayNone");
            }
        }
        else                                                    //si la barre est vide, affiche toutes les offres
        {
            mapOffresInfos.get(key).set("visibilite", true);
            mapOffresInfos.get(key).get("element").classList.remove("displayNone");
        }
    });
    
    // updateAffichageOffres();                                    //met à jour l'affichage des offres
    updateMap();
}


// ================== FILTRER ========================


// VERIFIE SI UNE OFFRE CORRESPOND AUX CRITERES

let critCategorie = [];
let critOuverture = [];
let critLieu = "";

// ============================ FILTRE PAR CATEGORIE ===========================

// liste des cases de catégories cochées
document.querySelectorAll('#categorie input[type="checkbox"]').forEach((cat) => {
    cat.addEventListener('change', listeCategorie);
});

function listeCategorie(event) {

    let value = event.target.value;

    if (event.target.checked) {
        // Si la case est cochée, on ajoute sa valeur au tableau
        if (!critCategorie.includes(value)) {
            critCategorie.push(value);
        }
    } else {
        // Si la case est décochée, on enlève sa valeur du tableau
        let index = critCategorie.indexOf(value);
        if (index !== -1) {
            critCategorie.splice(index, 1);
        }
    }
    updateAffichageOffres();
}

// ============================ FILTRE OUVERT FERME ===========================

// liste des cases d'ouverture cochées
document.querySelectorAll('#ouverture input[type="checkbox"]').forEach((ouv) => {
    ouv.addEventListener('change', listeOuverture);
});

function listeOuverture(event) {

    let value = event.target.value;

    if (event.target.checked) {
        // Si la case est cochée, on ajoute sa valeur au tableau
        if (!critOuverture.includes(value)) {
            critOuverture.push(value);
        }
    } else {
        // Si la case est décochée, on enlève sa valeur du tableau
        let index = critOuverture.indexOf(value);
        if (index !== -1) {
            critOuverture.splice(index, 1);
        }
    }
    updateAffichageOffres();
}

// ============================ RECHERCHE LIEU ===========================

// lieu rentrée en paramètre
document.getElementById("lieu").addEventListener("keyup", (event) => {

    critLieu = event.target.value;
    updateAffichageOffres();
});

// ========================= VERIFICATION FILTRES ========================

// Fonction qui retourne true si une offre correspond au critères sélectionnés
function verifFiltre(idOffre)
{
    let valide = false;

    let valideCat = false;
    let valideLieu = false;
    let valideOuv = false;
    // let valideDate = false;
    let validePrix = false;

    let offre = mapOffresInfos.get(idOffre);

    // Si aucun critère n'a été sélectionné
    if ((critCategorie.length == 0) && (critOuverture.length == 0) && (critLieu == "")){
        valide = true;
    // Si au moins 1 critère a été sélectionné
    } else {

        // On traite le cas du filtre catégorie
        if (critCategorie.length > 0) {
            if (critCategorie.includes(offre.get("categorie"))){
                valideCat = true;
            }
        } else {
            valideCat = true;
        }

        // On traite le cas du filtre lieu
        if (critLieu != "") {
            if (offre.get("ville").toLowerCase().includes(critLieu.toLowerCase())) {
                valideLieu = true;
            }
        } else {
            valideLieu = true;
        }

        // On traite le cas du filtre d'ouverture
        if (critOuverture.length > 0) {
            if ((critOuverture.includes("ouvert")) && (offre.get("ouverture"))){
                valideOuv = true;
            }
            if ((critOuverture.includes("ferme")) && (!offre.get("ouverture"))){
                valideOuv = true;
            }
        } else {
            valideOuv = true;
        }

        // Si l'offre correspond à tout les critères, elle peut être affichées
        if ((valideCat) && (valideLieu) && (valideOuv)) {
            valide = true;
        }
        
    }
    return valide;
}



// ================== FONCTIONS FILTRE PAR TAGS ========================

let listeTags = [];

// liste des cases de tags cochées
document.querySelectorAll('#fieldsetTag input[type="checkbox"]').forEach((tag) => {
    tag.addEventListener('change', (event) => {

        let value = event.target.value;

        if (event.target.checked) {
            // Si la case est cochée, on ajoute sa valeur au tableau
            if (!listeTags.includes(value)) {
                listeTags.push(value);
            }
        } else {
            // Si la case est décochée, on enlève sa valeur du tableau
            let index = listeTags.indexOf(value);
            if (index !== -1) {
                listeTags.splice(index, 1);
            }
        }

        updateAffichageOffres();
        updateMap();
    });
});


// Fonction qui vérifie si une offre correspond au filtre des tags
function verifTags(idOffre){

    let offre = mapOffresInfos.get(idOffre);
    let valideTag = false;

    // Si aucun critère n'a été sélectionné
    if (listeTags.length == 0){
        valideTag = true;
    // Si au moins 1 critère a été sélectionné
    } else {
        if (listeTags.some(tag => offre.get("tags").includes(tag))){
            valideTag = true;
        }
    }

    return valideTag;

}


// ================== FONCTIONS TRIES PRIX ========================

console.log(mapOffresInfos);

function toogleTrie(paramTrie,icone1,icone2,idBouton,sens){

    // Rajoute une bordure bleu sur le bouton
    document.getElementById(icone1).classList.toggle("displayNone");
    document.getElementById(icone2).classList.toggle("displayNone");

    if (sens == "asc") {
        // Trie le Tableau mapOffresInfos dans l'ordre croissant dans le tableau mapTrieAcs
        var mapTrie = new Map([...mapOffresInfos.entries()].sort((a,b) => a[1].get(paramTrie) - b[1].get(paramTrie)));

        document.getElementById(idBouton).style.border = "solid";
        document.getElementById(idBouton).style.borderWidth = "1px";
        document.getElementById(idBouton).style.borderColor = "blue" ;
    }
    if (sens == "decs") {
        // Trie le Tableau mapOffresInfos dans l'ordre décroissant dans le tableau mapTrieDesc
        var mapTrie = new Map([...mapOffresInfos.entries()].sort((a,b) => b[1].get(paramTrie) - a[1].get(paramTrie)));

        document.getElementById(idBouton).style.border = "solid";
        document.getElementById(idBouton).style.borderWidth = "1px";
        document.getElementById(idBouton).style.borderColor = "red" ;
    }
    if (sens == "default") {
        var mapTrie = mapOffresInfos;
        document.getElementById(idBouton).style.border = "none";
    }
    
    let index = 0;
    // Parcour le tableau mapTrieAcs pour ajouter un attribut order dans le style des élément
    mapTrie.forEach((map, key, value)=>{

        // Récupère l'élément dans la page
        let elem = document.getElementById(mapTrie.get(key).get("id"));
        elem.style.order = index;   // Rajoute l'attribut css order égal à sa position dans le tableau 

        index++;
    })
}

function clearBouton(icone,icone1,icone2,idBouton){
    if (document.getElementById(icone).classList.contains("displayNone")) {
        document.getElementById(icone).classList.toggle("displayNone");
    }
    if (document.getElementById(icone1).classList.contains("displayNone")==false) {
        document.getElementById(icone1).classList.toggle("displayNone");
    }
    if (document.getElementById(icone2).classList.contains("displayNone")==false) {
        document.getElementById(icone2).classList.toggle("displayNone");
    }
    document.getElementById(idBouton).style.border = "none";
}

triePrix="";
trieNote="";

function trierPrix() {
    clearBouton("iconeTrieNote","iconeTrieNote1","iconeTrieNote2","btnTrieNote");
    trieNote ="";
    if (triePrix == "") {
        toogleTrie("prix","iconeTriePrix1","iconeTriePrix","btnTriePrix","asc");
        triePrix = "asc";   // Modifie l'état du trie
    }
    else if(triePrix == "asc") {
        toogleTrie("prix","iconeTriePrix1","iconeTriePrix2","btnTriePrix","decs");
        triePrix = "decs";  // Modifie l'état du trie
    }
    else if(triePrix == "decs"){
        toogleTrie("prix","iconeTriePrix2","iconeTriePrix","btnTriePrix","default");
        triePrix = "";  // Modifie l'état du trie
    }
    updateAffichageOffres();
}

function trierNote() {
    clearBouton("iconeTriePrix","iconeTriePrix1","iconeTriePrix2","btnTriePrix");
    triePrix="";
    if (trieNote == "") {
        toogleTrie("note","iconeTrieNote1","iconeTrieNote","btnTrieNote","asc");
        trieNote = "asc";   // Modifie l'état du trie
    }
    else if(trieNote == "asc") {
        toogleTrie("note","iconeTrieNote1","iconeTrieNote2","btnTrieNote","decs");
        trieNote = "decs";  // Modifie l'état du trie
    }
    else if(trieNote == "decs"){
        toogleTrie("note","iconeTrieNote2","iconeTrieNote","btnTrieNote","default");
        trieNote = "";  // Modifie l'état du trie
    }
    updateAffichageOffres();
}

//========================================= DOUBLE SLIDER POUR NOTE ===================================================

let ecartMinimumNote = 0;   // écart minimum entre les deux curseurs
const rangeNote = document.getElementById("range-barNote");   // la bar entre les deux curseurs
const minvalNote = document.querySelector(".minvalueNote");     // la bulle avec la petite valeur
const maxvalNote = document.querySelector(".maxvalueNote");     // la bulle avec la grande valeur
const rangeInputNote = document.querySelectorAll(".inputNote");     // les deux sliders pour prix

let minRangeNote, maxRangeNote, pourcentageMinNote, pourcentageMaxNote;

function minRangeFillNote () {
    rangeNote.style.left = (rangeInputNote[0].value / rangeInputNote[0].max) * 100 + "%";   // détermine la taille de la bar range du coté gauche 
  }

function maxRangeFillNote () {
    rangeNote.style.right = 100 - (rangeInputNote[1].value / rangeInputNote[1].max) * 100 + "%";  // détermine la taille de la bar range du coté droit 
  }

function MinVlaueBubbleStyleNote () {   // détermine la marge necessaire à gauche pour bouger la bulle en même temps que le curseur 
    pourcentageMinNote = (minRangeNote / rangeInputNote[0].max) * 100;
    minvalNote.style.left = pourcentageMinNote + "%";
    minvalNote.style.transform = `translate(-${pourcentageMinNote / 2}%, -100%)`;
  }

function MaxVlaueBubbleStyleNote () {   // détermine la marge necessaire à droite pour bouger l'autre bulle en même temps que le curseur 
    pourcentageMaxNote = 100 - (maxRangeNote / rangeInputNote[1].max) * 100;
    maxvalNote.style.right = pourcentageMaxNote + "%";
    maxvalNote.style.transform = `translate(${pourcentageMaxNote / 2}%, 100%)`;
  }
  
function setMinValueOutputNote () {     // remplie la bulle de droite
    minRangeNote = parseInt(rangeInputNote[0].value);
    minvalNote.innerHTML = rangeInputNote[0].value;
    minvalNote.innerHTML += '<img src="/icones/etoilePleineSVG.svg" alt="icone étoile" >';
  }

function setMaxValueOutputNote () {     // remplie la bulle de gauche
    maxRangeNote = parseInt(rangeInputNote[1].value);
    maxvalNote.innerHTML = rangeInputNote[1].value;
    maxvalNote.innerHTML += '<img src="/icones/etoilePleineSVG.svg" alt="icone étoile" >';
  }

  setMinValueOutputNote()
  setMaxValueOutputNote()
  minRangeFillNote()
  maxRangeFillNote()
  MinVlaueBubbleStyleNote()
  MaxVlaueBubbleStyleNote()

rangeInputNote.forEach((input) => {
    input.addEventListener("input", (e) => {
        
        setMinValueOutputNote();
        setMaxValueOutputNote();

        minRangeFillNote();
        maxRangeFillNote();

        MinVlaueBubbleStyleNote();
        MaxVlaueBubbleStyleNote();

        if (maxRangeNote - minRangeNote <= ecartMinimumNote) {  // si l'écart entre les deux n'est pas supérieur à l'écart maximum définit
            if (e.target.className === "minNote") {     
                rangeInputNote[0].value = maxRangeNote - ecartMinimumNote;  // on définit la petite valeur
                setMinValueOutputNote();
                minRangeFillNote();
                MinVlaueBubbleStyleNote();
            }
            else {
                rangeInputNote[1].value = minRangeNote + ecartMinimumNote;  // on définit la grande valeur
                setMaxValueOutputNote();
                maxRangeFillNote();
                MaxVlaueBubbleStyleNote();
            }
        }

        if (rangeInputNote[0].value == 5) {     // si le curseur de gauche est totalement à droite
            rangeInputNote[0].style.zIndex = "2";   // on le met plus en avant que l'autre
            rangeInputNote[1].style.zIndex = "1";
        }
        if (rangeInputNote[1].value == 0) {     // on fait l'inverse d'au dessus
            rangeInputNote[1].style.zIndex = "2";
            rangeInputNote[0].style.zIndex = "1";
        }


        mapOffresInfos.forEach((map, key, value)=>{
                // si la note de l'offre est dans l'interval de la slide bar
                if(mapOffresInfos.get(key).get("note") >=rangeInputNote[0].value && mapOffresInfos.get(key).get("note")<=rangeInputNote[1].value)
                {
                    // si ceux qui doivent être affiché ne le sont pas on les affiches
                    if (document.getElementById(key).classList.contains("displayNone")) { 
                        document.getElementById(key).classList.toggle("displayNone");
                        mapOffresInfos.get(key).set("visibilite",true);
                    }
                }
                else
                {
                    // si ceux qui ne doivent pas être affiché le sont on les caches
                    if (!document.getElementById(key).classList.contains("displayNone")) {
                        document.getElementById(key).classList.toggle("displayNone");
                        mapOffresInfos.get(key).set("visibilite",false);
                    }
                }
        });
        updateMap();
    });
});

//============================================================================================

//========================================= DOUBLE SLIDER POUR PRIX ===================================================

// le prix le plus grand parmis toutes les offres disponibles
const maxPrix = new Map([...mapOffresInfos.entries()].sort((a,b) => b[1].get("prix") - a[1].get("prix"))).entries().next().value[1].get("prix");

// ajout dans le HTML des deux input avec le prix maximum
document.querySelector(".input-box").innerHTML = `<input type="range" class="inputPrix minPrix" min="0" max="${maxPrix}" value="0" step="0" />`;
document.querySelector(".input-box").innerHTML += `<input type="range" class="inputPrix maxPrix" min="0" max="${maxPrix}" value="${maxPrix}" step="0" />`;

let ecartMinimumPrix = 0;   // écart minimum entre les deux curseurs
const rangePrix = document.getElementById("range-barPrix");   // la bar entre les deux curseurs
const minvalPrix = document.querySelector(".minvaluePrix");     // la bulle avec la petite valeur
const maxvalPrix = document.querySelector(".maxvaluePrix");     // la bulle avec la grande valeur
const rangeInputPrix = document.querySelectorAll(".inputPrix");     // les deux sliders pour prix

let minRangePrix, maxRangePrix, pourcentageMinPrix, pourcentageMaxPrix;

function minRangeFillPrix () {
    rangePrix.style.left = (rangeInputPrix[0].value / rangeInputPrix[0].max) * 100 + "%";   // détermine la taille de la bar range du coté gauche 
  }

function maxRangeFillPrix () {
    rangePrix.style.right = 100 - (rangeInputPrix[1].value / rangeInputPrix[1].max) * 100 + "%";  // détermine la taille de la bar range du coté droit 
  }

function MinVlaueBubbleStylePrix () {   // détermine la marge necessaire à gauche pour bouger la bulle en même temps que le curseur 
    pourcentageMinPrix = (minRangePrix / rangeInputPrix[0].max) * 100;
    minvalPrix.style.left = pourcentageMinPrix + "%";
    minvalPrix.style.transform = `translate(-${pourcentageMinPrix / 2}%, -100%)`;
  }

function MaxVlaueBubbleStylePrix () {   // détermine la marge necessaire à droite pour bouger l'autre bulle en même temps que le curseur 
    pourcentageMaxPrix = 100 - (maxRangePrix / rangeInputPrix[1].max) * 100;
    maxvalPrix.style.right = pourcentageMaxPrix + "%";
    maxvalPrix.style.transform = `translate(${pourcentageMaxPrix / 2}%, 100%)`;
  }
  
function setMinValueOutputPrix () {     // remplie la bulle de droite
    minRangePrix = parseInt(rangeInputPrix[0].value);
    minvalPrix.innerHTML = `${rangeInputPrix[0].value}€`;
  }

function setMaxValueOutputPrix () {     // remplie la bulle de gauche
    maxRangePrix = parseInt(rangeInputPrix[1].value);
    maxvalPrix.innerHTML = `${rangeInputPrix[1].value}€`;
  }

  setMinValueOutputPrix()
  setMaxValueOutputPrix()
  minRangeFillPrix()
  maxRangeFillPrix()
  MinVlaueBubbleStylePrix()
  MaxVlaueBubbleStylePrix()

rangeInputPrix.forEach((input) => {
    input.addEventListener("input", (e) => {
        
        setMinValueOutputPrix();
        setMaxValueOutputPrix();

        minRangeFillPrix();
        maxRangeFillPrix();

        MinVlaueBubbleStylePrix();
        MaxVlaueBubbleStylePrix();

        if (maxRangePrix - minRangePrix <= ecartMinimumPrix) {  // si l'écart entre les deux n'est pas supérieur à l'écart maximum définit
            if (e.target.className === "minPrix") {     
                rangeInputPrix[0].value = maxRangePrix - ecartMinimumPrix;  // on définit la petite valeur
                setMinValueOutputPrix();
                minRangeFillPrix();
                MinVlaueBubbleStylePrix();
            }
            else {
                rangeInputPrix[1].value = minRangePrix + ecartMinimumPrix;  // on définit la grande valeur
                setMaxValueOutputPrix();
                maxRangeFillPrix();
                MaxVlaueBubbleStylePrix();
            }
        }

        if (rangeInputPrix[0].value == maxPrix) {     // si le curseur de gauche est totalement à droite
            rangeInputPrix[0].style.zIndex = "2";   // on le met plus en avant que l'autre
            rangeInputPrix[1].style.zIndex = "1";
        }
        if (rangeInputPrix[1].value == 0) {     // on fait l'inverse d'au dessus
            rangeInputPrix[1].style.zIndex = "2";
            rangeInputPrix[0].style.zIndex = "1";
        }


        mapOffresInfos.forEach((map, key, value)=>{
                // si le prix de l'offre est dans l'interval de la slide bar
                if(mapOffresInfos.get(key).get("prix") >=rangeInputPrix[0].value && mapOffresInfos.get(key).get("prix")<=rangeInputPrix[1].value)
                {
                    // si ceux qui doivent être affiché ne le sont pas on les affiches
                    if (document.getElementById(key).classList.contains("displayNone")) { 
                        document.getElementById(key).classList.toggle("displayNone");
                        mapOffresInfos.get(key).set("visibilite",true);
                    }
                }
                else
                {
                    // si ceux qui ne doivent pas être affiché le sont on les caches
                    if (!document.getElementById(key).classList.contains("displayNone")) {
                        document.getElementById(key).classList.toggle("displayNone");
                        mapOffresInfos.get(key).set("visibilite",false);
                    }
                }
        });
        updateMap();
    });
});

//============================================================================================
window.addEventListener("pageshow", function() {
    document.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
        checkbox.checked = checkbox.defaultChecked; // Restaure l'état initial défini dans le HTML
    });


    document.querySelectorAll('input[type="number"]').forEach((input) => {
        input.value = '';
    });

    document.querySelectorAll('input[type="text"]').forEach((input) => {
        input.value = '';
    });
    
    barreRecherche.value = '';
});


document.getElementById('bn-sidebar-exit').addEventListener("click", function() {
    document.getElementById('filtres-aside').classList.add('displayNone');
    document.getElementById('menu-aside').classList.remove('displayNone');
    document.querySelector('main').classList.remove('main-for-menu-opened');
});

document.getElementById('bn-sidebar-filtres').addEventListener("click", function() {
    document.getElementById('menu-aside').classList.add('displayNone');
    document.getElementById('filtres-aside').classList.remove('displayNone');
    document.querySelector('main').classList.add('main-for-menu-opened');
});