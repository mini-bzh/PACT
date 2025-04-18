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

/*------------------ categories ------------------*/

let nomCat = document.getElementById("nomCat");
let offreResto = document.getElementById("secRestaurant");
let offreParc = document.getElementById("secParcAttr");
let offreSpec = document.getElementById("secSpec");
let offreVisite = document.getElementById("secVisite");
let offreAct = document.getElementById("secAct");

function infoEnPlus() {
    if (nomCat.textContent === "restauration") {
        offreResto.classList.remove("displayNone");
    }

    if (nomCat.textContent === "parc d'attraction") {
        offreParc.classList.remove("displayNone");
    }

    if (nomCat.textContent === "spectacle") {
        offreSpec.classList.remove("displayNone");
    }

    if (nomCat.textContent === "visite") {
        offreVisite.classList.remove("displayNone");
    }

    if (nomCat.textContent === "activité") {
        offreAct.classList.remove("displayNone");
    }
}

infoEnPlus();

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
    trieDate="";
    clearBouton("iconeTrieDate","iconeTrieDate1","iconeTrieDate2","btnTrieDate");
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


/* ------------------------ empêcher ajouter 2e avis ------------------------*/

let btnAjouterAvis = document.getElementById("btnAjouterAvis");

if(btnAjouterAvis != null)
{
    btnAjouterAvis.addEventListener("click", () => {
    if (btnAjouterAvis.classList.contains("btnAjouterAvisGrise")) {
        alert("Veuillez supprimer votre ancien avis si vous voulez ajouter un nouvel avis");
    }
    });
}



/* ------------------ cookies offres récentes ------------------ */

function cookieContientCle(cle)         //renvoie true si les cookies contiennent une valeur pour la clé cle
{
    const cookies = document.cookie.split("; ");
    for(let cookie of cookies)
    {
        const [key, value] = cookie.split("=");
        if(key == cle)
        {
            return true
        }
    }
    return false;
}

function getCookieCle(cle)              // renvoie la valeur associée à la clé cle si elle existe dans les cookies, null sinon
{
    if(cookieContientCle(cle))
    {
        const cookies = document.cookie.split("; ")

        for(let cookie of cookies)
        {
            const [key, value] = cookie.split("=");
            if(key == cle)
            {
                return JSON.parse(value)
            }
        }
    }
    return null
}

function ajouteOffreCookie()                // ajoute dans les cookies l'id de l'offre dont le détail est affiché. Seul 5 id peuvent être stockés en même temps
{
    let idOffre = document.getElementById("idOffreCache").textContent       // récupération de l'id de l'offre
    if(cookieContientCle("offresVues"))
    {
        let offresVues = getCookieCle("offresVues")
        if(!offresVues.includes(idOffre))               // si l'id n'est pas déjà dans le tableau d'ids
        {
            if(offresVues.unshift(idOffre) >= 4)        // ajoute l'id de l'offre
            {
                offresVues.pop()                        // supprime l'id le plus ancien si limite d'ids dépassée
            }
            document.cookie=`offresVues=${JSON.stringify(offresVues)};path=/;SameSite=Lax`      // met à jour les cookies
        }
    }
    else                                // si le cookie pour stocker les ids n'est pas encore crée
    {
        let offresVues = [idOffre]
        document.cookie=`offresVues=${JSON.stringify(offresVues)};path=/;SameSite=Lax`          // crée le cookie, contenant l'id de l'offre affichée
    }
}

document.addEventListener("DOMContentLoaded", ajouteOffreCookie)            // exécute ajouteOffreCookie au chargement d'une page détailoffre