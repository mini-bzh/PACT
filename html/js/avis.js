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

        mapAvisInfos.set(element.id, mapTempo);
    });

    return mapAvisInfos;
}

let mapAvisInfos = initAvis();

function toogleTrie(paramTrie,icone1,icone2,idBouton,sens){

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

let trieDate ="";

function trierDate() {
    if (trieDate == "") {
        toogleTrie("date","iconeTrieDate1","iconeTrieDate","btnTrieDate","asc");
        trieDate = "asc";   // Modifie l'état du trie
    }
    else if(trieDate == "asc") {
        toogleTrie("date","iconeTrieDate1","iconeTrieDate2","btnTrieDate","decs");
        trieDate = "decs";  // Modifie l'état du trie
    }
    else if(trieDate == "decs"){
        toogleTrie("date","iconeTrieDate2","iconeTrieDate","btnTrieDate","default");
        trieDate = "";  // Modifie l'état du trie
    }
}


/* ------------------------ supprimer avis ------------------------*/


let btnSupprimerAvis = document.querySelectorAll(".btnSupprimerAvis");

btnSupprimerAvis.forEach(btn =>{
    btn.addEventListener("click", supprimerAvis);
})

function supprimerAvis()
{
    if(confirm("Voulez-vous supprimer votre avis ?\nVous pourrez en déposer un autre."))
    {
        let idAvis = document.querySelectorAll(".btnSupprimerAvis p")[1].textContent;
        $.ajax({
            url: "../php/supprimerAvis.php",              // Le fichier PHP à appeler, qui met à jour la BDD
            type: 'POST',                               // Type de la requête (pour transmettre idOffre au fichier PHP)
            data: {idAvis: idAvis},
            success: function(response) {
    
                //alert(response);                        // Affiche la réponse du script PHP si appelé correctement
                location.reload();

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("Erreur AJAX : ", textStatus, errorThrown);
                alert("Erreur lors de l'appel du script PHP : " + textStatus);
            }
        });
    }
    else
    {
        alert("Votre avis n'est pas supprimé.");
    }
}


/*---------------------------- preview image ----------------------------*/
let triggerAffichage = document.querySelectorAll(".imageAvis");
let overlay = document.getElementById("overlay");
let imageOverlay = document.querySelector("#overlay img");
console.log
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

let mapBtnConteneurAvis = new Map(); //map qui associe à un bouton pour déplier les avis le conteneur d'avis qui sera déplié

avisOffres.forEach(element =>{
    let btn = element.querySelector(".conteneurBtnTitre img");
    let conteneurAvis = element.querySelector(".conteneurAvis");

    mapBtnConteneurAvis.set(btn, conteneurAvis);

    btn.addEventListener("click", toggleAvisOffre);
})

function toggleAvisOffre()      //toggle l'affichage des avis d'une offre
{
    let conteneurAvis = mapBtnConteneurAvis.get(event.target);

    if(window.getComputedStyle(conteneurAvis).display == "none")
    {
        conteneurAvis.style.display = "flex";
        event.target.style.transform = "rotate(180deg)";
    }
    else
    {
        conteneurAvis.style.display = "none";
        event.target.style.transform = "rotate(0deg)";
    }
}

//mettre les avis en lu

let avisLisibles = document.querySelectorAll(".conteneurAvis .nouvelAvis");

const observer = new IntersectionObserver((entries)=>{
    entries.forEach(entry => {
        if(entry.isIntersecting)
        {
            let idAvis = entry.target.id.slice(4);
            avisLuBDD(idAvis);
        }
    });
})

avisLisibles.forEach(avisPasLu=>{
    observer.observe(avisPasLu);
})


function avisLuBDD(idAvis)
{
    $.ajax({
        url: "../php/avisLuParPro.php",              // Le fichier PHP à appeler, qui met à jour la BDD
        type: 'POST',                               // Type de la requête (pour transmettre idOffre au fichier PHP)
        data: {idAvis: idAvis},
        success: function(response)
        {
            console.log(response);                        // Affiche la réponse du script PHP si appelé correctement
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("Erreur AJAX : ", textStatus, errorThrown);         //affiche une erreur sinon
            alert("Erreur lors de l'appel du script PHP : " + textStatus);
        }
    });
}