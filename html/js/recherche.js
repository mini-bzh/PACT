function initOffres()
{
    let elementsOffres = document.querySelectorAll(".lienApercuOffre"); 

    let mapOffresInfos = new Map();

    elementsOffres.forEach(element => {

        let mapTempo = new Map();
        mapTempo.set("id", element.id);
        mapTempo.set("visibilite", []);         //array qui contiendra des chaines de caractères indiquant les filtres/tri/recherche qui ont 
                                                //besoin de l'afficher. Si vide, alors l'offre ne sera pas affichée
        mapTempo.set("element", element);
        mapTempo.set("titre", document.querySelectorAll("#" + element.id + " .apercuOffre h3")[0].textContent);
        let prix = document.querySelectorAll("#" + element.id + " .text-overlay span")[0].textContent;
        prix = prix.substring(0, prix.length-1);
        mapTempo.set("prix", parseInt(prix));
        mapTempo.set("ouverture", document.querySelectorAll("#" + element.id + " .ouvert")[0].textContent);

        mapOffresInfos.set(element.id, mapTempo);
    });

    return mapOffresInfos;
}


function updateAffichageOffres()
{
    mapOffresInfos.forEach((map, key, value)=>{
        if(mapOffresInfos.get(key).get("visibilite").length == 0)
        {
            mapOffresInfos.get(key).get("element").classList.add("displayNone");
        }
        else
        {
            mapOffresInfos.get(key).get("element").classList.remove("displayNone");
        }
    })
}

function verifConpatibilite(tab)
{
    
}

function retireElement(array, valeur)
{
    let i = 0;
    let trouve = false;

    while(i < array.length && !trouve)
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



let barreRecherche = document.getElementById("searchbar");
barreRecherche.addEventListener("keyup", rechercher);

function rechercher()
{
    let texte = barreRecherche.value.toLowerCase();

    mapOffresInfos.forEach((map, key, value)=>{
        if(texte.length >= 1)
        {
            if(mapOffresInfos.get(key).get("titre").toLowerCase().includes(texte))
            {
                if(!mapOffresInfos.get(key).get("visibilite").includes("recherche"))
                {
                    mapOffresInfos.get(key).get("visibilite").push("recherche");
                }
            }
            else
            {
                mapOffresInfos.get(key).set("visibilite", retireElement(mapOffresInfos.get(key).get("visibilite"), "recherche"));
            }
        }
        else
        {
            if(!mapOffresInfos.get(key).get("visibilite").includes("recherche"))
            {
                mapOffresInfos.get(key).get("visibilite").push("recherche");
            }
        }

    });
    
    updateAffichageOffres();
}