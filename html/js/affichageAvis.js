/* ------------------------ supprimer avis ------------------------*/

let btnSupprimerAvis = document.querySelector(".btnSupprimerAvis");


if(typeof(btnSupprimerAvis) !== 'undefined' && btnSupprimerAvis !== null)
{
    btnSupprimerAvis.addEventListener("click", supprimerAvis);
}

function supprimerAvis()
{
    if(confirm("Voulez-vous supprimer votre avis ?\nVous pourrez en déposer un autre."))
    {
        let idAvis = document.querySelectorAll(".btnSupprimerAvis p")[1].textContent;
        
        $.ajax({
            url: "../php/supprimerAvis.php",              // Le fichier PHP à appeler, qui met à jour la BDD
            type: 'POST',                               // Type de la requête (pour transmettre idOffre au fichier PHP)
            data: {idAvis: idAvis},
            success: function(response) {
    
                //alert(response);                        // Affiche la réponse du script PHP si appelé correctement
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("Erreur AJAX : ", textStatus, errorThrown);
                alert("Erreur lors de l'appel du script PHP : " + textStatus);
            }
        });
    }
    else
    {
        alert("Votre avis n'est pas supprimé.");
    }
}

/* ------------------------ Signaler avis ------------------------*/

let btnSignalerAvis = document.querySelectorAll(".btnSignalerAvis");


// btnSignalerAvis.forEach(btn =>{
//     if(typeof(btnSignalerAvis) !== 'undefined' && btnSignalerAvis !== null)
//         {
//             btn.addEventListener("click", confSignaler);
//         }
// })


function confSignaler(event){ //fonction pour afficher une pop up
    let idAvis = event.target.id; // on récupère l'id de l'avis
    console.log(idAvis);
    let pop = document.querySelector('.popUpSignaler');
    pop.style.display = 'flex';
    let btnValider = document.querySelectorAll(".btnValiderId")[0];
    document.body.classList.add('no-scroll');
    btnValider.id = idAvis;  //l'id de l'avis est mis dans le bouton bouton valider
    console.log(btnValider);
}

function fermeConfSignaler(){ //fonction pour fermer la pop up en cas d'annulation
    let pop = document.querySelector('.popUpSignaler');
    pop.style.display = 'none';
    document.body.classList.remove('no-scroll');
}

function signalerAvis(){ //fonction pour signaler. On récupère l'id de l'avis, le motif du signalement et l'id de la personne qui signal
    let btnValider = document.querySelectorAll(".btnValiderId")[0];
    console.log(btnValider);
    let motifSignalement = document.getElementById("motifSignalement").value;
    let idCompte = document.querySelectorAll(".btnSignalerAvis p")[1].textContent;
    let idAvis = btnValider.id;
    console.log(idAvis);

    if(motifSignalement != ""){
        $.ajax({
            url: "../php/signalerAvis.php",              // Le fichier PHP à appeler, qui met à jour la BDD
            type: 'POST',                               // Type de la requête (pour transmettre
            data: {idCompte: idCompte, motifSignalement: motifSignalement, idAvis: idAvis},
            success: function(reponse){
                alert("Signalement envoyé");
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("Erreur AJAX : ", textStatus, errorThrown);
                alert("Erreur lors de l'appel du script PHP : " + textStatus);
            }
        });
    }
    else{
        alert("Veuillez renseigner un motif de signalement");
    }
}

/* ------------------------ like/dislike avis ------------------------*/

//partie cookies

function cookieContientCle(cle)
{
    const cookies = document.cookie.split("; ");
    for(let cookie of cookies)
    {
        const [key, value] = cookie.split("=");
        if(key == cle)
        {
            return true
        }
    }
    return false;
}

function getCookie()
{
    const cookies = document.cookie.split("; ");
}

function supprimerCookiePouces() 
{
    document.cookie = "poucesAvis=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;SameSite=Lax";
}

