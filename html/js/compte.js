let detailPrixDeplie = document.getElementsByClassName("detailPrixDeplie");
let btnDetailPrix = document.getElementsByClassName("btnDetailPrix")[0];
if(btnDetailPrix != undefined)
    {
        btnDetailPrix.addEventListener("click", () => {
            detailPrixDeplie.classList.toggle('displayNone');
        });
    };



function confModifProfil() {
    let pop = document.querySelector('.popUpModif');
    pop.style.display = 'flex';
    document.body.classList.add('no-scroll');
}

function confModifBanc() {
    let pop = document.querySelector('.popUpModifBancaire');
    pop.style.display = 'flex';
    document.body.classList.add('no-scroll');
}

function verif_pass() {
    let passwordInput = document.getElementById('password_for_banc');
    let valeur = passwordInput.value;

    let request = "_compte where mot_de_passe = '" + valeur + "' and id_c = '" + id_c + "';";

    $.ajax({
        url: "../php/sql_request.php",              // Le fichier PHP à appeler, qui met à jour la BDD
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

let btnAPIkey = document.getElementsByClassName("btnCreaAPI")[0];
console.log(btnAPIkey);

btnAPIkey.addEventListener("click", () => {
    if (btnAPIkey.classList.contains("btnCreaAPIgris")) {
        alert("Vous avez déjà générer une clé API");
    } else {
        generateApiKey();
        btnAPIkey.classList.add("btnCreaAPIgris");
    }
})

function generateApiKey() {
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let apiKey = '';
    for (let i = 0; i < 32; i++) {
        apiKey += characters.charAt(Math.floor(Math.random() * characters.length));
    }

    // Afficher la clé générée sur la page
    document.getElementById('apiKeyTexte').innerText = "Votre Clé API : ";
    document.getElementById('apiKey').innerText = apiKey;


    $.ajax({
        url: "../php/genAPIkey.php",              // Le fichier PHP à appeler, qui met à jour la BDD
        type: 'POST',                               // Type de la requête (pour transmettre idOffre au fichier PHP)
        data: { apiKey: apiKey },
        success: function (response) {
            console.log("reussite");
        },
        error: function (jqXHR, textStatus, errorThrown) {
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