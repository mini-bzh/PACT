<!-- VERIFICATION SI LE MOT DE PASSE A ETE OUBLIE -->
<?php

session_start();

// recuperation des parametre de connection a la BdD
include('./connection_params.php');

// cree $comptePro qui est true quand on est sur un compte pro et false sinon
include('./verif_compte_pro.php');

// cree $compteMembre qui est true quand on est sur un compte pro et false sinon
include('./verif_compte_membre.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['recupLogin'];  // Email saisi par l'utilisateur
    $email = $_POST['recupMail'];  // Email saisi par l'utilisateur

    try {
        // Connexion à la base de données
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Vérifier si l'email existe dans la base de données et correspond au login
        print_r("login : $login mail : $email");
        if($comptePro){
            $stmt = $dbh->prepare("SELECT id_c FROM tripskell.pro_prive WHERE adresse_mail = :email AND login=:loginU");

            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':loginU', $login);     
    
            $stmt->execute();
            $user = $stmt->fetch();
            
            
            if (count($user) === 0) {
                
                $proPrive = false;
                $proPublic = true;

                $stmt2 = $dbh->prepare('SELECT id_c FROM tripskell.pro_public WHERE adresse_mail = :email AND login = :loginU');
    
                $stmt2->bindValue(':email', $email);
                $stmt2->bindValue(':loginU', $login);    
    
                $stmt2->execute();
                $user = $stmt2->fetch();

                if (count($user) === 0) {
                    $proPublic = false;
                } else{
                    print_r("ok");
                }
    
            } else {
                $proPrive = true;
                print_r("ok");
            }
        } else {
            $stmt3 = $dbh->prepare('SELECT id_c FROM tripskell.membre WHERE adresse_mail = :email AND login = :loginU');
            $stmt3->execute();
            
            $stmt3->bindValue(':email', $email);
            $stmt3->bindValue(':loginU', $login);    

            $user = $stmt3->fetch();
            print_r("ok");
        }

             


        if ($user) {
            // Générer un token unique et une expiration de 1 heure
            $token = bin2hex(random_bytes(16));  // Génère un token aléatoire
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));  // Expiration dans 1 heure

            // Mettre à jour le token et l'expiration dans la base de données
            $stmt = $dbh->prepare('UPDATE users SET reset_token = :token, reset_token_expiry = :expiry WHERE adresse_mail = :email');
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