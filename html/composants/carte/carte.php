<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<div id="map"></div>
<style>
    #map{
        width: 90%;
        height: 500px;
    }
</style>
<script>

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
        zoom: 7
    });
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    var listeMarker={};

    mapOffresInfos.forEach(element => {
        var customPopup = `<b>${element.get("titre")}</b></br><p>${element.get("prix")} €</p></br><p>${element.get("adresse")}</p></br><a href="/pages/detailOffre.php?idOffre=${element.get("id").replace('offre', '')}" class="lienApercuOffre grossisQuandHover">voir plus</a>`;
        var popupStyle = {
            'className' : 'grossisQuandHover popup'+ element.get("id")
        };
        
        console.log(element.get("adresse"));
        var xmlhttp = new XMLHttpRequest();
        var url = "https://nominatim.openstreetmap.org/search?format=json&limit=3&q=" + element.get("adresse")+" "+ element.get("ville");
        xmlhttp.onreadystatechange = function()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                var myArr = JSON.parse(this.responseText);
                try {
                    var marker = L.marker([parseFloat(myArr[0].lat),parseFloat(myArr[0].lon)]).addTo(map).bindPopup(customPopup,popupStyle); 
                    listeMarker[element.get("id")] = marker;
                } catch (error) {
                    var url = "https://nominatim.openstreetmap.org/search?format=json&limit=3&q=" + element.get("ville");
                    xmlhttp.onreadystatechange = function()
                    {
                        if (this.readyState == 4 && this.status == 200)
                        {
                            var marker;
                            var myArr = JSON.parse(this.responseText);
                            //if (){
                                
                            //}else{
                                marker = L.marker([parseFloat(myArr[0].lat),parseFloat(myArr[0].lon)]).addTo(map).bindPopup(customPopup,popupStyle); 
                            //}
                            listeMarker[element.get("id")] = marker;
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


console.log(listeMarker);


mapOffresInfos.addEvenListener("click", function (e) {
  console.log("coucou")
});


</script>