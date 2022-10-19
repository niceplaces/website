<?php
require_once 'data/protected/config.php';
?>
<!doctype html>
<html>
<head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <style>
        /* Always set the map height explicitly to define the size of the div
         * element that contains the map. */
        #map {
            height: 100%;
        }

        /* Optional: Makes the sample page fill the window. */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
    <style type="text/css">

        .container {
            background-size: cover;
            background: rgba(255, 255, 255, 0.7);
            margin-left: auto;
            margin-right: auto;
            border-radius: 10px !important;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.3), 0 6px 20px 0 rgba(0, 0, 0, 0.3);
        }

        .container-fluid {
            height: 100%;
            padding: 0 !important;
        }

        .col-nearest-places img {
            width: 80px;
            margin-right: 10px;
            margin-bottom: 7px;
        }

        #links img {
            height: 30px;
            width: 30px;
            opacity: 0.8;
            margin: 5px;
        }

        @media (min-width: 768px) {

            #links {
                position: absolute;
                bottom: 10px;
            }
        }
    </style>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div id="db-mode" style="display: none"><?php echo $_GET['mode'] ?></div>
<div class="container-fluid">
    <div id="map"></div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>
<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>
<script>

    const base_url = "https://www.niceplaces.it/";
    //const base_url = "http://localhost/niceplaces/";

    let map;
    let myposition;

    function initMap() {
        luoghi = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 42, lng: 12},
            zoom: 5.8
        });
    }

    function loadMap() {
        luoghi = new google.maps.Map(document.getElementById('map'), {
            center: myposition,
            zoom: 8,
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
        let marker = new google.maps.Marker({
            position: myposition,
            map: luoghi,
            title: 'La mia posizione'
        });
    }

    let mode = document.getElementById('db-mode').textContent
    let url = base_url + "data/query.php?version=v3&mode=" + mode + "&p1=getnearestplaces&p2=" + 43 + "&p3=" + 11 + "&p4=10000";
    $.ajax({
        url: url,
        type: "GET",
        contentType: "application/json",
        success: function (result) {
            let markers = [];
            for (let i = 0; i < result.length; i++) {
                let icon = {
                    url: base_url + "data/image.php?mode=" + mode + "&file=" + result[i].image + "&w=200&h=200",
                    scaledSize: new google.maps.Size(50, 50), // scaled size
                    origin: new google.maps.Point(0, 0), // origin
                    anchor: new google.maps.Point(25, 25) // anchor
                };
                let marker = new google.maps.Marker({
                    position: {lat: parseFloat(result[i].latitude), lng: parseFloat(result[i].longitude)},
                    map: luoghi,
                    title: result[i].name,
                    icon: icon
                });
                marker.addListener('click', function () {
                    window.open(base_url + result[i].id_string_area + "/" + result[i].id_string_place);
                });
                markers.push(marker);
                let image = '<div style="display: inline" class="col-md-12"> ' +
                    '<div style="display: inline-block; width: 100px; border-radius: 50px; text-align: center" class="row">' +
                    '<div style="display: inline; position: relative; ' +
                    'background-image: url(" ' + base_url + 'data/image.php?mode=' + mode + '&name=' + result[i].image + '&w=100&h=100")"' +
                    'class="col-sm-12 col-md-6"></div>' +
                    '<div style="display: inline; position: absolute; left: 0; top: 70px" class="col-sm-12 col-md-6"> ' +
                    result[i].name + "<br/>" + result[i].distance_km +
                    '</div></div></div>';
                $(".row-places-list").append(image);
            }
            let markerCluster = new MarkerClusterer(luoghi, markers,
                {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
            markerCluster.setGridSize(30);
        },
        error: function (error) {
            console.log(error);
        }
    });

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gmapsApiKey ?>&callback=initMap"></script>
</body>
</html>