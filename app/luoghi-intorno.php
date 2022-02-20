<?php
require_once '../protected/config.php';
require_once "../utils.php";
?>
<!DOCTYPE html>
<html>
<head>
    <?php include 'header.php' ?>
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
    <link rel="stylesheet" href="<?php echo $BASE_URL ?>app/css/luoghi-intorno.css">
    <title><?php echo t('luoghi-intorno-a-te') ?> | Nice Places</title>
</head>
<body>
<div class="container-fluid">
    <div class="row" id="acton-bar">
            <div class="app-icon"></div>
        <div class="app-name">
            <span><?php echo t("luoghi-intorno-a-te") ?></span>
        </div>
        <div class="nav-item dropdown lang">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php switch (get_lang()) {
                    case "it": ?>
                        <img src="<?php echo $BASE_URL ?>assets/icons/italy.png">
                        <?php break;
                    case "en": ?>
                        <img src="<?php echo $BASE_URL ?>assets/icons/united_kingdom.png">
                        <?php break;
                } ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <a class="dropdown-item flag-it" href="<?php echo $BASE_URL . "app/luoghi-intorno-a-te" ?>">
                    <img src="<?php echo $BASE_URL ?>assets/icons/italy.png"> Italiano</a>
                <a class="dropdown-item flag-en" href="<?php echo $BASE_URL . "en/app/places-around-you" ?>">
                    <img src="<?php echo $BASE_URL ?>assets/icons/united_kingdom.png"> English</a>
            </div>
        </div>
    </div>
    <div class="row row-map">
        <div class="col-12 col-map">
            <div id="map"></div>
        </div>
        <div class="col-12 col-nearest-places">
            <h5><?php echo t("luoghi-intorno-a-te") ?> <small>(nel raggio di 100 km)</small></h5>
            <div class="row-places-list"></div>
        </div>
    </div>
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
<script src="<?php echo $BASE_URL ?>js/utils.js"></script>
<script src="<?php echo $BASE_URL ?>app/js/luoghi-intorno.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gmapsApiKey ?>&callback=loadMap"></script>
</body>
</html>