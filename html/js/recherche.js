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
        mapTempo.set("categorie", document.querySelector("#" + element.id + " #cat").textContent)
        mapTempo.set("ville", document.querySelector("#" + element.id + " #ville").textContent)
        mapTempo.set("date", document.querySelector("#" + element.id + " #ville").textContent)

        let prix = document.querySelectorAll("#" + element.id + " .text-overlay span")[0].textContent;
        prix = prix.substring(0, prix.length-1);
        mapTempo.set("prix", parseInt(prix));   //prix

        let ouv = document.querySelector("#" + element.id + " #ouvertFerme");
        mapTempo.set("ouverture", ouv.classList.contains("ouvert"));        //si l'offre est ouverte ou non

        mapOffresInfos.set(element.id, mapTempo);
    });
    return mapOffresInfos;
}


function updateAffichageOffres()
/*masque les offres qui doivent être affichées par la recherche et les filtrer, masque les autres*/
{
    mapOffresInfos.forEach((map, key, value)=>{
        if((mapOffresInfos.get(key).get("visibilite")) && (verifFiltre(key)))
        {
            mapOffresInfos.get(key).get("element").classList.remove("displayNone");
        }
        else
        {
            mapOffresInfos.get(key).get("element").classList.add("displayNone");
        }
    });
}

function verifConpatibilite(idOffre)
{
    let tab = mapOffresInfos.get(idOffre).get("visibilite");

    let i = 0;
    compatible = true;

    while((compatible) && (i < tab.length))
    {
        if(tab["i"] == "recherche")
        {
            let verifRecherche;
            let texte = barreRecherche.value.toLowerCase();
            verifRecherche = mapOffresInfos.get(idOffre).get("titre").toLowerCase().includes(texte);

            compatible = compatible && verifRecherche;
        }



        i++;
    }


    return compatible;
}

function retireElement(array, valeur)
{
    let i = 0;
    let trouve = false;

    while((i < array.length) && (!trouve))
    {
        if(array[i] == valeur)
        {
            array.splice(i, 1);
            trouve = true;
        }

        i++;
    }

    return array;
}



let mapOffresInfos = initOffres();



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
            }
            else
            {
                mapOffresInfos.get(key).set("visibilite", false);
            }
        }
        else                                                    //si la barre est vide, affiche toutes les offres
        {
            mapOffresInfos.get(key).set("visibilite", true);
        }
    });
    
    updateAffichageOffres();                                    //met à jour l'affichage des offres
}


// ================== FILTRER ========================

let dateInput1 = document.querySelector("#dateDeb");
let dateInput2 = document.querySelector("#dateFin");
let calendarIcon1 = document.querySelector("#dateDeb + svg");
let calendarIcon2 = document.querySelector("#dateFin + svg");

calendarIcon1.addEventListener("click", () => {
    dateInput1.showPicker();
});

calendarIcon2.addEventListener("click", () => {
    dateInput2.showPicker();
});

function adjustValue(increment, prix) {
    // Trouver l'élément 'input' associé au bouton cliqué via l'ID
    let inputElement = document.getElementById(prix);

    let min = document.getElementById("prixMin").value;
    let max = document.getElementById("prixMax").value;
    
    // Récupérer la valeur actuelle et la convertir en nombre entier
    let currentValue = parseInt(inputElement.value, 10) || 0;

    // Si currentValue est NaN ou null, définir à 0
    if (isNaN(currentValue) || currentValue == "") {
        if ((prix == "prixMax") && (min != "")) {
            currentValue = parseInt(min, 10);
        } else {
            currentValue = 0;
        }
    }
    
    // Ajuster la valeur en fonction de l'incrément
    if ((prix == "prixMin") && ((currentValue + increment <= max) || (max == "") || (isNaN(max)))) {
        currentValue += increment;
        critPrixMin = parseInt(currentValue, 10);
    } else if ((prix == "prixMax") && ((currentValue + increment >= min) || (min == ""))) {
        currentValue += increment;
        critPrixMax = parseInt(currentValue, 10);
    }
    
    // Empêcher les valeurs négatives
    inputElement.value = Math.max(0, currentValue);


    updateAffichageOffres();
}


// Récupérer les éléments des champs de date
let dateDeb = document.getElementById('dateDeb');
let dateFin = document.getElementById('dateFin');

