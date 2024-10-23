function ChangerBtnLigne(idOffre) 
{
    //const image = document.getElementById('imgEnHorsLigne');
    const image = document.querySelectorAll("#offre" + idOffre + " #conteneurBtnGestion img");
    console.log("#offre" + idOffre + " .conteneurBtnGestion img");
    if (image.src.includes("/icones/horsLigneSVG.svg")) 
    {
        image.src = "/icones/enLigneSVG.svg";
    } else
    {
        image.src = "/icones/horsLigneSVG.svg";
    }

    const texte = document.getElementById('txtEnHorsLigne');
    if (texte.innerText === "Mettre l'offre hors ligne") {
        texte.innerText = "Mettre l'offre en ligne";
    } else {
        texte.innerText = "Mettre l'offre hors ligne";
    }

    const texte2 = document.getElementById('txtEnLigne');
    if (texte2.innerText === "En ligne"){
        texte2.innerText = "Hors ligne";
    } else {
        texte2.innerText = "En ligne";
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
