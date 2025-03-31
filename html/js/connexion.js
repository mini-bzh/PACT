let btnConnexion = document.getElementById("btnConnexionForm");
let btnAnnulerOTP = document.getElementById("btnAnnulerOTP");
let btnConfirmOTP = document.getElementById("btnConfirmerOTP");
let overlayOTP = document.getElementById("overlayOTP");

let loaderOTP = btnConfirmOTP.querySelector(".loader")
let textBtnConfirmer = btnConfirmOTP.querySelector("p")

let loaderCon = btnConnexion.querySelector(".loader")
let textBtnConnexion = btnConnexion.querySelector("p")

let texteErreurOTP = document.getElementById("texteErreurOTP");

if(btnConnexion != undefined)
{
    btnConnexion.addEventListener("click", ()=>{
        //supprime les cookies des pouces pour éviter qu'ils se conservent entre les comptes
        document.cookie = "poucesAvis=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;SameSite=Lax";
        //btnConnexion.disabled = "true"
    })
}

btnAnnulerOTP.addEventListener("click", ()=>{
    overlayOTP.style.display = "none"
})


let form = document.forms[0];
let msgErreurOtp = document.getElementById("msgErreurOtp");
let userNameInput = document.getElementById("userName");

let otpInput = document.getElementById("userOTP");

form.addEventListener("submit", async (event) => {                  // lorsque le formulaire de connexion est soumis
    event.preventDefault(); // Empêche la soumission immédiate
    try {
        let besoinOTP = await otpActif(userNameInput.value);
        if (besoinOTP) {                                        // si l'utilisateur a activé la 2FA, ouvre la pop-up pour entrer l'OTP

            overlayOTP.style.display = "flex"                   // affiche la pop-up de demande d'OTP

        } else {
            form.submit(); // Envoie le formulaire manuellement
        }
    } catch (error) {
        console.log("Erreur lors de la vérifiaction du besoin de l'OTP :", error);
    }
});


btnConfirmOTP.addEventListener("click", validationConnexionOTP)


async function validationConnexionOTP()                             // vérifie si l'OTP entré est correct, et soumet le formulaire si c'est le cas
{
    try{
        textBtnConfirmer.style.display = "none"                     // désactive le bouton le temps de la réponse d'ajax
        loader.style.display = "inline-block"
        btnConfirmOTP.disabled = true
        btnAnnulerOTP.disabled = true

        let valide = await otpValide(userNameInput.value, otpInput.value);

        textBtnConfirmer.style.display = "block"                    // réactive le bouton après la réponse d'ajax
        loader.style.display = "none"
        btnConfirmOTP.disabled = false
        btnAnnulerOTP.disabled = false

        if(valide)
        {
            form.submit()           // soumet le formulaire
        }
        else
        {
            texteErreurOTP.style.display = "flex"           // affiche un message d'erreur
        }
    } catch(error) {
        console.log("erreur lors de la validation otp : ", error);
    }
}

function otpActif(login)               // renvoie true si le compte [login] a activé l'authentification à 2 facteurs (TOTP), false sinon
{
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "../composants/ajax/besoinOTP.php",                // script qui va consulter la BDD
            type: 'POST',
            data: { login: login },
            success: function (response) {
                console.log("Réponse AJAX :", response);
                resolve(response == 1);                         // Vérifier si la réponse est bien "true"
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Erreur AJAX :", textStatus, errorThrown);
                alert("Erreur lors de l'appel du script PHP : " + textStatus);
                reject(false);
            }
        });
    });
}

function otpValide(login, otp) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "../composants/ajax/otpValide.php",                    // script qui va consulter la BDD
            type: 'POST',
            data: { login: login, otp: otp },
            success: function (response) {
                console.log("Réponse AJAX :", response);
                resolve(response == 1);                         // Vérifier si la réponse est bien "true"
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Erreur AJAX :", textStatus, errorThrown);
                alert("Erreur lors de l'appel du script PHP : " + textStatus);
                reject(false);
            }
        });
    });
}

/* raccourcis clavier */
document.addEventListener("keydown", (event) => {
    texteErreurOTP.style.display = "none"
    if(overlayOTP.style.display == "flex")
    {
        if(event.key === "Escape")
        {
            overlayOTP.style.display = "none"
        }
        if(event.key === "Enter")
        {
            validationConnexionOTP()
        }  
    }
})