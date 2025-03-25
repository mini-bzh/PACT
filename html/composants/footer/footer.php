<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page avec Footer Fixé</title>
    <link rel="stylesheet" href="styles.css"> <!-- Assurez-vous de lier votre fichier CSS -->
</head>
<body>

    <footer class="footer">
        <div class="footer-container">
            
            <div class="footer-section">
                <h4>A propos</h4>
                <p>Nous sommes une entreprise dédiée à offrir les meilleurs services à nos clients. Notre mission est de garantir la satisfaction et l'excellence à chaque étape.</p>
            </div>
            
            <div class="footer-section">
                <h4>Liens utiles</h4>
                <ul>
                    <li><a href="accueil.php">Accueil</a></li>
                    <li><a href="recherche.php">Recherche</a></li>
                    <li><a href="avis.php">Avis</a></li>
                    <li><a href="<?php
                        if($user)
                        {
                            echo 'compte.php';              
                        } else {
                            echo 'connexion.php';
                        }
                    ?>">Profil</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Contact</h4>
                <p> 12 Rue de l'alma , Rennes, Bretagne</p>
                <p> +33 1 23 45 67 89</p>
            </div>
            
            <div class="footer-section">
                <h4>Suivez-nous</h4>
                <a href="https://www.facebook.com"> <img src="/images/Réseaux/facebook.png" alt="logo facebook"/> </a>
                <a href="https://www.instagram.com"> <img src="/images/Réseaux/instagram.png"  alt="logo instagram"/> </a>
                <a href="https://www.youtube.com"><img src="/images/Réseaux/youtube.png"  alt="logo youtube"/> </a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2024 PACT. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
