// Fonction d'affichage de la pop-ip
function validerCorrect() {
    let pop = document.getElementsByClassName('popUp');
    pop.style.display = 'flex';
    document.body.classList.add('no-scroll');

    // Après 5 secondes, cacher la popup et réactiver les interactions
    setTimeout(function() {
        pop.style.display = 'none';
        document.body.classList.remove('no-scroll');
        header("Location: https://tripskell.ventsdouest.dev/pages/accueil.php?user=pro");
        exit;
    }, 5000);
}