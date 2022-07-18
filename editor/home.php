<?php
require_once '../data/protected/config.php';
?>
<!doctype html>
<html lang="it">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.7.0/css/all.css'
          integrity='sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ' crossorigin='anonymous'>
    <link rel="stylesheet" href="home.css"/>
    <link rel="stylesheet" href="map.css"/>
    <title>Nice Places Editor</title>
</head>
<body>
<div class="container">
    <div class="sub-container">
        <div class="row">
            <div class="col text-right">
                <span id="id_user"><?php echo $_SESSION["id_user"] ?></span>
                Ciao <?php echo $_SESSION["login"] ?>!&nbsp;
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary" id="btn_stats" data-toggle="modal"
                            data-target="#modal_stats">
                        Statistiche
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="btn_last_edits" data-toggle="modal"
                            data-target="#modal_last_edits">
                        Ultime modifiche
                    </button>
                    <form class="form" method="post" action="./">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="btn btn-outline-danger" id="btn_logout" style="align-self: center">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm">
                <h1>Nice Places Editor</h1>
            </div>
        </div>
        <div class="row">
            <div class="col text-center">
                <button type="button" class="btn btn-outline-primary" id="btn_edit_areas" data-toggle="modal"
                        data-target="#modal_areas">
                    Aggiungi zona
                </button>
            </div>
            <div class="col text-center">
                <button type="button" class="btn btn-outline-primary" id="btn_edit_places" data-toggle="modal"
                        data-target="#modal_places">
                    Aggiungi luogo
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="area">Zona</label>
                <div class="input-group">
                    <select class="custom-select" id="area">
                        <option selected>Seleziona...</option>
                    </select>
                </div>
            </div>
        </div>
        <div id="select_place">
            <div class="row">
                <div class="col">
                    <label for="place">Luogo</label>
                    <div class="input-group">
                        <select class="custom-select" id="place">
                            <option selected>Seleziona...</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div id="place_panel">
            <div class="row">
                <div class="col-sm">
                    <label for="english_name">Nome in inglese</label>
                    <input type="text" class="form-control" id="english_name">
                    <small>Lasciare vuoto se non c'è la traduzione.</small>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <label for="english_name">Posizione</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                        </div>
                        <input id="pac-input" class="controls form-control" type="text" placeholder="Cerca...">
                    </div>
                    <small>Usa il tasto destro per posizionare il marker.</small>
                    <div id="map"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    Lat: <span id="lat"></span>°, lon: <span id="lon"></span>°
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="row">
                        <label for="photo">Foto</label>
                        <div class="input-group">
                            <input type="text" id="photo" class="form-control" readonly>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-primary" id="btn_select_photo"
                                        data-toggle="modal"
                                        data-target="#modal_photos">Seleziona
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-outline-success" style="align-self: center"
                                data-toggle="modal" data-target="#modal_upload_image">
                            Carica da computer
                        </button>
                    </div>
                    <div class="row">
                        <a href="https://www.google.com/imghp?tbm=isch&source=lnt&tbs=sur:fc&sa=X" target="_blank">
                            Cerca immagini utilizzabili
                        </a>
                    </div>
                </div>
                <div class="col" id="selected-photo">
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <label for="photo_credits">Copyright foto</label>
                    <input type="text" class="form-control" id="photo_credits">
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" id="btn_desc_it">
                            Italiano
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="btn_desc_en">
                            Inglese
                        </button>
                    </div>
                </div>
            </div>
            <div class="row description">
                <div class="col-sm">
                    <label for="description">Descrizione <img class="flag" src="../assets/icons/italy.png"></label>
                    <span class="char_counter"><span>0</span> caratteri</span>
                    <textarea id="description" class="form-control"></textarea>
                </div>
            </div>
            <div class="row description_en">
                <div class="col-sm">
                    <label for="description_en">Description <img class="flag" src="../assets/icons/united_kingdom.png"></label>
                    <span class="char_counter"><span>0</span> caratteri</span>
                    <textarea id="description_en" class="form-control"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <label for="desc_sources">Fonti</label>
                    <input type="text" class="form-control" id="desc_sources">
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <label for="wiki_url">Wikipedia (IT)</label>
                    <input type="text" class="form-control" id="wiki_url">
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <label for="wiki_url_en">Wikipedia (EN)</label>
                    <input type="text" class="form-control" id="wiki_url_en">
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <label for="facebook">Post sul blog [IT]</label>
                    <input type="text" class="form-control" id="facebook">
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <label for="instagram">Post sul blog [EN]</label>
                    <input type="text" class="form-control" id="instagram">
                </div>
            </div>
        </div>
    </div>
    <div id="events_panel">
        <div class="row">
            <div class="col" id="div-events"></div>
        </div>
        <div class="row">
            <div class="col-sm text-center">
                <button type="button" class="btn btn-outline-primary" id="btn_save" style="align-self: center">Salva
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_areas" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aggiungi zona</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <label for="area_to_add">Nome</label>
                        <input type="text" class="form-control" id="area_to_add">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-outline-primary" id="modal_areas_save">Salva</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_places" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aggiungi zona</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <label for="places_area_to_add">Zona</label>
                        <select class="custom-select" id="places_area_to_add">
                            <option selected>Seleziona...</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="area_to_add">Nome</label>
                        <input type="text" class="form-control" id="place_to_add">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-outline-primary" id="modal_places_save">Salva</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_last_edits" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ultime modifiche</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        Aggiornati
                        <table id="table_updated" class="table-sm">
                            <thead>
                            <tr>
                                <th>Luogo</th>
                                <th width="30%">Istante</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="col">
                        Inseriti
                        <table id="table_inserted" class="table-sm">
                            <thead>
                            <tr>
                                <th>Luogo</th>
                                <th width="30%">Istante</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_stats" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Statistiche</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_photos" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input id="searchbar-photos" class="controls form-control" type="text" placeholder="Cerca...">
                </div>
                <div class="photos-body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_upload_image" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Carica una foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <label for="image_path">Immagine</label>
                        <form class="form" id="form_upload_image">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-secondary btn-file" type="button">
                                        Scegli... <input type="file" name="file" id="imgInp">
                                    </button>
                                </div>
                                <input type="text" id="image_path" class="form-control" readonly>
                            </div>
                            <div id="img-info"></div>
                        </form>
                    </div>
                    <div class="col" id="img-upload">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-outline-primary" id="btn_upload">Invia</button>
            </div>
        </div>
    </div>