function setCookiePouce(idAvis, pouce)
{
    if(!cookieContientCle("poucesAvis"))
    {
        let poucesAvis = [];
        poucesAvis.push([idAvis, pouce]);

        document.cookie = `poucesAvis=${JSON.stringify(poucesAvis)};path=/;SameSite=Lax`;
    }
    else
    {
        const cookies = document.cookie.split("; ");
        for(let cookie of cookies)
        {
            const [key, value] = cookie.split("=");
            if(key == "poucesAvis")
            {
                let poucesAvis = JSON.parse(value);
                let trouve = false;
                for(let pouceAvis of poucesAvis)
                {
                    if(pouceAvis[0] == idAvis)
                    {
                        pouceAvis[1] = pouce;
                        trouve = true;
                    }
                }

                if(!trouve)
                {
                    poucesAvis.push([idAvis, pouce]);
                }

                document.cookie = `poucesAvis=${JSON.stringify(poucesAvis)};path=/;SameSite=Lax`;
            }
        }
    }
}

function updateAffichageLikes()     //va vérifier dans les cookies si des pouces ont déjà été cliqués
{
    let poucesAvis = [];
    const cookies = document.cookie.split("; ");
    for(let cookie of cookies)
    {
        const [key, value] = cookie.split("=");
        if(key == "poucesAvis")
        {
            poucesAvis = JSON.parse(value);
        }
    }

    if(poucesAvis != [])
    {
        poucesAvis.forEach(element => {             //element = [idAvis, pouce]
            let idAvis = element[0];
            let pouce = element[1];
            let avis = document.getElementById(`Avis${idAvis}`);
            
            if(avis != undefined)
            {
                if(pouce == "dislike")
                {
                    let btnDislike = avis.querySelector(".conteneurPouces .pouceDislike img");

                    avis.classList.add("avisDislike");

                    btnDislike.src = "../icones/pouceBas2.png";
                }
                else if(pouce == "like")
                {
                    let btnLike = avis.querySelector(".conteneurPouces .pouceLike img");

                    avis.classList.add("avisLike");

                    btnLike.src = "../icones/pouceHaut2.png";
                }
            }
        })
    }
}

document.addEventListener("DOMContentLoaded", updateAffichageLikes);
//partie likes
let avis = document.querySelectorAll(".avis");

let mapBtnCptId = new Map();      //map qui associe à chaque bouton like/dislike son compteur de likes/dislike et l'id de l'avis concerné

avis.forEach(element =>{
    let btnLike = element.querySelector(".conteneurPouces .pouceLike img");
    let cptLike = element.querySelector(".conteneurPouces .pouceLike p");
    let btnDislike = element.querySelector(".conteneurPouces .pouceDislike img");
    let cptDislike = element.querySelector(".conteneurPouces .pouceDislike p");

    let idAvis = element.id.slice(4);

    mapBtnCptId.set(btnLike, [cptLike, idAvis]);
    mapBtnCptId.set(btnDislike, [cptDislike, idAvis]);

    btnLike.addEventListener("click", ()=>{ pouceClique("like")});
    btnDislike.addEventListener("click", ()=>{ pouceClique("dislike")});
});


