<?php
//
//  affichage de la note avec des etoiles
//
function affichage_etoiles($notes) {
    for ($i = 0; $i < intval($notes); $i++) {
        ?><img src="/icones/etoilePleineSVG.svg" alt="etoile pleine"><?php
    }
    if(floatval($notes) - intval($notes) >= 0.5) {
        ?><img src="/icones/etoileMoitiePleineSVG.svg" alt="etoile moitié pleine"><?php
        $i++;
    }
    for (; $i < 5; $i++) {
        ?><img src="/icones/etoileVideSVG.svg" alt="etoile vide"><?php
    }
}