let btnConnexion = document.getElementById("btnConnexionForm");
let btnAnnulerOTP = document.getElementById("btnAnnulerOTP");
let btnConfirmOTP = document.getElementById("btnConfirmerOTP");
let overlayOTP = document.getElementById("overlayOTP");

if(btnConnexion != undefined)
{
    btnConnexion.addEventListener("click", ()=>{
        //supprime les cookies des pouces pour éviter qu'ils se conservent entre les comptes
        document.cookie = "poucesAvis=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;SameSite=Lax";
    })
}

btnAnnulerOTP.addEventListener("click", ()=>{
    overlayOTP.style.display = "none"
})


let form = document.forms[0];
let msgErreurOtp = document.getElementById("msgErreurOtp");
let userNameInput = document.getElementById("userName");

let otpInput = document.getElementById("userOTP");

form.addEventListener("submit", async (event) => {
    event.preventDefault(); // Empêche la soumission immédiate
    try {
        let besoinOTP = await otpActif(userNameInput.value);
        if (besoinOTP) {                                        // si l'utilisateur a activé la 2FA, ouvre la pop-up pour entrer l'OTP

            console.log("besoin !")
            overlayOTP.style.display = "flex"

        } else {
            console.log("pas besoin !");
            form.submit(); // Envoie le formulaire manuellement
        }
    } catch (error) {
        console.log("Erreur lors de la vérifiaction du besoin de l'OTP :", error);
    }
});

btnConfirmOTP.addEventListener("click", ()=>{validationConnexionOTP})


async function validationConnexionOTP()
{
    try{
        let valide = await otpValide(userNameInput.value, otpInput.value);
        console.log(valide)

        if(valide)
        {
            console.log("valide")
            form.submit()
        }
        else
        {
            console.log("invalide")
        }
    } catch(error) {
        console.log("erreur lors de la validation otp : ", error);
    }
}

function otpActif(login)               // renvoie true si le compte [login] a activé l'authentification à 2 facteurs (TOTP), false sinon
{
    console.log()
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "../composants/ajax/besoinOTP.php",
            type: 'POST',
            data: { login: login },
            success: function (response) {
                console.log("Réponse AJAX :", response);
                resolve(response == 1); // Vérifier si la réponse est bien "true"
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
            url: "../composants/ajax/otpValide.php",
            type: 'POST',
            data: { login: login, otp: otp },
            success: function (response) {
                console.log("Réponse AJAX :", response);
                resolve(response == 1); // Vérifier si la réponse est bien "true"
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Erreur AJAX :", textStatus, errorThrown);
                alert("Erreur lors de l'appel du script PHP : " + textStatus);
                reject(false);
            }
        });
    });
}


document.addEventListener("keydown", (event) => {
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