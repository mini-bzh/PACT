function initOffres()
/*renvoie la map mapOffreInfos : sa clé est un id offre et sa valeur est une map dont la clé est un string du nom d'une information de l'offre et
la valeur de l'information (exemple : "titre" => "Fort la Latte", "prix" => 15)*/
{
    let elementsOffres = document.querySelectorAll(".lienApercuOffre");

    let mapOffresInfos = new Map();

    elementsOffres.forEach(element => {

        let mapTempo = new Map();                       //map temporaire qui stock les informations de l'élément

        mapTempo.set("id", element.id);                 //l'id de l'offre
        mapTempo.set("visibilite", true);               /*array qui contiendra des chaines de caractères indiquant les filtres/tri/recherche 
                                                        qui ont besoin de l'afficher. Si vide, alors l'offre ne sera pas affichée*/
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
/*met à jour les offres à afficher*/
{
    mapOffresInfos.forEach((map, key, value)=>{
        if(!mapOffresInfos.get(key).get("visibilite") && eric(key))
        {
            mapOffresInfos.get(key).get("element").classList.add("displayNone");
        }
        else
        {
            console.log(mapOffresInfos.get(key).get("element"));
            mapOffresInfos.get(key).get("element").classList.remove("displayNone");
        }
    });

    console.log("update faite");
}



let mapOffresInfos = initOffres();



let barreRecherche = document.getElementById("searchbar");
barreRecherche.addEventListener("keyup", rechercher);


function rechercher()
{
    let texte = barreRecherche.value.toLowerCase();
    console.log(mapOffresInfos);

    mapOffresInfos.forEach((map, key, value)=>{
        if(texte.length >= 1)
        {

            if(mapOffresInfos.get(key).get("titre").toLowerCase().includes(texte))
            {
                mapOffresInfos.get(key).set("visibilite", true);
            }
            else
            {
                console.log("cc");
                mapOffresInfos.get(key).set("visibilite", false);
            }
        }
        else
        {
            mapOffresInfos.get(key).set("visibilite", true);
        }
    });
    
    updateAffichageOffres();
}


function eric(idOffre)
{
    return true;
}

// ================== FONCTIONS TRIES PRIX ========================

let triePrix = "";

function trierPrix() {
    if (triePrix === "asc") {
        trierPrixDecroissant();
    }
    if (triePrix === "decs") {
        trierPrixCroissant();
    }
    else{
        trierPrixCroissant();
    }
}

function trierPrixCroissant() {
    let mapTrié = new Map([...mapOffresInfos.entries()].sort((a,b) => a[1].get("prix") - b[1].get("prix")));
    
    let i = 0;
    mapTrié.forEach((map, key, value)=>{
        mapTrié.get(key).get("element").classList.add("order"+i);
        console.log(mapTrié.get(key).get("element"));
        i++;
    })

    let index = 0;
    mapTrié.forEach((map, key, value)=>{
        let elem = document.getElementById(mapTrié.get(key).get("id"));
        elem.style.order = index;
        index++;
    })

    triePrix = "asc";
}

function trierPrixDecroissant() {
    let mapTrié = new Map([...mapOffresInfos.entries()].sort((a,b) => b[1].get("prix") - a[1].get("prix")));
    
    let i = 0;
    mapTrié.forEach((map, key, value)=>{
        mapTrié.get(key).get("element").classList.add("order"+i);
        console.log(mapTrié.get(key).get("element"));
        i++;
    })

    let index = 0;
    mapTrié.forEach((map, key, value)=>{
        let elem = document.getElementById(mapTrié.get(key).get("id"));
        elem.style.order = index;
        index++;
    })

    triePrix = "decs";
}