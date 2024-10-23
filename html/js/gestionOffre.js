function ChangerBtnLigne(idOffre) 
{
    const icone = document.querySelector("#offre" + idOffre + " #conteneurBtnGestion img");
    if (icone.src.includes("/icones/horsLigneSVG.svg")) 
    {
        icone.src = "/icones/enLigneSVG.svg";
    } else
    {
        icone.src = "/icones/horsLigneSVG.svg";
    }

    const texteBtnLigne = document.querySelector("#offre" + idOffre + " p");
    if (texteBtnLigne.innerText === "Mettre l'offre hors ligne") 
        {
            texteBtnLigne.innerText = "Mettre l'offre en ligne";
    } else 
    {
        texteBtnLigne.innerText = "Mettre l'offre hors ligne";
    }

    const texteStatutOffre = document.querySelector("#offre" + idOffre + " #conteneurGestion h4 span");
    if (texteStatutOffre.innerText === "En ligne")
    {
        texteStatutOffre.innerText = "Hors ligne";
        texteStatutOffre.classList.remove("enLigne");
        texteStatutOffre.classList.add("horsLigne");
    } else 
    {
        texteStatutOffre.innerText = "En ligne";
        texteStatutOffre.classList.remove("horsLigne");
        texteStatutOffre.classList.add("enLigne");
    }

    const boutonModif = document.querySelector("#offre" + idOffre + " #conteneurGestion a");
    boutonModif.classList.toggle("btnModifCache");
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
