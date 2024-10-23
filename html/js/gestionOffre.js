function ChangerBtnLigne(idOffre) 
{
    //const image = document.getElementById('imgEnHorsLigne');
    const image = document.querySelector("#offre" + idOffre + " #conteneurBtnGestion img");
    if (image.src.includes("/icones/horsLigneSVG.svg")) 
    {
        image.src = "/icones/enLigneSVG.svg";
    } else
    {
        image.src = "/icones/horsLigneSVG.svg";
    }

    //const texte = document.getElementById('txtEnHorsLigne');
    const texte = document.querySelector("#offre" + idOffre + " p");
    console.log(texte);
    if (texte.innerText === "Mettre l'offre hors ligne") 
        {
        texte.innerText = "Mettre l'offre en ligne";
    } else 
    {
        texte.innerText = "Mettre l'offre hors ligne";
    }

    //const texte2 = document.getElementById('txtEnLigne');
    const texte2 = document.querySelector("#offre" + idOffre + " #conteneurGestion h4 span");
    console.log(texte2);
    if (texte2.innerText === "En ligne")
    {
        texte2.innerText = "Hors ligne";
        texte2.classList.remove("enLigne");
        texte2.classList.add("horsLigne");
    } else 
    {
        texte2.innerText = "En ligne";
        texte2.classList.remove("horsLigne");
        texte2.classList.add("enLigne");
    }
}

function toggleEnLigne(idOffre)
{
    ChangerBtnLigne(idOffre);
    $.ajax({
        url: 'toggleStatutOffre.php', // Le fichier PHP à appeler
        type: 'POST',        // Type de la requête (POST dans ce cas)
        data: {idOffre: idOffre},
        success: function(response) {
            alert(response); // Affiche la réponse de fonction.php
        },
        error: function() {
            alert('Erreur lors de l\'exécution de la fonction PHP');
        }
    });
}
