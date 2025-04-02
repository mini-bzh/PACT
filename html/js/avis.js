function initAvis()
/*renvoie la map mapOffreInfos : sa clé est un id offre et sa valeur est une map dont la clé est un string du nom d'une information de l'offre et
la valeur de l'information (exemple : "titre" => "Fort la Latte", "prix" => 15)*/
{
    let elementsAvis = document.querySelectorAll(".avis");

    let mapAvisInfos = new Map();

    elementsAvis.forEach(element => {

        let mapTempo = new Map();                       //map temporaire qui stock les informations de l'élément

        mapTempo.set("id", element.id);                 //l'id de l'offre
        mapTempo.set("element", element);               //l'élément dans le DOM
        
        let date = document.querySelectorAll("#" + element.id + " .datePublication")[0].textContent;
        date = date.split("-");
        let i = 0 ;
        date.forEach(element => {
            date[i] = parseInt(element);
            i++;
        });
        mapTempo.set("date", date);   //date

        mapTempo.set("note", [
            parseFloat(document.querySelector("#" + element.id + " .etoiles p:last-child").textContent.slice(1, -1))
        ]);

        mapAvisInfos.set(element.id, mapTempo);
    });

    return mapAvisInfos;
}

let mapAvisInfos = initAvis();

console.log(mapAvisInfos);

