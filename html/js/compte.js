let btnDetailPrix = document.getElementsByClassName("btnDetailPrix");
let detailPrixDeplie = document.getElementsByClassName("detailPrixDeplie");

btnDetailPrix.addEventListener('click', () => {
    detailPrixDeplie.classList.toggle('displayNone');
});

function confModif() {
    let pop = document.querySelector('.popUpModif');
    pop.style.display = 'flex';
    document.body.classList.add('no-scroll');
}


/*
<script>
// Mot de passe correct
const correctPassword = <?php echo $infos['mot_de_passe'];?> ;
</script>
*/