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

function goToListeFacture() {
    window.location.href = "../pages/listeFacture.php";
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
if (btnAPIkey) {
    btnAPIkey.addEventListener("click", () => {
        if (btnAPIkey.classList.contains("btnCreaAPIgris")) {
            alert("Vous avez déjà générer une clé API");
        } else {
            generateApiKey();
            btnAPIkey.classList.add("btnCreaAPIgris");
        }
    })
}

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
let btnModifExit = document.getElementById('bn-modif-exit');

if (btnModifExit) {
    btnModifExit.addEventListener("click", function() {
        document.getElementById('popUpModif').style.display = "none";
    });
}

let btnModifBancExit = document.getElementById('bn-modifBanc-exit');

if (btnModifBancExit) {
    btnModifBancExit.addEventListener("click", function() {
        document.getElementById('popUpModifBancaire').style.display = "none";
    });
}

function ouvrirPopupQuit() {
    let popUPQuit = document.getElementsByClassName("popUpQuitOTP")[0];
    popUPQuit.style.display = "flex";
}


let secretOTP = "";

$(document).ready(function() {
    $(document).on("click", ".btnAuthent", function() {     // Si Authentikator n'est pas encore activé
        $.ajax({
            url: '../composants/ajax/generateur_qrcode.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if ((response.qr_url) && (response.secret)) {
                    let qrDiv = document.getElementById("imgQRcode");
                    let textOTP = document.getElementById("textSecretOTP");

                    secretOTP = response.secret;    // On stock le secret dans une variable

                    if (!qrDiv) {
                        console.error("Erreur : Élément imgQRcode introuvable !");
                        return;
                    }

                    // Vider l'ancien QR Code
                    $('#imgQRcode').html('');

                    // et le texte secret
                    // $('#textSecretOTP').textContent = "";

                    // Générer le QR Code
                    new QRCode(qrDiv, {
                        text: response.qr_url
                    });

                    // et le texte
                    // textOTP.textContent = secretOTP;

                    // Afficher la pop-up
                    let pop = document.getElementsByClassName('popQRcode')[0];
                    if (pop) {
                        pop.style.display = 'flex';
                        document.body.classList.add('no-scroll');

                        let croix = document.getElementById("annulerQRcode");

                        if (croix) {
                            // Si on clique sur la croix lors de l'activation sans valider le code OTP, on met une pop up pour le signaler
                            croix.addEventListener("click", ouvrirPopupQuit);
                        }

                        // Boutons pour la pop up
                        let popUpQuitOTP = document.querySelector(".popUpQuitOTP");

                        let btnQuitOTP = document.querySelector(".btnQuit");
                        let btnValQuitOTP = document.querySelector(".btnValiderQuit");

                        if (btnQuitOTP) {
                            btnQuitOTP.addEventListener("click", () => {
                                popUpQuitOTP.style.display = "none";
                            });
                        }

                        if (btnValQuitOTP) {
                            btnValQuitOTP.addEventListener("click", () => {
                                popUpQuitOTP.style.display = "none";

                                let pop = document.getElementsByClassName('popQRcode')[0];
                                pop.style.display = 'none';
                                document.body.classList.remove('no-scroll');
                            });
                        }
                    }
                }
            },
            error: function() {
                alert('Erreur lors de la génération du QR Code.');
            }
        });
    });

    $(document).on("click", ".btnAffQRcode", function() {       // Si Authentikator est déjà activé
        $.ajax({
            url: '../composants/ajax/affichage_qrcode.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.qr_url) {
                    let qrDiv = document.getElementById("imgQRcode");
                    let textOTP = document.getElementById("textSecretOTP");

                    if (!qrDiv) {
                        console.error("Erreur : Élément imgQRcode introuvable !");
                        return;
                    }

                    secretOTP = response.secret;

                    // Vider l'ancien QR Code
                    $('#imgQRcode').html('');

                    // et le texte secret
                    // $('#textSecretOTP').textContent = "";

                    // Générer le QR Code
                    new QRCode(qrDiv, {
                        text: response.qr_url
                    });

                    // et le texte
                    // textOTP.textContent = response.secret;

                    // Afficher la pop-up
                    let pop = document.getElementsByClassName('popQRcode')[0];
                    if (pop) {
                        pop.style.display = 'flex';
                        document.body.classList.add('no-scroll');

                        let messInfo = document.querySelector("#qrcodeDiv ~ p");
                        messInfo.style.display = "none";

                        let aster = document.querySelector("#qrcodeDiv + div span");
                        aster.style.display = "none";

                        let croix = document.getElementById("annulerQRcode");

                        if (croix) {
                            // Si on clique sur la croix lors de l'affichage sans valider le code OTP, on ferme directement
                            croix.addEventListener("click", () => {
                                let pop = document.getElementsByClassName('popQRcode')[0];
                                pop.style.display = 'none';
                                document.body.classList.remove('no-scroll');
                            })
                        }
                    }
                }
            },
            error: function() {
                alert('Erreur lors de la génération du QR Code.');
            }
        });
    });
});


