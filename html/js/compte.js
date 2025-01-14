let btnDetailPrix = document.getElementsByClassName("btnDetailPrix");
let detailPrixDeplie = document.getElementsByClassName("detailPrixDeplie");

btnDetailPrix.addEventListener('click', () => {
    detailPrixDeplie.classList.toggle('displayNone');
});

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
        data: {request: request},
        success: function(response) {
            let pass = response;
            //alert(pass);                        // Affiche la réponse du script PHP si appelé correctement
            //location.reload();
            if(pass === "1") {
                window.location.href = "../pages/ModifInfoBancaire.php";
            } else {
                document.getElementById('erreur_mdp').classList.remove('displayNone');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
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