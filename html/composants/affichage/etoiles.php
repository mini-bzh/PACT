<?php
//
//  affichage de la note avec des etoiles
//
function affichage_etoiles($notes) {
    ?><p class="hideForGraphic">Etoiles : </p><?php
    for ($i = 0; $i < intval($notes); $i++) {
        ?><img src="/icones/etoilePleineSVG.svg" alt="pleine&nbsp;"><?php
    }
    if(floatval($notes) - intval($notes) >= 0.5) {
        ?><img src="/icones/etoileMoitiePleineSVG.svg" alt="moitié pleine&nbsp;"><?php
        $i++;
    }
    for (; $i < 5; $i++) {
        ?><img src="/icones/etoileVideSVG.svg" alt="vide&nbsp;"><?php
    }
}