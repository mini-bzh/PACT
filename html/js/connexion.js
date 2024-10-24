// Fonction d'affichage de la pop-up
function validerCorrect() {
    let pop = document.querySelector('.popUp');
    pop.style.display = 'flex';
    document.body.classList.add('no-scroll');

    // Après 5 secondes, cacher la popup et réactiver les interactions
    setTimeout(function() {
        pop.style.display = 'none';
        document.body.classList.remove('no-scroll');
        window.location.href = "https://tripskell.ventsdouest.dev/pages/accueil.php";
    }, 1500);
}