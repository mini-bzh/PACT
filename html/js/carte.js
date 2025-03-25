/*
Fonction sleep
*/
function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds){
        break;
        }
    }
}

// Définition de la carte
var map = L.map('map', {
    center: [48.2640845, -2.9202408],
    zoom: 7,
    preferCanvas: true
});

// Définition du fond puis ajout à la carte
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    detectRetina: true
}).addTo(map);

var imageUrl = '../images/listenbourg_map_2-removebg-preview.png',
imageBounds = [[46.739861,-13.747021], [40.680638,-4.428233]];

L.imageOverlay(imageUrl, imageBounds).addTo(map);

// Préchargement de la carte
map.on('load', function() {
    preloadTiles();
});

/*
Fonction pour précharger la carte 
*/
function preloadTiles() {
    let bounds = map.getBounds();
    let zoom = map.getZoom();

    for (let x = bounds.getWest(); x < bounds.getEast(); x += 0.5) {
        for (let y = bounds.getSouth(); y < bounds.getNorth(); y += 0.5) {
            tileLayer._tileCoordsToKey({ x, y, z: zoom });
        }
    }
}

var markersCluster = L.markerClusterGroup({
    spiderfyOnMaxZoom: true,
    showCoverageOnHover: false,
    zoomToBoundsOnClick: true
});

var listeMarker={};

mapOffresInfos.forEach(element => {    
    var xmlhttp = new XMLHttpRequest();
    var url = "https://nominatim.openstreetmap.org/search?format=json&limit=3&q=" + element.get("adresse")+" "+ element.get("ville");
    xmlhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            var myArr = JSON.parse(this.responseText);
            try {
                var content = element.get('element');
                content.innerHTML += 
                `
                <button class="btnItineraire" onclick="openNavigation(${myArr[0].lat}, ${myArr[0].lon})" style="margin-top:5px;padding:5px 10px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer;">
                                            Itinéraire
                </button>
                `;
                
                var customPopup = content;
                console.log(customPopup);

                var marker = L.marker([parseFloat(myArr[0].lat),parseFloat(myArr[0].lon)]).bindPopup(customPopup);
                listeMarker[element.get("id")] = [marker,true];
                markersCluster.addLayer(marker);
            } catch (error) {
            var url = "https://nominatim.openstreetmap.org/search?format=json&limit=3&q=" + element.get("ville");
                xmlhttp.onreadystatechange = function()
                {
                    if (this.readyState == 4 && this.status == 200)
                    {
                        var marker;
                        var myArr = JSON.parse(this.responseText);

                        var content = element.get('element');
                        content.innerHTML += 
                        `
                        <button class="btnItineraire" onclick="openNavigation(${myArr[0].lat}, ${myArr[0].lon})" style="margin-top:5px;padding:5px 10px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer;">
                                                    Itinéraire
                        </button>
                        `;
                        
                        var customPopup = content;
                        console.log(customPopup);

                        marker = L.marker([parseFloat(myArr[0].lat),parseFloat(myArr[0].lon)]).bindPopup(customPopup);
                        listeMarker[element.get("id")] = [marker,true];
                        markersCluster.addLayer(marker);
                    }
                };
                xmlhttp.open("GET", url, true);
                xmlhttp.send();
            }
            
        }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
    sleep(100); 
});

// Pour laisser du temps pour que les points apparaîssent puis les ajouter à la carte
setTimeout(() => {
    map.addLayer(markersCluster);
}, 2000);

/*

Fonction pour mettre à jour les points dans les clusters

*/
function updateMap() {
    markersCluster.clearLayers();
    for(elem in listeMarker){
        listeMarker[elem][0].remove();
        if (mapOffresInfos.get(elem).get("visibilite")) {
            markersCluster.addLayer(listeMarker[elem][0]);
        }
    }
    map.addLayer(markersCluster);
}

// Booleen pour afficher/cacher la carte
let aff = true;

/*

Fonction pour cacher la carte si elle est afficher et l'afficher si ielle est cacher

*/
function resizeMap(e) {
    if (aff) { // si la carte est cacher
        aff = false;
        document.getElementById("map").style.display="block";  // afficher la carte
        document.getElementById("map").style.height="80vh";
        map.setView(new L.LatLng(48.2640845, -2.9202408), 7);  // recentrer la carte
        document.getElementById("btnAgrandir").addEventListener("click", resizeMap)  // on remet l'écouteur
    }else{ // si la carte est affiché
        aff = true;
        document.getElementById("map").style.display="none";  // cacher la carte
        map.setView(new L.LatLng(48.2640845, -2.9202408), 7);  // recentrer la carte
        document.getElementById("btnAgrandir").addEventListener("click", resizeMap)  // on remet l'écouteur
    }
    setTimeout(() => { // pour laisser le temps à la carte de s'afficher
        map.invalidateSize();
        // Écouteur pour le bouton pour faire aparaitre/disparaitre la carte
        document.getElementById("map").scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
    }, 100);
}

// Écouteur pour le bouton pour faire aparaitre/disparaitre la carte
document.getElementById("btnAgrandir").addEventListener("click", resizeMap)

// Désactiver le scroll de la page quand on commence à drag la carte
map.on('mousedown', function () {
    document.body.style.overflow = 'hidden'; // Empêche le scroll de la page
});

// Réactiver le scroll quand on relâche la souris
map.on('mouseup', function () {
    document.body.style.overflow = ''; // Rétablit le scroll normal
});

// Réactiver le scroll aussi si la souris quitte la fenêtre
document.addEventListener('mouseleave', function () {
    document.body.style.overflow = '';
});

function openNavigation(lat, lon) {
    var userAgent = navigator.userAgent || navigator.vendor || window.opera;
    var url = "";

    if (/iPad|iPhone|iPod/.test(userAgent)) {
        // iOS → Apple Maps
        url = `https://maps.apple.com/?daddr=${lat},${lon}`;
    } else if (/android/i.test(userAgent)) {
        // Android → Google Maps
        url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lon}`;
    } else {
        // PC → Google Maps par défaut
        url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lon}`;
    }

    window.open(url, "_blank"); // Ouvre le lien dans un nouvel onglet
}