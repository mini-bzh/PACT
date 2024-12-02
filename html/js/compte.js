

let btnDetailPrix = document.getElementsByClassName("btnDetailPrix");
let detailPrixDeplie = document.getElementsByClassName("detailPrixDeplie");

btnDetailPrix.addEventListener('click', () => {
    detailPrixDeplie.classList.toggle('displayNone');
});

/*
<script>
// Mot de passe correct
const correctPassword = <?php echo $infos['mot_de_passe'];?> ;

document.getElementById("trigger-modifier").addEventListener("click", () => {
// Affiche la popup de saisie du mot de passe
document.getElementById("popup").style.display = "block";
});

function verifyPassword() {
const inputPassword = document.getElementById('password').value;
const errorMessage = document.getElementById('error-message');
const popup = document.getElementById('popup');

// Comparer le mot de passe entré avec celui qui est récupéré du serveur
const correctPassword = "<?php echo $infos['mot_de_passe']; ?>"; // Passer la variable PHP au JavaScript

if (inputPassword === correctPassword) {
// Si le mot de passe est correct, on cache la popup
popup.style.display = "none";
window.location.href = "modifComptemembre.php";
} else {
// Affiche un message d'erreur si le mot de passe est incorrect
errorMessage.style.display = "block";
}
}
</script>

*/