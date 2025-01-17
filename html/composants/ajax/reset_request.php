<?php
// VERIFICATION SI LE MOT DE PASSE A ETE OUBLIE
session_start();


// recuperation des parametre de connection a la BdD
include('../bdd/connection_params.php');

// cree $comptePro qui est true quand on est sur un compte pro et false sinon
include('../verif/verif_compte_pro.php');

// cree $compteMembre qui est true quand on est sur un compte pro et false sinon
include('../verif/verif_compte_membre.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['recupLogin'];  // Email saisi par l'utilisateur
    $email = $_POST['recupMail'];  // Email saisi par l'utilisateur

    try {
        // Connexion à la base de données
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Vérifier si l'email existe dans la base de données et correspond au login
        if ($comptePro) {
            // Préparation de la requête pour la table pro_prive
            $stmt = $dbh->prepare("SELECT id_c FROM tripskell.pro_prive WHERE adresse_mail = :email AND login = :loginU");
        
            // Liaison des paramètres
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':loginU', $login);
        
            // Exécution de la requête
            $stmt->execute();
            $user = $stmt->fetch();
        
            // Vérification du résultat
            if (count($user) === 0) {
                $proPrive = false;
                $proPublic = true;
        
                // Préparation de la requête pour la table pro_public
                $stmt = $dbh->prepare("SELECT id_c FROM tripskell.pro_public WHERE adresse_mail = :email AND login = :loginU");
        
                // Liaison des paramètres
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':loginU', $login);
        
                // Exécution de la requête
                $stmt->execute();
                $user = $stmt->fetch();
        
                // Vérification du résultat pour la table pro_public
                if (count($user) === 0) {
                    $proPublic = false;
                }
            } else {
                $proPrive = true;
            }
        } else {
            $stmt = $dbh->prepare('SELECT id_c FROM tripskell.membre WHERE adresse_mail = :email AND login = :loginU');
            
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':loginU', $login);   
            
            $stmt->execute();

            $user = $stmt->fetch();
        }

             


        if ($user) {
            // Générer un token unique et une expiration de 1 heure
            $token = bin2hex(random_bytes(16));  // Génère un token aléatoire
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));  // Expiration dans 1 heure

            // Mettre à jour le token et l'expiration dans la base de données
            if ($comptePro) {
                if ($proPrive) {
                    $stmt = $dbh->prepare('UPDATE tripskell.pro_prive SET token = :token, date_expiration = :expiry WHERE adresse_mail = :email');
                } else if ($proPublic) {
                    $stmt = $dbh->prepare('UPDATE tripskell.pro_public SET token = :token, date_expiration = :expiry WHERE adresse_mail = :email');
                } 
            } else {
                $stmt = $dbh->prepare('UPDATE tripskell.membre SET token = :token, date_expiration = :expiry WHERE adresse_mail = :email');
            }
            
            $stmt->execute([
                'token' => $token,
                'expiry' => $expiry,
                'email' => $email,
            ]);

            // Envoi du lien de réinitialisation par email
            $resetLink = "./reset_password.php?token=$token";  // URL avec token

            // Envoi de l'email (vous devrez configurer un serveur SMTP pour cela)
            mail($email, "Réinitialisation de votre mot de passe", "Cliquez sur ce lien pour réinitialiser votre mot de passe : $resetLink");
?>
            <script>
                alert("Un email de réinitialisation a été envoyé si cet email existe dans notre base de données.");
            </script>
<?php
        } else {
?>
            <script>
                alert("L'email saisi n'est pas associé à un compte.");
            </script>
<?php
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>