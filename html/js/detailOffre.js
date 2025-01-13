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


/* ------------------------ empêcher ajouter 2e avis ------------------------*/

let btnAjouterAvis = document.getElementById("btnAjouterAvis");

if(btnAjouterAvis != null)
{
    btnAjouterAvis.addEventListener("click", (event) => { // Ajouter le paramètre 'event'
    if (btnAjouterAvis.classList.contains("btnAjouterAvisGrise")) {
        alert("Veuillez supprimer votre ancien avis si vous voulez ajouter un nouvel avis");
    }
    });
}



/* ------------------------ supprimer avis ------------------------*/


let btnSupprimerAvis = document.querySelector(".btnSupprimerAvis");
if(typeof(btnSupprimerAvis) !== 'undefined' && btnSupprimerAvis !== null)
{
    btnSupprimerAvis.addEventListener("click", supprimerAvis);
}

function supprimerAvis()
{
    if(confirm("Voulez-vous supprimer votre avis ?\nVous pourrez en déposer un autre."))
    {
        let idAvis = document.querySelectorAll("#btnSupprimerAvis p")[1].textContent;
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


/* ------------------------ like/dislike avis ------------------------*/

let avis = document.querySelectorAll(".avis");

let mapBtnCptId = new Map();      //map qui associe à chaque bouton like/dislike son compteur de likes/dislike et l'id de l'avis concerné

avis.forEach(element =>{
    let btnLike = element.querySelector(".conteneurPouces .pouceLike img");
    let cptLike = element.querySelector(".conteneurPouces .pouceLike p");
    let btnDislike = element.querySelector(".conteneurPouces .pouceDislike img");
    let cptDislike = element.querySelector(".conteneurPouces .pouceDislike p");

    let idAvis = element.id.slice(4);

    mapBtnCptId.set(btnLike, [cptLike, idAvis]);
    mapBtnCptId.set(btnDislike, [cptDislike, idAvis]);

    btnLike.addEventListener("click", ()=>{ pouceClique("like")});
    btnDislike.addEventListener("click", ()=>{ pouceClique("dislike")});
});


function pouceClique(pouce)     // lorsqu'un pouce est cliqué, incrémente son compteur et met à jour la BDD
{                               // pouce induque si un like ou dislike a été cliqué
    let cpt = mapBtnCptId.get(event.target)[0];
    let avisParent = document.getElementById("Avis" + mapBtnCptId.get(event.target)[1]);

    if(pouce == "like")
    {
        if(avisParent.classList.contains("avisLike"))
        {
            avisParent.classList.remove("avisLike");

            cpt.textContent = parseInt(cpt.textContent) - 1;
            event.target.src = "../icones/pouceHautSVG.svg";

            updatePoucesAvis(mapBtnCptId.get(event.target)[1], "like", -1);
        }
        else
        {
            avisParent.classList.add("avisLike");

            cpt.textContent = parseInt(cpt.textContent) + 1;
            event.target.src = "../icones/pouceHaut2.png";

            updatePoucesAvis(mapBtnCptId.get(event.target)[1], "like", 1);


            if(avisParent.classList.contains("avisDislike"))
            {
                avisParent.classList.remove("avisDislike");
                let btnDislike = avisParent.querySelector(".pouceDislike img");
                let cptDislike = avisParent.querySelector(".pouceDislike p");

                cptDislike.textContent = parseInt(cptDislike.textContent) - 1;
                btnDislike.src = "../icones/pouceBasSVG.svg";

                updatePoucesAvis(mapBtnCptId.get(event.target)[1], "dislike", -1);
            }
        }
    }
    else if(pouce == "dislike")
    {
        if(avisParent.classList.contains("avisDislike"))
        {
            avisParent.classList.remove("avisDislike");

            cpt.textContent = parseInt(cpt.textContent) - 1;
            event.target.src = "../icones/pouceBasSVG.svg";

            updatePoucesAvis(mapBtnCptId.get(event.target)[1], "dislike", -1);

        }
        else
        {
            avisParent.classList.add("avisDislike");

            cpt.textContent = parseInt(cpt.textContent) + 1;
            event.target.src = "../icones/pouceBas2.png";

            updatePoucesAvis(mapBtnCptId.get(event.target)[1], "dislike", 1);


            if(avisParent.classList.contains("avisLike"))
            {
                avisParent.classList.remove("avisLike");
                let btnLike = avisParent.querySelector(".pouceLike img");
                let cptLike = avisParent.querySelector(".pouceLike p");

                cptLike.textContent = parseInt(cptLike.textContent) - 1;
                btnLike.src = "../icones/pouceHautSVG.svg";

                updatePoucesAvis(mapBtnCptId.get(event.target)[1], "like", -1);

            }
        }
    }
}


function updatePoucesAvis(idAvis, pouce, changement)    //met à jour le compteur de like/dislike de l'avis idAvis
                                                        // pouce indique s'il faut mettre à jour les likes ou dislikes
                                                        //changement vaut 1 ou -1 et indique s'il faut incrémenter ou décrémenter
{
    $.ajax({
        url: "../php/updatePoucesAvis.php",         // Le fichier PHP à appeler, qui met à jour la BDD
        type: 'POST',                               // Type de la requête (pour transmettre idOffre au fichier PHP)
        data:  {                                    // données transférées au script php
            idAvis: idAvis,
            pouce: pouce,
            changement, changement
        },
        success: function(response) {

            console.log(response);                        // Affiche la réponse du script PHP si appelé correctement
        },
        error: function(jqXHR, textStatus, errorThrown) 
        {
            console.log("Erreur AJAX : ", textStatus, errorThrown);
            alert("Erreur lors de l'appel du script PHP : " + textStatus);
        }
    });
}
