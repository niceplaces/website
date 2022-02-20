$(document).ready(function () {
    $(window).resize(function() {
        if ($(this).width() > 768) {
            $(".row-places-list").addClass("row");
        }
        else {
            $(".row-places-list").removeClass("row");
        }
    });
    let nearest_places_height = 180;
    let action_bar_height = $("#acton-bar").height();
    console.log(action_bar_height);
    $(".col-map").css("height", ($(window).height() - nearest_places_height - action_bar_height) + "px");
    $(".col-nearest-places").css("height", nearest_places_height + "px");

    let map;
    let markers = [];
    let infowindows = [];
    let myposition;
    let zoom = 12;
    if (localStorage.getItem("zoom") !== null){
        zoom = parseInt(localStorage.getItem("zoom"));
    }
    let currentPosition = null;
    if (localStorage.getItem("currentPosition") !== null){
        currentPosition = JSON.parse(localStorage.getItem("currentPosition"));
    }

    function loadMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: currentPosition,
            zoom: zoom,
            styles: [
                {
                    "featureType": "administrative",
                    "elementType": "geometry",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "poi",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "road",
                    "elementType": "labels.icon",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "transit",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                }
            ]
        });
        map.addListener('zoom_changed', function() {
            localStorage.setItem("zoom", map.getZoom());
        });
        map.addListener('center_changed', function() {
            console.log(map.getCenter());
            localStorage.setItem("currentPosition",
                JSON.stringify({lat: map.getCenter().lat(), lng:  map.getCenter().lng()}));
        });
        let marker = new google.maps.Marker({
            position: myposition,
            map: map,
            title: 'La mia posizione'
        });
        let infowindow = new google.maps.InfoWindow({
            content: 'La mia posizione'
        });
        marker.addListener('click', function() {
            infowindow.open(map, marker);
        });
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            alert("Geolocalizzazione non supportata.");
        }
    }

    function showPosition(position) {
        myposition = {lat: position.coords.latitude, lng: position.coords.longitude};
        if (currentPosition === null){
            currentPosition = {lat: position.coords.latitude, lng: position.coords.longitude};
        }
        loadMap();
        let url = BASE_URL + "data/query.php?version=v3&mode=release&p1=getnearestplaces&p2=" + myposition.lat + "&p3=" + myposition.lng + "&p4=10";
        $.ajax({
            url: url,
            type: "GET",
            contentType: "application/json",
            success: function (result) {
                for (let i = 0; i < result.length; i++) {
                    let icon = {
                        url: BASE_URL + "data/image.php?file=" + result[i].image + "&w=200&h=200" ,
                        scaledSize: new google.maps.Size(50, 50), // scaled size
                        origin: new google.maps.Point(0, 0), // origin
                        anchor: new google.maps.Point(25, 25) // anchor
                    };
                    let position = {lat: parseFloat(result[i].latitude), lng: parseFloat(result[i].longitude)};
                    let marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: result[i].name,
                        icon: icon
                    });
                    let infowindow = new google.maps.InfoWindow({
                        content: '<a href="../' + result[i].id_string_area + '/' + result[i].id_string_place + '">'
                            + '<span style="font-weight: bold;">'+ result[i].name + '</span><br/>' + formatDistance(result[i].distance_km) + '</a>'
                    });
                    marker.addListener('click', function() {
                        for (let i = 0; i < infowindows.length; i++){
                            infowindows[i].close();
                        }
                        infowindow.open(map, marker);
                    });
                    infowindows.push(infowindow);
                    markers.push(marker);
                    let name = result[i].name;
                    if (get_lang() === "en" && result[i].name_en){
                        name = result[i].name_en;
                    }
                    let image = '<div style="display: inline; padding-left: 0; padding-right: 0" class="place-item col-12"> ' +
                        '<div style="display: inline-block; width: 120px; text-align: center" class="row">' +
                        '<div style="display: inline; position: relative" class="col-12"> ' +
                        '<span class="marker-id" style="display: none;">' + i + '</span>' +
                        '<img src="' + BASE_URL + 'data/image.php?file=' + result[i].image + '&w=200&h=200">' +
                        '</div>' +
                        '<div style="word-break: break-all; display: inline; position: absolute; left: 0; top: 55px" class="col-12"> ' +
                        '<span class="place-name">' + name + "</span><br/>" + formatDistance(result[i].distance_km) +
                        '</div></div></div>';
                    $(".row-places-list").append(image);
                }
                let markerCluster = new MarkerClusterer(map, markers,
                    {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
                markerCluster.setGridSize(30);
                console.log(markerCluster.getGridSize());
                $('.place-name').each(function () {
                    let div = $('.place-name').parent().width();
                    let truncated = false;
                    while ( $(this).width() > div) {
                        truncated = true;
                        let text = $(this).text();
                        $(this).text(text.substr(0, text.length - 1));
                    }
                    if (truncated){
                        let text = $(this).text();
                        $(this).text(text.substr(0, text.length - 3) + "...");
                    }
                });
                $(".place-item").on("click", function () {
                    let index = parseInt($(this).find(".marker-id").text());
                    let marker = markers[index];
                    map.panTo(new google.maps.LatLng(marker.getPosition().lat(), marker.getPosition().lng()));
                    for (let i = 0; i < infowindows.length; i++){
                        infowindows[i].close();
                    }
                    infowindows[index].open(map, marker);
                });
            },
            error: function (error) {
                console.log(url);
                console.log(error);
            }
        });
    }

    getLocation();

    /*function getService() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showCoords, showError);
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function showCoords(position) {
        alert("Latitude: " + position.coords.latitude +
            "<br>Longitude: " + position.coords.longitude);
    }*/

    function showError(error) {
        switch(error.code) {
            case error.PERMISSION_DENIED:
                alert("Geolocalizzazione disattivata. Attivala e ricarica la pagina per utilizzare questa funzione.");
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Geolocalizzazione non disponibile.");
                break;
            case error.TIMEOUT:
                alert("Tempo di richiesta scaduto.");
                break;
            case error.UNKNOWN_ERROR:
                alert("Errore ignoto.");
                break;
        }
    }

    //getService();

});