document.getElementById("copyButton").addEventListener("click", function() {
    navigator.clipboard.writeText(secretOTP).then(() => {
        let copyButton = document.getElementById("copyButton");
        let copyContainer = document.querySelector(".conteneur-copie");
        let okContainer = document.querySelector(".conteneur-ok");

        copyButton.classList.add("success");

        // Changer l'icône en check
        copyContainer.style.display = "none";
        okContainer.style.display = "flex";

        // Remettre l'icône et la couleur après 2 secondes
        setTimeout(() => {
            copyButton.classList.remove("success");
            copyContainer.style.display = "flex";
            okContainer.style.display = "none";
        }, 2000);
    });
});



// Formulaire de submition OTP
let otpInput = document.getElementById('codeOTP');
let errorMessage = document.getElementById('error-message');
let submitBtn = document.getElementById('submit-btn-otp');

if (otpInput) {
    otpInput.addEventListener('input', function(e) {
        let value = otpInput.value.replace(/\s/g, ''); // Supprime les espaces existants
        value = value.replace(/\D/g, ''); // Supprime tout sauf les chiffres

        if (value.length > 6) {
            value = value.slice(0, 6); // Limite à 6 chiffres
        }

        if (value.length > 3) {
            value = value.slice(0, 3) + ' ' + value.slice(3); // Ajoute l'espace après le 3e chiffre
        }

        otpInput.value = value;

        // Vérifier si l'OTP contient exactement 6 chiffres (sans espace)
        if (/^\d{6}$/.test(value.replace(/\s/g, ''))) {
            submitBtn.disabled = false;
            errorMessage.textContent = '';
        } else {
            submitBtn.disabled = true;
            errorMessage.textContent = 'Le code doit contenir 6 chiffres.';
        }
    });
}

if (submitBtn) {
    submitBtn.addEventListener('click', function() {
        const otpCode = otpInput.value.replace(/\s/g, ''); // Enlever les espaces avant d'envoyer
        console.log("Envoi AJAX - OTP :", otpCode);
        console.log("Envoi AJAX - Secret :", secretOTP);

        $.ajax({
            url: '../composants/ajax/verifier_otp_correct.php',
            type: 'POST',
            data: { otp: otpCode, secret: secretOTP },
            dataType: 'json',
            success: function(response) {
                console.log("Réponse JSON :", response.success);
        
                if (response.success) {     // Si le code OTP est correct on change 
                    
                    let btnAuth = document.getElementsByClassName('btnAuthent')[0];
                    
                    // Change le bouton activer en afficher
                    if (btnAuth) {
                        btnAuth.classList.remove("btnAuthent");
                        btnAuth.classList.add("btnAffQRcode");

                        let croix = document.getElementById("annulerQRcode");
                        croix.removeEventListener("click", ouvrirPopupQuit);    // On enlève la pop up de confirmation si le code OTP n'a pas été entré
                    }
                    
                    document.querySelector('.btnAffQRcode p').textContent = "Afficher Authentikator";
                    
                    secretOTP = "";
                    alert("code OTP correct");

                    // Fait disparaitre la pop up
                    let pop = document.getElementsByClassName('popQRcode')[0];
                    pop.style.display = 'none';
                    document.body.classList.remove('no-scroll');
                } else {
                    errorMessage.textContent = 'code OTP incorrect';
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Erreur AJAX :", textStatus, errorThrown);
                console.error("Réponse serveur brute :", jqXHR.responseText);
            }
        });
        
    });
}