</div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">
<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
<!-- (Optional) Latest compiled and minified JavaScript translation files -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/i18n/defaults-*.min.js"></script>
<script type="text/javascript">

    let googleMap;
    let markers = [];

    function initAutocomplete() {
        googleMap = new google.maps.Map(document.getElementById('map'), {
            center: {lat: -33.8688, lng: 151.2195},
            zoom: 13,
            mapTypeId: 'roadmap'
        });

        // Create the search box and link it to the UI element.
        let input = document.getElementById('pac-input');
        let searchBox = new google.maps.places.SearchBox(input);

        // Bias the SearchBox results towards current map's viewport.
        googleMap.addListener('bounds_changed', function () {
            searchBox.setBounds(googleMap.getBounds());
        });

        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function () {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // Clear out the old markers.
            markers.forEach(function (marker) {
                marker.setMap(null);
            });
            markers = [];

            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();
            places.forEach(function (place) {
                if (!place.geometry) {
                    console.log("Returned place contains no geometry");
                    return;
                }
                var icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                // Create a marker for each place.
                markers.push(new google.maps.Marker({
                    map: googleMap,
                    icon: icon,
                    title: place.name,
                    position: place.geometry.location
                }));

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            googleMap.fitBounds(bounds);
        });
        google.maps.event.addListener(googleMap, "rightclick", function (e) {
            //lat and lng is available in e object
            console.log(e.latLng)
            placeMarker({lat: e.latLng.lat(), lng: e.latLng.lng()}, googleMap);
        });
    }

    function placeMarker(position, map) {
        console.log("New marker!", position);
        markers.forEach(function (marker) {
            marker.setMap(null);
        });
        markers = [];
        let marker = new google.maps.Marker({
            position: position,
            map: map
        });
        markers.push(marker);
        $('#lat').html(position.lat.toFixed(6));
        $('#lon').html(position.lng.toFixed(6));
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gmapsApiKey ?>&libraries=places&callback=initAutocomplete"
        async defer></script>
<script type="application/javascript" src="js/home.js"></script>
<script type="application/javascript" src="js/upload_image.js"></script>
</body>
</html>