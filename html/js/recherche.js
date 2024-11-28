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
        else if(!eric(key))
        {
            mapOffresInfos.get(key).get("element").classList.remove("displayNone");
        }
    });
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

let triePrix = "";

function trierPrix() {
    if (triePrix == "") {
        let mapTrieAcs = new Map([...mapOffresInfos.entries()].sort((a,b) => a[1].get("prix") - b[1].get("prix")));
        
        let index = 0;
        mapTrieAcs.forEach((map, key, value)=>{
            let elem = document.getElementById(mapTrieAcs.get(key).get("id"));
            elem.style.order = index;
            index++;
        })
        
        triePrix = "asc";
    }
    else if(triePrix == "asc") {
        let mapTrieDesc = new Map([...mapOffresInfos.entries()].sort((a,b) => b[1].get("prix") - a[1].get("prix")));

        let index = 0;
        mapTrieDesc.forEach((map, key, value)=>{
            let elem = document.getElementById(mapTrieDesc.get(key).get("id"));
            elem.style.order = index;
            index++;
        })
        triePrix = "decs";
    }
    else if(triePrix == "decs"){
        let index = 0;

        mapOffresInfos.forEach((map, key, value)=>{
            let elem = document.getElementById(mapOffresInfos.get(key).get("id"));
            elem.style.order = index;
            index++;
        })
        triePrix = ""
    }
}