// Fonction d'affichage de la pop-ip
function validerCorrect() {
    let pop = document.getElementsByClassName('popUp');
    pop.style.display = 'flex';

    // Après 5 secondes, cacher la popup et réactiver les interactions
    setTimeout(function() {
        pop.style.display = 'none';
        header("Location: https://tripskell.ventsdouest.dev/pages/accueil.php?user=pro");
        exit;
    }, 5000);
}