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


