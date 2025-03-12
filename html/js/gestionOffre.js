function ChangerBtnLigne(idOffre)
/*  idOffre : id de l'offre concernée par les changements
    met à jour le visuel du bouton pour passer l'offre en/hors ligne, le texte indiquant le statut de l'offre, et 
    affiche ou non le bouton modifier */
{
    // met à jour l'icône de bouton
    const icone = document.querySelector("#offre" + idOffre + " #conteneurBtnGestion img");
    if (icone.src.includes("/icones/horsLigneSVG.svg")) 
    {
        icone.src = "/icones/enLigneSVG.svg";
    } else
    {
        icone.src = "/icones/horsLigneSVG.svg";
    }

    // met à jour le texte du bouton
    const texteBtnLigne = document.querySelector("#offre" + idOffre + " #conteneurBtnGestion p");
    if (texteBtnLigne.innerText === "Mettre l'offre hors ligne") 
    {
        texteBtnLigne.innerText = "Mettre l'offre en ligne";
    } else 
    {
        texteBtnLigne.innerText = "Mettre l'offre hors ligne";
    }

    // met à jour le statut de l'offre (texte et couleur)
    const texteStatutOffre = document.querySelector("#offre" + idOffre + " h4 span");
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

    // cache ou non le bouton modifier
    let boutonModif = document.querySelector("#offre" + idOffre + " #conteneurBtnGestion a");
    boutonModif.classList.toggle("btnModifCache");

    
}

function toggleEnLigne(idOffre)
/*  idOffre : id de l'offre concernée par les changements
    s'exécute quand le bouton pour passer une offre en/hors ligne. Change le statut de l'offre dans la BDD et 
    les visuels de l'offre (ciblée avec idOffre) */
{
    ChangerBtnLigne(idOffre);                       //change les visuels de l'offre (ciblée avec idOffre)
    $.ajax({
        url: "/composants/ajax/toggleStatutOffre.php",        // Le fichier PHP à appeler, qui met à jour la BDD
        type: 'POST',                               // Type de la requête (pour transmettre idOffre au fichier PHP)
        data: {idOffre: idOffre},
        success: function(response) {

            alert(response);                        // Affiche la réponse du script PHP si appelé correctement
        },
        error: function()
        {
            alert('Erreur lors de l\'exécution de la fonction PHP');        //affiche un message d'erreur sinon
        }
    });
}

