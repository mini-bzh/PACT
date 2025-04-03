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

var LeafIcon = L.Icon.extend({
    options: {
        iconSize:     [38, 38],
        iconAnchor:   [19, 38],
        popupAnchor:  [0, -38]
    }
});

var randoMark = new LeafIcon({iconUrl: '..\\images\\carte\\rando.png'});
var parcMark = new LeafIcon({iconUrl: '..\\images\\carte\\parc.png'});
var spectMark = new LeafIcon({iconUrl: '..\\images\\carte\\spectacle.png'});
var restoMark = new LeafIcon({iconUrl: '..\\images\\carte\\resto.png'});
var activiteMark = new LeafIcon({iconUrl: '..\\images\\carte\\activite.png'});


//LISTENBOURG
// var imageUrl = '../images/listenbourg_map_2-removebg-preview.png',
// imageBounds = [[46.739861,-13.747021], [40.680638,-4.428233]];
// L.imageOverlay(imageUrl, imageBounds).addTo(map);

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

// créé les clusters
var markersCluster = L.markerClusterGroup({
    spiderfyOnMaxZoom: true,
    showCoverageOnHover: false,
    zoomToBoundsOnClick: true
});

var listeMarker={}; // la liste des markers


let getCoordinates = async (element) => {
    let baseUrl = "https://nominatim.openstreetmap.org/search?format=json&limit=3&q=";
    let url = `${baseUrl}${element.get("adresse")} ${element.get("ville")}`;
    
    try {
        let response = await fetch(url);
        let data = await response.json();

        if (data.length === 0) {
            url = `${baseUrl}${element.get("ville")}`;
            response = await fetch(url);
            data = await response.json();
        }

        if (data.length > 0) {
            let lat = parseFloat(data[0].lat);
            let lon = parseFloat(data[0].lon);
            let content = element.get('element');

            content.innerHTML += `
                <button class="btnItineraire grossisQuandHover" 
                    onclick="event.preventDefault();openNavigation(${lat}, ${lon});" 
                    style="padding:5px 10px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer;">
                    Itinéraire
                </button>
            `;

            let icon;
            switch (element.get('categorie')) {
                case "visite": icon = randoMark; break;
                case "activité": icon = activiteMark; break;
                case "parc d'attraction": icon = parcMark; break;
                case "restauration": icon = restoMark; break;
                case "spectacle": icon = spectMark; break;
                default: icon = defaultMark; break;
            }

            let marker = L.marker([lat, lon], { icon }).bindPopup(content);
            listeMarker[element.get("id")] = [marker, true];
            marker.on('mouseover', () => marker.openPopup());
            markersCluster.addLayer(marker);
        }
    } catch (error) {
        console.error(`Erreur lors de la récupération des coordonnées pour ${element.get("adresse")}:`, error);
    }
};

// Exécuter toutes les requêtes en parallèle
let promises = [];
mapOffresInfos.forEach((element) => {
    promises.push(getCoordinates(element));
});

Promise.all(promises).then(() => {
    console.log("ok");
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
        document.getElementById("map").style.height="60vh";
        map.setView(new L.LatLng(48.2640845, -2.9202408), 7);  // recentrer la carte
        document.getElementById("btnAgrandir").addEventListener("click", resizeMap)  // on remet l'écouteur
    }else{ // si la carte est affiché
        aff = true;
        document.getElementById("map").style.height="0";  // cacher la carte
        map.setView(new L.LatLng(48.2640845, -2.9202408), 7);  // recentrer la carte
        document.getElementById("btnAgrandir").addEventListener("click", resizeMap)  // on remet l'écouteur
    }
    setTimeout(() => { // pour laisser le temps à la carte de s'afficher
        map.invalidateSize();
        // Écouteur pour le bouton pour faire aparaitre/disparaitre la carte
        document.getElementById("map").scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
    }, 200);
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