// Fonction pour ajuster les dates
function adjustDates(event) {
    let minDate = new Date(dateDeb.value);
    let maxDate = new Date(dateFin.value);

    // Si la date de fin est antérieure à la date de début, ajuster la date de fin
    if ((maxDate < minDate) && (event.target == dateFin)) {
        dateFin.value = dateDeb.value; // Réinitialiser la date de fin pour correspondre à la date de début
    }

    // Si la date de début est postérieure à la date de fin, ajuster la date de début
    if ((minDate > maxDate) && (event.target == dateDeb)) {
        dateDeb.value = dateFin.value; // Réinitialiser la date de début pour correspondre à la date de fin
    }
    updateAffichageOffres();
}

// Ajouter des écouteurs d'événements pour les changements de valeur
dateDeb.addEventListener('change', adjustDates);
dateFin.addEventListener('change', adjustDates);


let etoileMin = document.getElementById('etoileMin');
let etoileMax = document.getElementById('etoileMax');

// Fonction pour ajuster les valeurs possibles du filtre des étoiles
function adjustOptions(event) {
    let min = parseInt(etoileMin.value, 10);
    let max = parseInt(etoileMax.value, 10);

    // Empêcher que max soit inférieur à min
    if ((event.target == etoileMax) && (max < min)) {
        etoileMax.value = etoileMin.value; // Réinitialiser max pour correspondre à min
    }

    // Empêcher que min soit supérieur à max
    if ((event.target == etoileMin) && (min > max)) {
        etoileMin.value = etoileMax.value; // Réinitialiser min pour correspondre à max
    }
    updateAffichageOffres();
}

// Écouter les changements de valeur dans les deux sélecteurs
etoileMin.addEventListener('change', adjustOptions);
etoileMax.addEventListener('change', adjustOptions);



let prixMin = document.getElementById('prixMin');
let prixMax = document.getElementById('prixMax');

// Fonction pour ajuster les valeurs possibles du filtre des étoiles
function ajustePrix(event) {
    let min = parseInt(prixMin.value, 10);
    let max = parseInt(prixMax.value, 10);

    // Empêcher que max soit inférieur à min
    if (event.target == prixMax) {
        if (max < min) {
            prixMax.value = prixMin.value; // Réinitialiser max pour correspondre à min
        }
    }

    // Empêcher que min soit supérieur à max
    if (event.target == prixMin) {
        if (min > max) {
            prixMin.value = prixMax.value; // Réinitialiser min pour correspondre à max
        }
    }
    updateAffichageOffres();
}

// Écouter les changements de valeur dans les deux sélecteurs
prixMin.addEventListener('change', ajustePrix);
prixMax.addEventListener('change', ajustePrix);


// VERIFIE SI UNE OFFRE CORRESPOND AUX CRITERES

let critCategorie = [];
let critOuverture = [];
let critLieu = "";
let critDateDeb = null;
let critDateFin = null;
let critPrixMin = null;
let critPrixMax = null;

// liste des cases de catégories cochées
document.querySelectorAll('#categorie input[type="checkbox"]').forEach((cat) => {
    cat.addEventListener('change', (event) => {

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
    });
});

// liste des cases d'ouverture cochées
document.querySelectorAll('#ouverture input[type="checkbox"]').forEach((ouv) => {
    ouv.addEventListener('change', (event) => {

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
    });
});

// lieu rentrée en paramètre
document.getElementById("lieu").addEventListener("keyup", (event) => {

    critLieu = event.target.value;
    updateAffichageOffres();

});

// date de début / fin entrée
document.getElementById("dateDeb").addEventListener("change", (event) => {

    critDateDeb = new Date(event.target.value);
    updateAffichageOffres();

});

document.getElementById("dateFin").addEventListener("change", (event) => {

    critDateFin = new Date(event.target.value);
    updateAffichageOffres();

});


// Prix min / max
document.getElementById("prixMin").addEventListener("change", (event) => {

    if (event.target.value == "") {
        critPrixMin = null;
    } else {
        critPrixMin = event.target.value;
    }
    updateAffichageOffres();

});

document.getElementById("prixMax").addEventListener("change", (event) => {

    if (event.target.value == "") {
        critPrixMax = null;
    } else {
        critPrixMax = event.target.value;
    }
    updateAffichageOffres();

});


