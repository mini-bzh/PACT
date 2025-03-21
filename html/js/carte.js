function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds){
        break;
        }
    }
}

var map = L.map('map', {
    center: [48.2640845, -2.9202408],
    zoom: 7,
    preferCanvas: true
});

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    detectRetina: true
}).addTo(map);

map.on('load', function() {
    preloadTiles();
});

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
    var customPopup = element.get("element");

    var popupStyle = {
        'className' : 'grossisQuandHover popup'+ element.get("id")
    };
        
    var xmlhttp = new XMLHttpRequest();
    var url = "https://nominatim.openstreetmap.org/search?format=json&limit=3&q=" + element.get("adresse")+" "+ element.get("ville");
    xmlhttp.onreadystatechange = function()
    {
        if (this.readyState == 4 && this.status == 200)
        {
            var myArr = JSON.parse(this.responseText);
            try {
                var marker = L.marker([parseFloat(myArr[0].lat),parseFloat(myArr[0].lon)]).bindPopup(customPopup,popupStyle); 
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
                        marker = L.marker([parseFloat(myArr[0].lat),parseFloat(myArr[0].lon)]).bindPopup(customPopup,popupStyle); 
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

setTimeout(() => {
    map.addLayer(markersCluster);
}, 2000);

function updateMap() {
    for(elem in listeMarker){
        listeMarker[elem][0].remove();
        if (mapOffresInfos.get(elem).get("visibilite")) {
            listeMarker[elem][0].addTo(map);
        }
    }
}

document.getElementsByClassName("leaflet-top leaflet-right")[0].innerHTML = `<div id="btnAgrandir" class="leaflet-control leaflet-bar"><svg width="100px" height="100px" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="si-glyph si-glyph-arrow-resize-1">
    
        <title>124</title>
        
        <defs>

        </defs>
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g fill="#434343">
                    <path d="M6.995,10.852 L5.133,9.008 L2.107,11.988 L0.062,9.972 L0.062,15.875 L6.049,15.875 L3.973,13.828 L6.995,10.852 Z" class="si-glyph-fill">

        </path>
                    <path d="M9.961,0.00800000003 L12.058,2.095 L9.005,5.128 L10.885,7.008 L13.942,3.97 L15.909,5.966 L15.909,0.00800000003 L9.961,0.00800000003 Z" class="si-glyph-fill">

        </path>
                </g>
            </g>
        </svg></div>`;

let grandir = true;

function resizeMap(e) {
    if (grandir) {
        grandir = false;
        document.getElementById("map").style.height="80vh";
        document.getElementsByClassName("leaflet-top leaflet-right")[0].innerHTML = `<div id="btnAgrandir" class="leaflet-control leaflet-bar"><svg width="100px" height="100px" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="si-glyph si-glyph-arrow-resize-3">
    
        <title>125</title>
        
        <defs>

        </defs>
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g fill="#434343">
                    <path d="M15.995,1.852 L14.133,0.00800000003 L11.107,2.988 L9.062,0.972 L9.062,6.875 L15.049,6.875 L12.973,4.828 L15.995,1.852 Z" class="si-glyph-fill">

        </path>
                    <path d="M0.961,9.008 L3.058,11.095 L0.005,14.128 L1.885,16.008 L4.942,12.97 L6.909,14.966 L6.909,9.008 L0.961,9.008 Z" class="si-glyph-fill">

        </path>
                </g>
            </g>
        </svg></div>`;
        map.setView(new L.LatLng(48.2640845, -2.9202408), 7);
        document.getElementById("btnAgrandir").addEventListener("click", resizeMap)
    }else{
        grandir = true;
        document.getElementById("map").style.height="20vh";
        document.getElementsByClassName("leaflet-top leaflet-right")[0].innerHTML = `<div id="btnAgrandir" class="leaflet-control leaflet-bar"><svg width="100px" height="100px" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="si-glyph si-glyph-arrow-resize-1">
    
        <title>124</title>
        
        <defs>

        </defs>
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g fill="#434343">
                    <path d="M6.995,10.852 L5.133,9.008 L2.107,11.988 L0.062,9.972 L0.062,15.875 L6.049,15.875 L3.973,13.828 L6.995,10.852 Z" class="si-glyph-fill">

        </path>
                    <path d="M9.961,0.00800000003 L12.058,2.095 L9.005,5.128 L10.885,7.008 L13.942,3.97 L15.909,5.966 L15.909,0.00800000003 L9.961,0.00800000003 Z" class="si-glyph-fill">

        </path>
                </g>
            </g>
        </svg></div>`;
        map.setView(new L.LatLng(48.2640845, -2.9202408), 7);
        document.getElementById("btnAgrandir").addEventListener("click", resizeMap)
    }
    setTimeout(() => {
        map.invalidateSize();
        document.getElementById("map").scrollIntoView({ behavior: "smooth", block: "end", inline: "nearest" });
    }, 100);
}

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