function toogleTrieDate(paramTrie,icone1,icone2,idBouton,sens){

    // Rajoute une bordure bleu sur le bouton
    document.getElementById(icone1).classList.toggle("displayNone");
    document.getElementById(icone2).classList.toggle("displayNone");

    if (sens == "asc") {
        // Trie le Tableau mapAvisInfos dans l'ordre croissant dans le tableau mapTrieAcs
        var mapTrie = new Map(
            [...mapAvisInfos.entries()].sort((a,b) => {
              // Tri selon la somme des éléments
              const sumA = a[1].get(paramTrie).reduce((acc, val) => acc + val, 0);
              const sumB = b[1].get(paramTrie).reduce((acc, val) => acc + val, 0);
              return sumA - sumB; // Ordre croissant
            })
          );

        document.getElementById(idBouton).style.border = "solid";
        document.getElementById(idBouton).style.borderWidth = "1px";
        document.getElementById(idBouton).style.borderColor = "blue" ;
    }
    if (sens == "decs") {
        // Trie le Tableau mapAvisInfos dans l'ordre décroissant dans le tableau mapTrieDesc
        var mapTrie = new Map(
            [...mapAvisInfos.entries()].sort((a,b) => {
              // Tri selon la somme des éléments
              const sumA = a[1].get(paramTrie).reduce((acc, val) => acc + val, 0);
              const sumB = b[1].get(paramTrie).reduce((acc, val) => acc + val, 0);
              return sumB - sumA; // Ordre croissant
            })
          );

        document.getElementById(idBouton).style.border = "solid";
        document.getElementById(idBouton).style.borderWidth = "1px";
        document.getElementById(idBouton).style.borderColor = "red" ;
    }
    if (sens == "default") {
        var mapTrie = mapAvisInfos;
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




//--------- trie note ------------


function toogleTrie(paramTrie,icone1,icone2,idBouton,sens){

    // Rajoute une bordure bleu sur le bouton
    document.getElementById(icone1).classList.toggle("displayNone");
    document.getElementById(icone2).classList.toggle("displayNone");

    if (sens == "asc") {
        // Trie le Tableau mapAvisInfos dans l'ordre croissant dans le tableau mapTrieAcs
        var mapTrie = new Map([...mapAvisInfos.entries()].sort((a,b) => a[1].get(paramTrie)[0] - b[1].get(paramTrie)[0]));

        document.getElementById(idBouton).style.border = "solid";
        document.getElementById(idBouton).style.borderWidth = "1px";
        document.getElementById(idBouton).style.borderColor = "blue" ;
    }
    if (sens == "decs") {
        // Trie le Tableau mapAvisInfos dans l'ordre décroissant dans le tableau mapTrieDesc
        var mapTrie = new Map([...mapAvisInfos.entries()].sort((a,b) => b[1].get(paramTrie)[0] - a[1].get(paramTrie)[0]));

        document.getElementById(idBouton).style.border = "solid";
        document.getElementById(idBouton).style.borderWidth = "1px";
        document.getElementById(idBouton).style.borderColor = "red" ;
    }
    if (sens == "default") {
        var mapTrie = mapAvisInfos;
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


let trieNote ="";
let trieDate ="";

function trierDate() {
    if(trieDate == "desc"){
        toogleTrieDate("date","iconeTrieDate2","iconeTrieDate","btnTrieDate","default");
        trieDate="";
    }
    if (trieDate == "") {
        toogleTrieDate("date","iconeTrieDate1","iconeTrieDate","btnTrieDate","asc");
        trieDate = "asc";   // Modifie l'état du trie
    }
    else if(trieDate == "asc"){
        trieNote="";
        clearBouton("iconeTrieNote","iconeTrieNote1","iconeTrieNote2","btnTrieNote");
        toogleTrieDate("date","iconeTrieDate1","iconeTrieDate2","btnTrieDate","decs");
        trieDate = "desc";  // Modifie l'état du trie
    }
}

trierDate();

function trierNote() {
    clearBouton("iconeTrieDate","iconeTrieDate1","iconeTrieDate2","btnTrieDate");
    trieDate="";
    if (trieNote == "") {
        toogleTrie("note","iconeTrieNote1","iconeTrieNote","btnTrieNote","asc");
        trieNote = "asc";   // Modifie l'état du trie
    }
    else if(trieNote == "asc") {
        toogleTrie("note","iconeTrieNote1","iconeTrieNote2","btnTrieNote","decs");
        trieNote = "decs";  // Modifie l'état du trie
    }
    else if(trieNote == "decs"){
        trierDate();
        toogleTrie("note","iconeTrieNote2","iconeTrieNote","btnTrieNote","default");
        trieNote = "";  // Modifie l'état du trie
    }
}



/*---------------------------- preview image ----------------------------*/
let triggerAffichage = document.querySelectorAll(".imageAvis");
let overlay = document.getElementById("overlay");
let imageOverlay = document.querySelector("#overlay img");
let btnfermerOverlay = document.getElementById("btnFermerOverlay");

triggerAffichage.forEach(element => {
    element.addEventListener("click", afficheOverlayImage);
});

btnfermerOverlay.addEventListener("click", fermerOverlayImage);

function afficheOverlayImage()
{
    let image = event.target.currentSrc;
    imageOverlay.src = image;
    overlay.style.display = "flex";
}

function fermerOverlayImage()
{
    overlay.style.display = "none";
}


/*---------------------------- déplier avis pour offres + mettre en vu  ----------------------------*/

//déplier les avis par offre

let avisOffres = document.querySelectorAll("#mainAvis .conteneurAvisOffre");

let mapBtnConteneurAvisOffre = new Map(); //map qui associe à un bouton pour déplier les avis le conteneur d'avis qui sera déplié

avisOffres.forEach(element =>{
    let btn = element.querySelector(".conteneurBtnTitre img");

    mapBtnConteneurAvisOffre.set(btn, element);

    btn.addEventListener("click", toggleAvisOffre);
})

function toggleAvisOffre()      //toggle l'affichage des avis d'une offre
{
    let conteneurAvisOffre = mapBtnConteneurAvisOffre.get(event.target);
    let conteneurAvis = conteneurAvisOffre.querySelector(".conteneurAvis");

    let pastille = conteneurAvisOffre.querySelector(".pastilleCptAvisNonLus");

    if(pastille != undefined)
    {
        pastille.style.display = "none";
    }

    if(window.getComputedStyle(conteneurAvis).display == "none")
    {
        conteneurAvis.style.display = "block";
        
        event.target.style.transform = "rotate(180deg)";

        MettreAvisOffreLu(conteneurAvisOffre);
    }
    else
    {
        conteneurAvis.style.display = "none";
        event.target.style.transform = "rotate(0deg)";
    }
}
let nouveauxDejaVus = [];   //tableau qui contiendra les nouveaux avis ayant déjà été passés en lu par l'observeur (évite qu'afficher et masquer en boucle un avis ne déclenche plusieurs fois le traitement)

function MettreAvisOffreLu(conteneurAvisOffre)
{            
    let avisLisibles = conteneurAvisOffre.querySelectorAll(".conteneurAvis .nouvelAvis");
    let cptAvisNonLus = document.getElementById("cptAvisNonLus");

    avisLisibles.forEach(avis=>{
        if(!nouveauxDejaVus.includes(avis))
        {
            let nbAvisNonLus = cptAvisNonLus.querySelector("span");  
            if(nbAvisNonLus.textContent == 1)
            {
                cptAvisNonLus.textContent = "Vous n'avez pas de nouvel avis";
            }
            else if(nbAvisNonLus.textContent == 2)
            {
                cptAvisNonLus.innerHTML = "Vous avez <span>1</span> nouvel avis";
            }
            else
            {
                nbAvisNonLus.textContent = parseInt(nbAvisNonLus.textContent)-1;
            }
            
            let idAvis = avis.id.slice(4);
            avisLuBDD(idAvis);

            nouveauxDejaVus.push(avis);
        }
    })
}

//mettre les avis en lu
function avisLuBDD(idAvis)
{
    $.ajax({
        url: "../composants/ajax/avisLuParPro.php",              // Le fichier PHP à appeler, qui met à jour la BDD
        type: 'POST',                               // Type de la requête (pour transmettre idOffre au fichier PHP)
        data: {idAvis: idAvis},
        success: function(response)
        {
            //console.log(response);                        // Affiche la réponse du script PHP si appelé correctement
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("Erreur AJAX : ", textStatus, errorThrown);         //affiche une erreur sinon
            alert("Erreur lors de l'appel du script PHP : " + textStatus);
        }
    });
}
