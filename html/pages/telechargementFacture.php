<?php
                // Inclure l'autoloader de Composer

                require_once '../librairies/dompdf/autoload.inc.php';
                
                use Dompdf\Dompdf;

                // Créer un nouvel objet Dompdf
                $dompdf = new Dompdf;
                
                // Charger le contenu HTML à partir d'un fichier ou d'une chaîne
                ob_start();
                require_once 'contentFacture.php';
                $html = ob_get_contents();
                ob_end_clean();
                
                $dompdf->loadHtml($html);
                
                // Rendre le document PDF
                $dompdf->render();
                
                // Envoyer le document PDF à la sortie
                $dompdf->stream('Facture.pdf');
            
                ?>