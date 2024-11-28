<?php
    session_start(); // recuperation de la sessions

    // recuperation des parametre de connection a la BdD
    include('../php/connection_params.php');

    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    // cree $comptePro qui est true quand on est sur un compte pro et false sinon
    include('../php/verif_compte_pro.php');
    // cree $compteMembre qui est true quand on est sur un compte membre et false sinon
    include('../php/verif_compte_membre.php');


    // contient fonction caf_offre pour afficher les offres
    include('../php/affichage_offre.php');

    // Inclue la fonction qui verifie la catégorie d'une offre
    // include('../php/verif_categorie.php');

    if($comptePro)      /* prépare la requête pour récupérer les offres à afficher : offres du pro si connecté en tant que pro, toutes les 
                         offres sinon */

    {
        $stmt = $dbh->prepare("select * from tripskell.offre_pro where ville = (SELECT ville FROM tripskell.pro_prive WHERE id_c=:id_c)");

        // binding pour l'id du compte (id_c <- idCompte(dans $_SESSION))
        $stmt->bindParam(":id_c", var: $id_c);
        
        $stmt = $dbh->prepare("select * from tripskell.offre_pro");

        $id_c = $_SESSION["idCompte"];
    }
    else
    {
        $stmt = $dbh->query("SELECT * from tripskell.offre_pro WHERE enligne = true");
    }

    $stmt->execute();
    $rows = $stmt->fetchAll();          // rows : les offres à afficher

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rechercher</title>

        <!-- Favicon -->
        <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

        <link rel="stylesheet" href="/style/pages/recherche.css">
        <script src="../js/recherche.js" defer></script>

    </head>
    <body  class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($comptePro)
            {
                echo "fondPro";
            }
            else
            {
                echo "fondVisiteur";
            }
        ?>>
        <?php include "../composants/header/header.php";        //import navbar
        ?>
        <div class="titrePortable">

            <svg width="401" height="158" viewBox="0 0 401 158" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g filter="url(#filter0_d_169_4380)">
            <ellipse cx="169.5" cy="61" rx="231.5" ry="89" fill="white"/>
            </g>
            <defs>
            <filter id="filter0_d_169_4380" x="-66" y="-28" width="471" height="186" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
            <feFlood flood-opacity="0" result="BackgroundImageFix"/>
            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
            <feOffset dy="4"/>
            <feGaussianBlur stdDeviation="2"/>
            <feComposite in2="hardAlpha" operator="out"/>
            <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>
            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_169_4380"/>
            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_169_4380" result="shape"/>
            </filter>
            </defs>
            </svg>

            <div id="conteneurTitreMobile">
                <!--<img src="/images/logo/logo_grand.png" alt="logo grand" id="logoTitreMobile">-->
                <img src="/images/logo/logo_petit.png" alt="logo petit" id="logoTitreMobile">
                <h3>Rechercher</h3>
            </div>
        </div>
        <main>
            <section id="conteneurBarre">
                <label for="searchbar"></label>
                <input type="searchbar" id="searchbar" placeholder="Rechercher">
            </section>

            <section id="conteneurBouton">
            <div id="btnTriePrix" class="grossisQuandHover" onclick="trierPrix()">
                <img src="/icones/trierSVG.svg" alt="iconeTrie" id="iconeTriePrix">
                <p id="txtBtnPrix">prix</p>
            </div> 
            
            <section class="filtrerBarre">
                <div class="filtreHead">
                    <svg width="89" height="81" viewBox="0 0 89 81" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M86.3333 3H3L36.3333 42.4167V69.6667L53 78V42.4167L86.3333 3Z" stroke="black" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="texteLarge">Filtrer les offres</p>
                </div>
                <div class="filtreDeplie displayNone">
                    <div>
                        <div id="filtreCat">
                            <div class="titreFiltre">
                                <hr>
                                <h3>Catégorie</h3>
                            </div>

                            <fieldset id="categorie">
                                <label>
                                    <input type="checkbox" name="categorie" value="parcAttr">
                                    <p>Parc d'attractions</p>
                                </label>
                                <label>
                                    <input type="checkbox" name="categorie" value="rest">
                                    <p>Restauration</p>
                                </label>
                                <label>
                                    <input type="checkbox" name="categorie" value="spec">
                                    <p>Spectacle</p>
                                </label>
                                <label>
                                    <input type="checkbox" name="categorie" value="activ">
                                    <p>Activités</p>
                                </label>
                                <label>
                                    <input type="checkbox" name="categorie" value="visite">
                                    <p>Visites</p>
                                </label>
                            </fieldset>
                        </div>

                        <div id="filtreLieu">
                            <div class="titreFiltre">
                                <hr>
                                <h3>Lieu</h3>
                            </div>

                            <input id="lieu" type="text" name="lieu" placeholder="Commune / Lieu-dit">
                        </div>
                    </div>
                    <div>
                        <div id="filtreOuverture">
                            <div class="titreFiltre">
                                <hr>
                                <h3>Ouverture</h3>
                            </div>

                            <fieldset id="ouverture">
                                <label>
                                    <input type="checkbox" name="ouverture" value="ouvert">
                                    <p>Ouvert</p>
                                </label>
                                <label>
                                    <input type="checkbox" name="ouverture" value="ferme">
                                    <p>Fermé</p>
                                </label>
                            </fieldset>
                        </div>

                        <div id="filtreDate">
                            <div class="titreFiltre">
                                <hr>
                                <h3>Dates</h3>
                            </div>
                            <div class="remplirDate">
                                <div>
                                    <label for="dateDeb"><p>Date de début :</p></label>
                                    <div class="datePerso">
                                        <input type="date" id="dateDeb" name="dateDeb">
                                        <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M33.3334 8.33325V24.9999" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M66.6666 8.33325V24.9999" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M79.1667 16.6667H20.8333C16.231 16.6667 12.5 20.3977 12.5 25.0001V83.3334C12.5 87.9358 16.231 91.6667 20.8333 91.6667H79.1667C83.769 91.6667 87.5 87.9358 87.5 83.3334V25.0001C87.5 20.3977 83.769 16.6667 79.1667 16.6667Z" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12.5 41.6667H87.5" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M33.3334 58.3333H33.375" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M50 58.3333H50.0417" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M66.6666 58.3333H66.7083" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M33.3334 75H33.375" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M50 75H50.0417" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M66.6666 75H66.7083" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>

                                    </div>
                                </div>

                                <div>
                                    <label for="dateFin"><p>Date de fin :</p></label>
                                    <div class="datePerso">
                                        <input type="date" id="dateFin" name="dateFin">
                                        <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M33.3334 8.33325V24.9999" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M66.6666 8.33325V24.9999" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M79.1667 16.6667H20.8333C16.231 16.6667 12.5 20.3977 12.5 25.0001V83.3334C12.5 87.9358 16.231 91.6667 20.8333 91.6667H79.1667C83.769 91.6667 87.5 87.9358 87.5 83.3334V25.0001C87.5 20.3977 83.769 16.6667 79.1667 16.6667Z" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12.5 41.6667H87.5" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M33.3334 58.3333H33.375" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M50 58.3333H50.0417" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M66.6666 58.3333H66.7083" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M33.3334 75H33.375" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M50 75H50.0417" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M66.6666 75H66.7083" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="justifyEvenly">

                        <div id="filtreEtoile">
                            <div class="titreFiltre">
                                <hr>
                                <h3>Étoiles</h3>
                            </div>
                            <div class="interEtoile">

                                <div>
                                    <label for="etoileMin"><p>Étoile(s)<br>minimum</p></label>
                                    <select name="etoileMin" id="etoileMin">
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>

                                    <svg width="106" height="106" viewBox="0 0 106 106" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M53 3L68.45 35.9127L103 41.2229L78 66.8275L83.9 103L53 85.9127L22.1 103L28 66.8275L3 41.2229L37.55 35.9127L53 3Z" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" style="fill: rgb(223, 223, 17);"/>
                                    </svg>
                                </div>

                                <div>
                                    <label for="etoileMax"><p>Étoile(s)<br>maximum</p></label>
                                    <select name="etoileMax" id="etoileMax">
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>

                                    <svg width="106" height="106" viewBox="0 0 106 106" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M53 3L68.45 35.9127L103 41.2229L78 66.8275L83.9 103L53 85.9127L22.1 103L28 66.8275L3 41.2229L37.55 35.9127L53 3Z" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" style="fill: rgb(223, 223, 17);"/>
                                    </svg>
                                </div>

                            </div>


                        </div>

                        <div id="filtrePrix">
                            <div class="titreFiltre">
                                <hr>
                                <h3>Prix</h3>
                            </div>
                            <div class="prixFiltre">
                                <div>
                                    <label for="prixMin"><p>Minimum</p></label>
                                    <input type="number" id="prixMin" name="prixMin" value="" min="0" step="1" placeholder="ex : 70">
                                    <div class="number-input">
                                        <button class="increment" onclick="adjustValue(1, 'prixMin')">⯅</button>
                                        <button class="decrement" onclick="adjustValue(-1, 'prixMin')">⯆</button>
                                    </div>
                                    <p>€</p>
                                </div>
                                <div>
                                    <label for="prixMax"><p>Maximum</p></label>
                                    <input type="number" id="prixMax" name="prixMax" value="" min="0" step="1" placeholder="ex : 300">
                                    <div class="number-input">
                                        <button class="increment" onclick="adjustValue(1, 'prixMax')">⯅</button>
                                        <button class="decrement" onclick="adjustValue(-1, 'prixMax')">⯆</button>
                                    </div>
                                    <p>€</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                
            </section>
            <section id="conteneurOffres">
                <article>
                    
                    <?php
                        foreach($rows as $row)          // il parcourt les offres pour les afficher
                        {
                            ?>
                                <a <?php
                                    if($comptePro)
                                    {
                                        ?>
                                            href="/pages/gestionOffres.php/#offre<?php echo $row['idoffre'];?>"
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                            href="/pages/detailOffre.php?idOffre=<?php echo $row["idoffre"]?>"
                                        <?php
                                    }
                                ?>
                                href="/pages/detailOffre.php?idOffre=<?php echo $row["idoffre"]?>" class="lienApercuOffre grossisQuandHover" 
                                id="offre<?php echo $row['idoffre']?>">
                                    <?php af_offre($row);?>
                                </a>
                            <?php
                        }
                    ?>
                </article>
            </section>
        </main>
        <?php
            include "../composants/footer/footer.php";
        ?>
    </body>
</html>