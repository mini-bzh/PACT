let detailPrixDeplie = document.getElementsByClassName("detailPrixDeplie");
let btnDetailPrix = document.getElementsByClassName("btnDetailPrix")[0];
if(btnDetailPrix != undefined)
    {
        btnDetailPrix.addEventListener("click", () => {
            detailPrixDeplie.classList.toggle('displayNone');
        });
    };



function confModifProfil() {
    let pop = document.getElementById('popUpModif');
    pop.style.display = 'flex';
    document.body.classList.add('no-scroll');
}

function confModifBanc() {
    let pop = document.getElementById('popUpModifBancaire');
    pop.style.display = 'flex';
    document.body.classList.add('no-scroll');
}

function verif_pass() {
    let passwordInput = document.getElementById('password_for_banc');
    let valeur = passwordInput.value;

    let request = "_compte where mot_de_passe = '" + valeur + "' and id_c = '" + id_c + "';";

    $.ajax({
        url: "../composants/ajax/sql_request.php",              // Le fichier PHP à appeler, qui met à jour la BDD
        type: 'POST',                               // Type de la requête (pour transmettre idOffre au fichier PHP)
        data: { request: request },
        success: function (response) {
            let pass = response;
            //alert(pass);                        // Affiche la réponse du script PHP si appelé correctement
            //location.reload();
            if (pass === "1") {
                window.location.href = "../pages/ModifInfoBancaire.php";
            } else {
                document.getElementById('erreur_mdp').classList.remove('displayNone');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Erreur AJAX : ", textStatus, errorThrown);
            alert("Erreur lors de l'appel du script PHP : " + textStatus);
        }
    });
}

// on récupère le clique sur le bouton btnCreaAPI 
let btnAPIkey = document.getElementsByClassName("btnCreaAPI")[0];

// afin de pouvoir executer la fonction generateAPIkey au moment du clique et éviter que le membre ou pro spam clique le bouton
// il est griser après le déclachement de la fonction
btnAPIkey.addEventListener("click", () => {
    if (btnAPIkey.classList.contains("btnCreaAPIgris")) {
        alert("Vous avez déjà générer une clé API");
    } else {
        generateApiKey();
        btnAPIkey.classList.add("btnCreaAPIgris");
    }
})

// fonction qui permet de générer des clé API et de les envoyer
function generateApiKey() {

    // ici on crée la clé API
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let apiKey = '';
    for (let i = 0; i < 32; i++) {
        apiKey += characters.charAt(Math.floor(Math.random() * characters.length));
    }

    // Afficher la clé générée sur la page
    document.getElementById('apiKeyTexte').innerText = "Votre Clé API : ";
    document.getElementById('apiKey').innerText = apiKey;

    // permet de lié JS et PHP
    $.ajax({
        url: "../composants/ajax/genAPIkey.php",              // Le fichier PHP à appeler, qui met à jour la BDD 
        type: 'POST',                               
        data: { apiKey: apiKey },
        success: function (response) { // en cas de réussite
            console.log("reussite");
        },
        error: function (jqXHR, textStatus, errorThrown) { // en cas d'erreur
            console.log("Erreur AJAX : ", textStatus, errorThrown);
            alert("Erreur lors de l'appel du script PHP : " + textStatus);
        }
    });
}




/*
<script>
// Mot de passe correct
const correctPassword = <?php echo $infos['mot_de_passe'];?> ;
</script>
*/

/* Pour la fermeture des pop Up */
document.getElementById('bn-modif-exit').addEventListener("click", function() {
    document.getElementById('popUpModif').style.display = "none";
});

document.getElementById('bn-modifBanc-exit').addEventListener("click", function() {
    document.getElementById('popUpModifBancaire').style.display = "none";
});


// on récupère le clique sur le bouton btnAuthenticator
let btnAuthenticator = document.getElementsByClassName("btnAuthent")[0];

// afin de pouvoir executer la fonction generateAPIkey au moment du clique et éviter que le membre ou pro spam clique le bouton
// il est griser après le déclachement de la fonction
btnAuthenticator.addEventListener("click", () => {
    let pop = document.getElementsByClassName('popQRcode')[0];
    pop.style.display = 'flex';
    document.body.classList.add('no-scroll');
})

let croix = document.getElementById("annulerQRcode");

croix.addEventListener("click", () => {
    let pop = document.getElementsByClassName('popQRcode')[0];
    pop.style.display = 'none';
    document.body.classList.remove('no-scroll');
})