let btnConnexion =document.querySelector(".btnConnexion");

if(btnConnexion != undefined)
{
    btnConnexion.addEventListener("click", ()=>{
        //supprime les cookies des pouces pour éviter qu'ils se conservent entre les comptes
        document.cookie = "poucesAvis=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;SameSite=Lax";
    })
}

let form = document.forms[0];
let msgErreurOtp = document.getElementById("msgErreurOtp");
let userNameInput = document.getElementById("userName");
let otpInput = document.getElementById("userOTP");

if (document.querySelectorAll("form input").length === 3) {
    form.addEventListener("submit", async (event) => {
        event.preventDefault(); // Empêche la soumission immédiate

        try {
            let valide = await otpValide(userNameInput.value, otpInput.value);
            if (!valide) {
                msgErreurOtp.style.display = "block";
            } else {
                console.log("Connexion réussie !");
                form.submit(); // Envoie le formulaire manuellement
            }
        } catch (error) {
            console.log("Erreur lors de la validation OTP :", error);
        }
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
