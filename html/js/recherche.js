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
        
        let prix = document.querySelectorAll("#" + element.id + " .text-overlay span")[0].textContent;
        prix = prix.substring(0, prix.length-1);
        mapTempo.set("prix", parseInt(prix));   //prix

        mapTempo.set("ouverture", element.classList.contains("ouvert"));        //si l'offre est ouverte ou non

        mapOffresInfos.set(element.id, mapTempo);
    });

    return mapOffresInfos;
}


function updateAffichageOffres()
/*masque les offres qui doivent être affichées par la recherche et les filtrer, masque les autres*/
{
    mapOffresInfos.forEach((map, key, value)=>{
        if(!mapOffresInfos.get(key).get("visibilite") && eric(key))
        {
            mapOffresInfos.get(key).get("element").classList.add("displayNone");
        }
        else
        {
            mapOffresInfos.get(key).get("element").classList.remove("displayNone");
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


function eric(idOffre)
{
    return true;
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
            document.getElementById("btnTriePrix").style.borderColor = "blue" ;
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