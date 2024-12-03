

let btnDetailPrix = document.getElementsByClassName("btnDetailPrix");
let detailPrixDeplie = document.getElementsByClassName("detailPrixDeplie");

btnDetailPrix.addEventListener('click', () => {
    detailPrixDeplie.classList.toggle('displayNone');
});
function verifyPassword(event) {
    // Empêche le comportement par défaut du formulaire
    event.preventDefault();
    
    let inputPassword = document.getElementById('password').value;
    let errorMessage = document.getElementById('error-message');
    let popup = document.querySelector('.popUpModif');

    // Récupérer le mot de passe réel à partir de l'attribut data
    let RealPassword = document.getElementById('data-container').getAttribute('data-value');
    
    alert("Mot de passe correct récupéré : " + RealPassword);
    alert("Mot de passe saisi par l'utilisateur : " + inputPassword);
    
    if (inputPassword === RealPassword) {
        // Si le mot de passe est correct, cacher la popup
        popup.style.display = "none";
        document.body.classList.remove('no-scroll');
        window.location.href = "modifComptemembre.php";
    } else {
        // Afficher un message d'erreur si le mot de passe est incorrect
        errorMessage.style.display = "block";
    }
}

function confModif() {
    let pop = document.querySelector('.popUpModif');
    pop.style.display = 'flex';
    document.body.classList.add('no-scroll');
}

// Ajoute un écouteur d'événement au formulaire
document.querySelector('#popup-content form').addEventListener('submit', verifyPassword);

/*
<script>
// Mot de passe correct
const correctPassword = <?php echo $infos['mot_de_passe'];?> ;
</script>
*/