function pouceClique(pouce)     // lorsqu'un pouce est cliqué, incrémente son compteur et met à jour la BDD
{                               // pouce induque si un like ou dislike a été cliqué
    let cpt = mapBtnCptId.get(event.target)[0];
    let avisParent = document.getElementById("Avis" + mapBtnCptId.get(event.target)[1]);

    if(pouce == "like")
    {
        if(avisParent.classList.contains("avisLike"))
        {
            setCookiePouce(mapBtnCptId.get(event.target)[1], "none");

            avisParent.classList.remove("avisLike");

            cpt.textContent = parseInt(cpt.textContent) - 1;
            event.target.src = "../icones/pouceHautSVG.svg";

            updatePoucesAvis(mapBtnCptId.get(event.target)[1], "like", -1);
        }
        else
        {
            setCookiePouce(mapBtnCptId.get(event.target)[1], "like");

            avisParent.classList.add("avisLike");

            cpt.textContent = parseInt(cpt.textContent) + 1;
            event.target.src = "../icones/pouceHaut2.png";

            updatePoucesAvis(mapBtnCptId.get(event.target)[1], "like", 1);


            if(avisParent.classList.contains("avisDislike"))
            {
                avisParent.classList.remove("avisDislike");
                let btnDislike = avisParent.querySelector(".pouceDislike img");
                let cptDislike = avisParent.querySelector(".pouceDislike p");

                cptDislike.textContent = parseInt(cptDislike.textContent) - 1;
                btnDislike.src = "../icones/pouceBasSVG.svg";

                updatePoucesAvis(mapBtnCptId.get(event.target)[1], "dislike", -1);
            }
        }
    }
    else if(pouce == "dislike")
    {
        if(avisParent.classList.contains("avisDislike"))
        {
            setCookiePouce(mapBtnCptId.get(event.target)[1], "none");

            avisParent.classList.remove("avisDislike");

            cpt.textContent = parseInt(cpt.textContent) - 1;
            event.target.src = "../icones/pouceBasSVG.svg";

            updatePoucesAvis(mapBtnCptId.get(event.target)[1], "dislike", -1);

        }
        else
        {
            setCookiePouce(mapBtnCptId.get(event.target)[1], "dislike");

            avisParent.classList.add("avisDislike");

            cpt.textContent = parseInt(cpt.textContent) + 1;
            event.target.src = "../icones/pouceBas2.png";

            updatePoucesAvis(mapBtnCptId.get(event.target)[1], "dislike", 1);


            if(avisParent.classList.contains("avisLike"))
            {
                avisParent.classList.remove("avisLike");
                let btnLike = avisParent.querySelector(".pouceLike img");
                let cptLike = avisParent.querySelector(".pouceLike p");

                cptLike.textContent = parseInt(cptLike.textContent) - 1;
                btnLike.src = "../icones/pouceHautSVG.svg";

                updatePoucesAvis(mapBtnCptId.get(event.target)[1], "like", -1);

            }
        }
    }
}


function updatePoucesAvis(idAvis, pouce, changement)    //met à jour le compteur de like/dislike de l'avis idAvis
                                                        // pouce indique s'il faut mettre à jour les likes ou dislikes
                                                        //changement vaut 1 ou -1 et indique s'il faut incrémenter ou décrémenter
{
    $.ajax({
        url: "../php/updatePoucesAvis.php",         // Le fichier PHP à appeler, qui met à jour la BDD
        type: 'POST',                               // Type de la requête (pour transmettre idOffre au fichier PHP)
        data:  {                                    // données transférées au script php
            idAvis: idAvis,
            pouce: pouce,
            changement, changement
        },
        success: function(response) {

            //console.log(response);                        // Affiche la réponse du script PHP si appelé correctement
        },
        error: function(jqXHR, textStatus, errorThrown) 
        {
            console.log("Erreur AJAX : ", textStatus, errorThrown);
            alert("Erreur lors de l'appel du script PHP : " + textStatus);
        }
    });
}

/* ------------------------ repondre avis ------------------------*/

avis.forEach(av => {
    let btnRepondre = av.querySelector(".formReponse .btnRepondre");

    if(btnRepondre != undefined)
    {
        let reponseAvis = av.querySelector(".formReponse .reponseAvis");
        let erreurReponseVide = av.querySelector(".formReponse .erreurReponseVide");
    
        let idAvis = av.id.slice(4);
    
        console.log(btnRepondre, reponseAvis, idAvis, erreurReponseVide);
    
        btnRepondre.addEventListener("click", ()=>{
            if(reponseAvis.value.length == 0)
            {
                erreurReponseVide.hidden = false;
            }
            else
            { 
                envoyerReponse(idAvis, reponseAvis.value);
            }
        });
    
        reponseAvis.addEventListener("keyup", ()=>{             //enlève le message d'erreur si l'utilisateur commence à écrire une réponse
            if(erreurReponseVide.hidden == false && reponseAvis.value.length > 0)
            {
                erreurReponseVide.hidden = true;
            }
        })
    }
})

function envoyerReponse(idAvis, reponseAvis)
{
    $.ajax({
        url: "../php/reponseAvis.php",              // Le fichier PHP à appeler, qui met à jour la BDD
        type: 'POST',                               // Type de la requête (pour transmettre idOffre au fichier PHP)
        data: {idAvis: idAvis, reponseAvis : reponseAvis},
        success: function(response)
        {
            console.log(response);                        // Affiche la réponse du script PHP si appelé correctement
            location.reload();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("Erreur AJAX : " + textStatus + errorThrown);         //affiche une erreur sinon
            alert("Erreur lors de l'appel du script PHP : " + textStatus);
        }
    });
}