// Fonction qui retourne true si une offre correspond au critères sélectionnés
function verifFiltre(idOffre)
{
    let valide = false;

    let valideCat = false;
    let valideLieu = false;
    let valideOuv = false;
    let valideDate = false;
    let validePrix = false;

    let offre = mapOffresInfos.get(idOffre);

    // Si acun critère n'a été sélectionné
    if ((critCategorie.length == 0) && (critOuverture.length == 0) && (critLieu == "") && (critDateDeb == null) && (critDateFin == null) && (critPrixMin == null) && (critPrixMax == null)){
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

        // On traite le cas du filtre par date
        if ((critDateDeb != null) || (critDateFin != null)) {
            if ((critDateDeb != null) && (critDateFin != null)) {
                
            } else if (critDateDeb != null) {
                
            } else if (critDateFin != null) {
                
            }
        } else {
            valideDate = true;
        }

        // On traite le cas du filtre par fourchette de prix
        if (((critPrixMin != null)) || ((critPrixMax != null))) {
            if (((critPrixMin != null) && (critPrixMax != null))) {
                if ((critPrixMin <= parseInt(offre.get("prix"), 10)) && (critPrixMax >= parseInt(offre.get("prix"), 10))) {
                    validePrix = true;
                }
            } else if (critPrixMin != null) {
                if (critPrixMin <= parseInt(offre.get("prix"), 10)) {
                    validePrix = true;
                }
            } else if (critPrixMax != null) {
                if (critPrixMax >= parseInt(offre.get("prix"), 10)) {
                    validePrix = true;
                }
            }
        } else {
            validePrix = true;
        }


        // Si l'offre correspond à tout les critères, elle peut être affichées
        if ((valideCat) && (valideLieu) && (valideOuv) && (valideDate) && (validePrix)) {
            valide = true;
        }
        
    }
    console.log(valide);

    return valide;
}


// ================== FONCTIONS TRIES PRIX ========================

let triePrix = "";  // Pour connaitre l'état du trie

function trierPrix() {
    if (triePrix == "") {

        // Trie le Tableau mapOffresInfos dans l'ordre croissant dans le tableau mapTrieAcs
        let mapTrieAcs = new Map([...mapOffresInfos.entries()].sort((a,b) => a[1].get("prix") - b[1].get("prix")));
        
        let index = 0;
        // Parcour le tableau mapTrieAcs pour ajouter un attribut order dans le style des élément
        mapTrieAcs.forEach((map, key, value)=>{

            // Récupère l'élément dans la page
            let elem = document.getElementById(mapTrieAcs.get(key).get("id"));
            elem.style.order = index;   // Rajoute l'attribut css order égal à sa position dans le tableau 

            // Rajoute une bordure bleu sur le bouton
            document.getElementById("btnTriePrix").style.border = "solid";
            document.getElementById("btnTriePrix").style.borderColor = "blue" ;

            index++;
        })
        
        triePrix = "asc";   // Modifie l'état du trie
    }
    else if(triePrix == "asc") {

        // Trie le Tableau mapOffresInfos dans l'ordre décroissant dans le tableau mapTrieDesc
        let mapTrieDesc = new Map([...mapOffresInfos.entries()].sort((a,b) => b[1].get("prix") - a[1].get("prix")));

        let index = 0;
        // Parcour le tableau mapTrieDesc
        mapTrieDesc.forEach((map, key, value)=>{

            // Récupère l'élément dans la page
            let elem = document.getElementById(mapTrieDesc.get(key).get("id"));
            elem.style.order = index;   // Rajoute l'attribut css order égal à sa position dans le tableau 

            // Rajoute une bordure bleu sur le bouton
            document.getElementById("btnTriePrix").style.border = "solid";
            document.getElementById("btnTriePrix").style.borderColor = "red" ;
            index++;
        })
        triePrix = "decs";  // Modifie l'état du trie
    }
    else if(triePrix == "decs"){
        let index = 0;
        // Parcour le tableau mapOffresInfos pour enlever le trie et remettre les offres dans l'ordre normale
        mapOffresInfos.forEach((map, key, value)=>{

            // Récupère l'élément dans la page
            let elem = document.getElementById(mapOffresInfos.get(key).get("id"));
            elem.style.order = index;   // Rajoute l'attribut css order égal à sa position dans le tableau 

            // Rajoute une bordure bleu sur le bouton
            document.getElementById("btnTriePrix").style.border = "none";
            index++;
        })
        triePrix = "";  // Modifie l'état du trie
    }
    updateAffichageOffres();
}


// Gestion du déroulement du filtre

let filtreBarre = document.getElementsByClassName("filtrerBarre")[0];
let filtreHead = document.getElementsByClassName("filtreHead")[0];
let filtreG = document.getElementsByClassName("filtreDeplie")[0];

filtreHead.addEventListener("click", derouleFiltre);

function derouleFiltre() {
    filtreBarre.classList.toggle("changeBordure");
    filtreG.classList.toggle("filtreGrand");
    filtreG.classList.toggle("displayNone");
}