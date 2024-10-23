<?php
//
//  affichage de la note avec des etoiles
//
for ($i = 0; $i < intval($contentOffre["note"]); $i++) {
    ?><img src="/icones/etoilePleineSVG.svg" alt="etoile pleine"><?php
}
if(floatval($contentOffre["note"]) - intval($contentOffre["note"]) >= 0.5) {
    ?><img src="/icones/etoileMoitiePleineSVG.svg" alt="etoile moitié pleine"><?php
    $i++;
}
for (; $i < 5; $i++) {
    ?><img src="/icones/etoileVideSVG.svg" alt="etoile vide"><?php
}