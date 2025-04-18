<?php

use Dompdf\Dompdf;

session_start(); // recuperation de la sessions

// recuperation des parametre de connection a la BdD
include('../composants/bdd/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

// cree $comptePro qui est true quand on est sur un compte pro et false sinon
include('../composants/verif/verif_compte_pro.php');

// cree $compteMembre qui est true quand on est sur un compte pro et false sinon
include('../composants/verif/verif_compte_membre.php');

// On va récupérer ici l'identifiant id_c présent dans les vues pro.


if (array_key_exists("idCompte", $_SESSION)) {
    $idCompte = $_SESSION['idCompte'];
}


if (isset($idCompte)) {
    if ($comptePro) {
        if (key_exists("idCompte", $_SESSION)) {
            // Récupération de id_c de pro_prive
            $idpropriveResult = $dbh->query("select id_c from tripskell.pro_prive where id_c=" . $_SESSION["idCompte"] . ";")->fetchAll();

            if (count($idpropriveResult) > 0) {
                $idproprive = $idpropriveResult[0];
            } else {
                // Si aucun résultat n'est trouvé, vous pouvez gérer cette situation ici
                $idproprive = null;  // Par exemple, on définit $idproprive comme null
            }

            if (!isset($idproprive)) {
                // Récupération de id_c de pro_public si pro_prive n'a pas donné de résultat
                $idpropublicResult = $dbh->query("select id_c from tripskell.pro_public where id_c=" . $_SESSION["idCompte"] . ";")->fetchAll();

                if (count($idpropublicResult) > 0) {
                    $idpropublic = $idpropublicResult[0];
                } else {
                    // Si aucun résultat n'est trouvé ici aussi, vous pouvez définir $idpropublic comme null
                    $idpropublic = null;
                }
            }
        }

        $stmt = $dbh->prepare("SELECT * from tripskell.pro_prive where id_c = :id");

        $stmt->bindParam(':id', $idCompte, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetchAll();


        if (count($result) === 0) {

            $stmt = $dbh->prepare("SELECT * from tripskell.pro_public where id_c = :id");

            $stmt->bindParam(':id', $idCompte, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetchAll();
        }
    } else {

        $stmt = $dbh->prepare("SELECT * from tripskell.membre where id_c = :id");

        $stmt->bindParam(':id', $idCompte, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetchAll();
    }

    $infos = $result[0];

    if (isset($idproprive)) {
        if ((isset($_POST['password']))) {
            $password = $_POST['password'];
            $realpassword = $infos['mot_de_passe'];
            if ($password == $realpassword) {
                header("Location: ModifCompteProPrive.php");
            }
        }
    } elseif (isset($idpropublic)) {
        if ((isset($_POST['password']))) {
            $password = $_POST['password'];
            $realpassword = $infos['mot_de_passe'];
            if ($password == $realpassword) {
                header("Location: ModifCompteProPublic.php");
            }
        }
    } else {
        if ((isset($_POST['password']))) {
            $password = $_POST['password'];
            $realpassword = $infos['mot_de_passe'];
            if ($password == $realpassword) {
                header("Location: ModifComptemembre.php");
            } else {
            ?>
                <script>
                    alert("Le mot de passe saisi est incorrect!");
                </script>
<?php
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/pages/compte.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script src="../js/deconnexion.js" defer></script>
    <script src="../js/suppressionCompte.js" defer></script>
    <script src="../js/menuDeroulant.js" defer></script>
<?php
    if (isset($idCompte)) {
?>
    <script>let id_c = <?php echo $idCompte;?>; // donne l'id_c au javascript</script>
<?php
    }
?>
    <script src="../js/compte.js" defer></script>
</head>


<body class=<?php echo ($comptePro) ? "fondPro" : "fondVisiteur"; //met le bon fond en fonction de l'utilisateur 
            ?>>


    <!------ HEADER  ------>
    <?php
    include "../composants/header/header.php";
    ?>

<header>
            <div class="titrePortable">
                    <!--<img src="/images/logo/logo_grand.png" alt="logo grand" id="logoTitreMobile">-->
                    <img src="/images/logo/logo_petit.png" alt="logo petit" id="logoTitreMobile">
                    <?php
                    if (($comptePro) || ($compteMembre)) {
                    ?>
                        <h1>Profil</h1>
                    <?php
                    } else {
                    ?>
                        <h1>Connexion</h1>
                    <?php } ?>
        </header>

    <!-- SI C'EST UN VISITEUR !!! -->
    <?php

    if ((!$comptePro) && (!$compteMembre)) {
    ?>

        <!------ MAIN  ------>
        <main id="mainCompte">
            <p class="texteLarge">Veuillez sélectionner une option de connexion</p>
            <div>
                <?php
                // boutons pour choisir de ce connecter en tant que membre ou
                include '../composants/btnConnexion/btnCoMembre.php';
                include '../composants/btnConnexion/btnCoPro.php';
                ?>
            </div>
            <div>
                <h4>Pas encore de compte ?</h4>
                <a href="ChoixCreationCompte.php">
                    <p>Créez le !</p>
                </a>
            </div>
        </main>

    <?php
    } else {
    ?>

        <!------ MAIN  ------>
        <main>
            <!-- div principale -->
            <div class="informationsCompte">

                <!-- div des informations de compte -->
                <div class="zoneInfos">

                    <!-- div de l'image et de l'identité -->
                    <div class="infoId">
                        <div class="image-container">
                            <img class="circular-image" src="../images/pdp/<?php echo $infos['pdp'] ?>" alt="Photo de profil" title="Photo de profil">
                        </div>
                        <?php
                        // On récupère la date au bon format de la date de création d'une offre
                        // On récupère la date
                        $dateString = $infos["date_crea_compte"];

                        // On créer un objet DateTime à partir de la chaîne
                        $date = new DateTime($dateString);

                        // On formater la date au format dd/mm/aaaa
                        $formattedDate = $date->format('d/m/Y');

                        // On change le mdp en étoile
                        $cache = str_repeat("*", strlen($infos["mot_de_passe"])); // On créer une variable qui possède autant de "*" que le nombre de lettres du mdp

                        // On rajoute un espace entre 2 caractères pour le téléphone
                        if ($infos["numero_tel"] != "") {
                            $tel = trim(chunk_split($infos["numero_tel"], 2, " "));
                        }

                        // On récupère le type du compte
                        //Si c'est un membre, on met membre
                        if ($compteMembre) {
                            $tCompte = "Membre";
                            // Si c'est un professionnel
                        } else {
                            // On regarde si c'est un professionnel privé
                            if (isset($dbh->query("select id_c from tripskell.pro_prive where id_c='" . $_SESSION["idCompte"] . "';")->fetchAll()[0])) {
                                $tCompte = "Professionnel privé";
                                // Sinon c'est un professionnel public
                            } else {
                                $tCompte = "Professionnel public";
                            }


                            // Dans le cas d'un compte pro, on décompose son numéro SIREN
                            if (isset($infos["num_siren"])) {
                                $siren = trim(chunk_split($infos["num_siren"], 3, " ")); // On ajoute un esapce entre 3 caractères
                            }

                            // On concatène les informations de l'adresse d'un pro
                            if (($infos["numero"] != "") && ($infos["rue"] != "") && ($infos["ville"] != "") && ($infos["codepostal"] != "")) {
                                $adrPro = $infos["numero"] . " " . $infos["rue"] . ", " . $infos["ville"] . " " . $infos["codepostal"];
                            }
                        }

                        if ($compteMembre) {  // Si c'est un membre on affiche son nom / prénom / login
                        ?>
                            <div class="infoPrinc">
                                <h3 class="boldArchivo titreLogin"><?php echo $infos["login"] ?></h3>
                                <div>
                                    <p class="resizeHide"><span class="boldArchivo">Nom : </span><?php echo $infos["nom"] ?></p>
                                    <p class="resizeHide"><span class="boldArchivo">Prenom : </span><?php echo $infos["prenom"] ?></p>
                                    <p class="texteSmall resizeShow">Création compte : <?php echo $formattedDate ?></p>
                                    <p class="texteSmall resizeShow">Type de compte : <?php echo $tCompte ?></p>
                                </div>
                            </div>
                        <?php
                        } else {
                        ?>
                            <div class="infoPrinc">
                                <p class="boldArchivo titreLogin"><?php echo $infos["raison_social"] ?></p>

                                <div>
                                    <?php
                                    if (isset($siren)) {
                                    ?>
                                        <p class="resizeHide"><span class="boldArchivo">Numéro SIREN : </span><?php echo $siren ?></p>
                                    <?php
                                    }
                                    ?>
                                    <p class="texteSmall resizeShow">Création compte : <?php echo $formattedDate ?></p>
                                    <p class="texteSmall resizeShow">Type de compte :<br><?php echo $tCompte ?></p>
                                </div>
                            </div>
                        <?php
                        }
                        ?>

                        <button class="resizeShow btnDeplie ">
                            <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M75 62.5L50 37.5L25 62.5" stroke="white" stroke-width="7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <svg class="fleche2 displayNone" width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M75 62.5L50 37.5L25 62.5" stroke="white" stroke-width="7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                    </div>

                    <!-- div du reste des informations en format portable -->
                    <div class="displayNone" id="menuDeroule">
                        <div class="zoneInfosPort">
                            <?php
                            if ($compteMembre) {  // Si c'est un membre on affiche son nom / prénom
                            ?>
                                <div>
                                    <p class="boldArchivo displayNone">Nom : <?php echo $infos["nom"] ?></p>
                                    <p class="boldArchivo displayNone">Prenom : <?php echo $infos["prenom"] ?></p>
                                </div>
                                <?php
                            } else {  // Si c'est un pro, son numéro SIREN
                                if (isset($siren)) {
                                ?>
                                    <div>
                                        <p class="boldArchivo displayNone">Numéro SIREN : <?php echo $siren ?></p>
                                    </div>
                            <?php
                                }
                            }
                            ?>

                            <!-- Mot de passe -->
                            <p class="boldArchivo displayNone">Mot de passe : <?php echo $cache ?></p>
                            <?php
                            if (isset($tel)) {
                            ?>
                                <!-- Téléphone -->
                                <p class="boldArchivo displayNone">Téléphone : <?php echo $tel ?></p>
                            <?php
                            }
                            ?>

                            <!-- E-mail -->
                            <p class="boldArchivo displayNone">Adresse mail :<br><?php echo $infos["adresse_mail"] ?></p>


                            <!-- Adresse -->
                            <?php
                            if ($compteMembre) {
                            ?>
                                <p class="boldArchivo displayNone">Adresse postal : <?php echo $infos["codepostal"] ?></p>
                                <?php
                            } else {
                                if (isset($adrPro)) {
                                ?>
                                    <p class="boldArchivo displayNone">Adresse :<br><?php echo $adrPro ?></p>
                            <?php
                                }
                            }
                            ?>
                        </div>
                        <!-- div des boutons -->
                        <div class="zoneBtnPort">
                            <!-- Bouton de modification portable -->
                            <button class="btnModifPort displayNone btn" onclick="confModifProfil()">
                                <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M88.2249 28.3831C90.4279 26.1807 91.6657 23.1934 91.6661 20.0784C91.6665 16.9633 90.4294 13.9757 88.227 11.7727C86.0246 9.56976 83.0373 8.33193 79.9222 8.33154C76.8071 8.33115 73.8195 9.56823 71.6166 11.7706L16.0082 67.3915C15.0408 68.3561 14.3254 69.5437 13.9249 70.8498L8.42072 88.9831C8.31304 89.3435 8.3049 89.7263 8.39719 90.0909C8.48947 90.4554 8.67872 90.7883 8.94487 91.054C9.21102 91.3197 9.54414 91.5084 9.90888 91.6001C10.2736 91.6918 10.6564 91.6831 11.0166 91.5748L29.1541 86.0748C30.4589 85.6779 31.6464 84.9669 32.6124 84.004L88.2249 28.3831Z" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M62.5 20.8335L79.1667 37.5002" stroke="black" stroke-width="7" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="boldArchivo displayNone">Modifier les informations</p>
                            </button>

                            <!-- Bouton de supression compte portable -->
                            <!-- <button class="btnSupPort btn displayNone">
                    <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.5 25H87.5" stroke="black" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M79.1666 25V83.3333C79.1666 87.5 74.9999 91.6667 70.8333 91.6667H29.1666C24.9999 91.6667 20.8333 87.5 20.8333 83.3333V25" stroke="black" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M33.3333 24.9999V16.6666C33.3333 12.4999 37.4999 8.33325 41.6666 8.33325H58.3333C62.4999 8.33325 66.6666 12.4999 66.6666 16.6666V24.9999" stroke="black" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M41.6667 45.8333V70.8333" stroke="black" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M58.3333 45.8333V70.8333" stroke="black" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <p class="boldArchivo displayNone">Supprimer le compte</p>
                </button> -->
                        </div>

                    </div>

                    <!-- div du reste des informations en format desktop -->
                    <div class="resteInfos">

                        <p><span class="boldArchivo">Création compte : </span><?php echo $formattedDate ?></p>

                        <p><span class="boldArchivo">Type de compte : </span><?php echo $tCompte ?></p>

                        <!-- Email -->
                        <p><span class="boldArchivo">E-mail : </span><?php echo $infos["adresse_mail"] ?></p>

                        <!-- Adresse -->
                        <?php
                        if ($compteMembre) {
                        ?>
                            <p><span class="boldArchivo">Adresse postal : </span><?php echo $infos["codepostal"] ?></p>
                            <?php
                        } else {
                            if (isset($adrPro)) {
                            ?>
                                <p><span class="boldArchivo">Adresse : </span><?php echo $adrPro ?></p>
                            <?php
                            }
                        }

                        if (isset($tel)) {
                            ?>
                            <!-- Téléphone -->

                            <p><span class="boldArchivo">Téléphone : </span><?php echo $tel ?></p>
                        <?php
                        }
                        ?>

                        <!-- Mot de passe (caché) -->

                        <p><span class="boldArchivo">Mot de passe : </span><?php echo $cache ?></p>

                    </div>

                </div>


                <!-- div des boutons de compte -->
                <div class="zoneBtn">

                    <!-- div des boutons de consultation / modification données de compte-->
                    <div>
                        <!-- Bouton de modification -->
                        <button class="btnModifCompte btn" onclick="confModifProfil()">
                            <?php
                            include '../icones/modifierSVG.svg';
                            ?>
                            <p class="boldArchivo texteSmall">Modifier le profil</p>
                        </button>



                        <!-- Bouton de données -->
                        <!-- <button class="btnDataCompte">
            <?php
            //include '../icones/databaseSVG.svg';
            ?>
                <p class="boldArchivo texteSmall">Télécharger les données du compte</p>
            </button> -->

                        <?php
                        // On affiche le bouton de données bancaires si c'est un pro
                        if ($comptePro) {
                        ?>

            <!-- Bouton de données bancaires -->
            <!--<a href="ModifInfoBancaire.php">-->
            <?php if (isset($idproprive)){?>
            <button class="btnDataBanc btn"  onclick="confModifBanc()">
            <?php
                 include '../icones/creditCardSVG.svg';
            ?>
                <p class="boldArchivo texteSmall">Modifier les informations bancaires</p>
            </button>
            <?php } ?>
            <!--</a>-->


                        <?php
                        }
                        ?>

                        <?php

                        // On affiche le bouton de données bancaires si c'est un pro
                        if (isset($idproprive)) {
                        ?>
                            <!-- <a href="listeFacture.php"> -->
                            <!-- Bouton direction page facture -->
                            <button class="btnFacture btn" onclick="goToListeFacture()">

                                <?php
                                include '../icones/infoSVG.svg';
                                ?>
                                <p class="boldArchivo texteSmall">Gérer mes factures</p>

                            </button>
                            <!-- </a> -->
                        <?php
                        }
                        ?>

                        <!-- On affiche le bouton qui génère des clés API -->
                        <button class="btnCreaAPI btn">
                            <?php
                                    include '../icones/APIkey.svg';
                            ?>
                            <p class="boldArchivo texteSmall">Génerer une clé API</p>
                        </button>

<?php
            $stmt = $dbh->prepare("SELECT secretotp from tripskell._compte where id_c = :id");

            $stmt->bindParam(':id', $_SESSION["idCompte"], PDO::PARAM_STR);
        
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['secretotp'] !== null) {
                $cleSecrete = true;
            } else {
                $cleSecrete = false;
            }
?>

                        <!-- On affiche le bouton qui génère / affiche le QRcode authentikator  -->
                        <button class="btn 
                        <?php if ($cleSecrete) { ?> btnAffQRcode <?php } else { ?> btnAuthent <?php } ?>
                        ">
                        
                            <?php
                                    include '../icones/qr-code.svg';
                            ?>
                            <p class="boldArchivo texteSmall"><?php if ($cleSecrete) { ?> Afficher <?php } else { ?> Activer <?php } ?> Authentikator</p>
                        </button>

                    </div>

                    <!-- Clé API -->
                    <div class="générationAPI">
                        
                    <strong><p id="apiKeyTexte"></p></strong>
                    <p id="apiKey"></p>
                    </div>

                    <!-- div des boutons dangereux -->
                    <div>

                        <!-- Bouton de deconnexion -->
                        <button id="btnDeconnexion" class="btn" onclick="confDeco()">
<?php
                            include '../icones/deconnexionSVG.svg';
?>
                            <p class="boldArchivo texteSmall">Déconnexion</p>
                        </button>

<?php
                    if ($compteMembre) {
?>
                        <!-- Bouton de suppression compte -->
                        <button id="btnSupCompte" class="btn" onclick="confSup()">
<?php
                            include '../icones/supprimerSVG.svg';
?>
                            <p class="boldArchivo texteSmall">Supprimer le compte</p>
                        </button>
<?php
                    }
?>

                    </div>

                </div>

            </div>



            <!-- POP-UP d'Authentikator -->
            <div class="popQRcode popUp">
                <div class="popup-content">
                    <div id="annulerQRcode">
<?php
                       include '../icones/croixSVG.svg';
?>
                    </div>
                    <div>
                        <div id="qrcodeDiv">
                            <div id="imgQRcode"></div>
                            <button class="copy-btn" id="copyButton">
                                <p>Secret OTP</p>
                                <div class="separateur"></div>
                                <div class="conteneur-copie">
<?php
                                    include '../icones/copierSVG.svg';
?>
                                </div>
                                <div class="conteneur-ok" style="display: none;">
<?php
                                    include '../icones/okSVG.svg';
?>
                                </div>
                            </button>
                            <!-- <p id="textSecretOTP"></p> -->
                        </div>
                        <div>
                            <label for="codeOTP">Vérifier le code OTP<span> *</span> :</label>
                            <input id="codeOTP" class="otp-input" name="codeOTP" type="text" placeholder="--- ---">
                            <p id="error-message" class="texteSmall"></p>
                            <button id="submit-btn-otp" disabled>Vérifier</button>
                        </div>
                        <p>* Obligatoire uniquement lors de l'activation</p>
                    </div>
                </div>
            </div>

            <!-- POP-UP confirmation quitter sans OTP -->
            <div class="popUpQuitOTP popUp">
                <div class="popup-content">
                    <p>Êtes vous sur de vouloir vous fermer la fenêtre sans valider le code OTP ?<br><span>Le QRcode partagé ne sera plus valide après ça.</span></p>
                    <div>
                        <button class="btnQuit">Non</button>
                        <button class="btnValiderQuit">Oui</button>
                    </div>
                </div>
            </div>

            <!-- POP-UP de deconnexion -->
            <div class="popUpDeco popUp">
                <div class="popup-content">
                    <p>Êtes vous sur de vouloir vous déconnecter ?</p>
                    <div>
                        <button class="btnAnnulerDeco" onclick="fermeConfDeco()">Non</button>
                        <button class="btnValiderDeco" onclick="deconnexion()">OK</button>
                    </div>
                </div>
            </div>

            <!-- POP-UP de suppression de compte -->
            <div class="popUpSup popUp">
                <div class="popup-content">
                    <p class="ajoutBorder">Pour valider la suppression du compte veuillez entrer votre mot de passe :</p>

                    <!-- <div class="popup-align">
                        <label for="nomUserSupCompte">Nom d'utilisateur :</label>
                        <input id="nomUserSupCompte" name="nomUserSupCompte" type="text" placeholder="Nom d'utilisateur">
                    </div> -->

                    <div class="popup-align">
                        <label for="pswSupCompte">Mot de passe :</label>
                        <input id="pswSupCompte" name="pswSupCompte" type="password" placeholder="Mot de passe">
                    </div>
                    <p id="textNonValide" class="displayNone texteSmall remplirChampsError" style="color: red">Mot de passe incorrect !</p>
                    <p class="boldArchivo" style="color: red">Cette action est irréversible !</p>
                    <div class="btnSup">
                        <button class="btnValiderSup" onclick="suppressionCompte()" disabled>
                            Confirmer
<?php
                            include '../icones/supprimerSVG.svg';
?>
                        </button>
                        <button class="btnAnnulerSup" onclick="fermeConfSup()">
                            Annuler
<?php
                            include '../icones/croixSVG.svg';
?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pop-up modif profil -->
            <div id="popUpModif" class="popUp">
                <div class="popup-content">
                    <div id="bn-modif-exit" class="button">
                        
                        <svg viewBox="0 0 89 81" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 10 L79 71 M79 10 L10 71" stroke="black" stroke-width="5" stroke-linecap="round"/>
                        </svg>

                    </div>
                    <form method="post" action="">
                        <label for="password">Mot de passe :</label>
                        <input type="password" id="password" name="password" placeholder="Mot de passe" />
                        <button class="btnValider" type="submit">Valider</button>
                    </form>
                </div>
            </div>

            <!-- Pop-up modif bancaire -->
            <div id="popUpModifBancaire" class="popUp">
                <div class="popup-content">
                    <div id="bn-modifBanc-exit" class="button">
                        
                        <svg viewBox="0 0 89 81" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 10 L79 71 M79 10 L10 71" stroke="black" stroke-width="5" stroke-linecap="round"/>
                        </svg>

                    </div>
                    <label for="password_for_banc">Mot de passe :</label>
                    <input type="password" id="password_for_banc" placeholder="Mot de passe" />
                    <p class="displayNone" id="erreur_mdp" style="color: red;">Mots de passe incorrect</p>
                    <button  class="btnValider" onclick="verif_pass()">Valider</button>
                </div>
            </div>


        </main>

    <?php
    }
    ?>

    <!------ FOOTER  ------>

    <?php
    include "../composants/footer/footer.php";
    ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</body>



</html>