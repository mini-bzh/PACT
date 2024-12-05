let nomCat = document.getElementById("nomCat");
let offreResto = document.getElementById("secRestaurant");

function infoEnPlus() {
    if (nomCat.textContent === "restauration") {
        offreResto.classList.remove("displayNone");
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

let trieDate = "";

function trierDate() {
    if (trieDate == "") {

        // Trie le Tableau mapAvisInfos dans l'ordre croissant dans le tableau mapTrieAcs
        let mapTrieAcs = new Map(
            [...mapAvisInfos.entries()].sort((a,b) => {
              // Tri selon la somme des éléments
              const sumA = a[1].get("date").reduce((acc, val) => acc + val, 0);
              const sumB = b[1].get("date").reduce((acc, val) => acc + val, 0);
              return sumA - sumB; // Ordre croissant
            })
          );

        
        let index = 0;
        // Parcour le tableau mapTrieAcs pour ajouter un attribut order dans le style des élément
        mapTrieAcs.forEach((map, key, value)=>{

            // Récupère l'élément dans la page
            let elem = document.getElementById(mapTrieAcs.get(key).get("id"));
            elem.style.order = index;   // Rajoute l'attribut css order égal à sa position dans le tableau 

            // Rajoute une bordure bleu sur le bouton
            document.getElementById("iconeTrieDate1").classList.toggle("displayNone");
            document.getElementById("iconeTrieDate").classList.toggle("displayNone");
            document.getElementById("btnTrieDate").style.border = "solid";
            document.getElementById("btnTrieDate").style.borderColor = "blue" ;

            index++;
        })
        
        trieDate = "asc";   // Modifie l'état du trie
    }
    else if(trieDate == "asc") {

        // Trie le Tableau mapAvisInfos dans l'ordre décroissant dans le tableau mapTrieDesc
        let mapTrieDesc = new Map(
            [...mapAvisInfos.entries()].sort((a,b) => {
              // Tri selon la somme des éléments
              const sumA = a[1].get("date").reduce((acc, val) => acc + val, 0);
              const sumB = b[1].get("date").reduce((acc, val) => acc + val, 0);
              return sumB - sumA; // Ordre croissant
            })
          );

        let index = 0;
        // Parcour le tableau mapTrieDesc
        mapTrieDesc.forEach((map, key, value)=>{

            // Récupère l'élément dans la page
            let elem = document.getElementById(mapTrieDesc.get(key).get("id"));
            elem.style.order = index;   // Rajoute l'attribut css order égal à sa position dans le tableau 

            // Rajoute une bordure bleu sur le bouton
            document.getElementById("iconeTrieDate1").classList.toggle("displayNone");
            document.getElementById("iconeTrieDate2").classList.toggle("displayNone");
            document.getElementById("btnTrieDate").style.border = "solid";
            document.getElementById("btnTrieDate").style.borderColor = "red" ;
            index++;
        })
        trieDate = "decs";  // Modifie l'état du trie
    }
    else if(trieDate == "decs"){
        let index = 0;
        // Parcour le tableau mapAvisInfos pour enlever le trie et remettre les offres dans l'ordre normale
        mapAvisInfos.forEach((map, key, value)=>{

            // Récupère l'élément dans la page
            let elem = document.getElementById(mapAvisInfos.get(key).get("id"));
            elem.style.order = index;   // Rajoute l'attribut css order égal à sa position dans le tableau 

            // Rajoute une bordure bleu sur le bouton
            document.getElementById("iconeTrieDate2").classList.toggle("displayNone");
            document.getElementById("iconeTrieDate").classList.toggle("displayNone");
            document.getElementById("btnTrieDate").style.border = "none";
            index++;
        })
        trieDate = "";  // Modifie l'état du trie
    }
}


/* ------------------------ empêcher ajouter 2e avis ------------------------*/

let btnAjouterAvis = document.getElementById("btnAjouterAvis");

btnAjouterAvis.addEventListener("click", (event) => { // Ajouter le paramètre 'event'
    if (btnAjouterAvis.classList.contains("btnAjouterAvisGrise")) {
        alert("Veuillez supprimer votre ancien avis si vous voulez ajouter un nouvel avis");
    }
});


/* ------------------------ supprimer avis ------------------------*/

let btnSupprimerAvis = document.getElementById("btnSupprimerAvis");
let textBtnSupprimerAvis = document.querySelectorAll("#btnSupprimerAvis p")[1];

console.log(typeof(textBtnSupprimerAvis));

btnSupprimerAvis.addEventListener("click", supprimerAvis);

function supprimerAvis()
{
    if(confirm("Voulez-vous supprimer votre avis ?\nVous pourrez en déposer un autre."))
    {
        $.ajax({
            url: "/php/supprimerAvis.php",              // Le fichier PHP à appeler, qui met à jour la BDD
            type: 'POST',                               // Type de la requête (pour transmettre idOffre au fichier PHP)
            data: {idAvis: idAvis},
            success: function(response) {
    
                //alert(response);                        // Affiche la réponse du script PHP si appelé correctement
            },
            error: function()
            {
                alert('Erreur lors de l\'exécution de la fonction PHP');        //affiche un message d'erreur sinon
            }
        });

        location.reload();
    }
    else
    {
        alert("Votre avis n'est pas supprimé.");
    }
}