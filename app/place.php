<?php
require_once "../utils.php";
require_once "../data/requires.php";
error_reporting(E_ALL && ~E_WARNING);
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$params = strpos($url, "?") != FALSE ? strpos($url, "?") : strlen($url);
$url = substr($url, 0, $params);
$explode = explode("/", $url);
$id_area = $explode[count($explode)-2];
$id_place = $explode[count($explode)-1];

$conn = mySqlConnect();
$daoPlaces = new DaoPlacesV3($conn, VERSION, MODE);
$place = $daoPlaces->getOneByIdString($id_area, $id_place);
$name = $place["name"];
if (get_lang() == "en" && $place["name_en"] != ""){
    $name = $place["name_en"];
}
$area = $place["area"];
if (get_lang() == "en" && $place["area_en"] != ""){
    $area = $place["area_en"];
}
$region = $place["region"];
if (get_lang() == "en" && $place["region_en"] != ""){
    $region = $place["region_en"];
}
$description = $place["description"];
if (get_lang() == "en"){
    $description = $place["description_en"];
}
$wiki_url = $place["wiki_url"];
if (get_lang() == "en" && $place["wiki_url_en"] != ""){
    $wiki_url = $place["wiki_url_en"];
}

$width = 0;
$height = 0;
if (strcmp($place["image"], "") != 0) {
    list($width, $height) = getimagesize($BASE_URL."data/photos/release/".$place["image"]);
}
?>
<!DOCTYPE html>
<html lang="<?php echo get_lang() ?>">
<head>
    <link rel="stylesheet" href="<?php echo $BASE_URL ?>app/css/place.css">
    <link rel="stylesheet" href="<?php echo $BASE_URL ?>css/card_list2b.css">
    <?php include 'header.php' ?>
    <title><?php echo $name." (".$area.")" ?> | Nice Places</title>
    <meta name="description" content="<?php echo substr($description, 0, 300) ?>">
    <!--- Link previews -->
    <meta property="og:url" content="<?php echo $url ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $name." (".$area.")" ?> | Nice Places" />
    <meta property="og:description" content="<?php echo substr($description, 0, 300) ?>" />
    <meta property="og:image" content="<?php echo $BASE_URL."data/photos/release/".$place["image"] ?>" />
    <meta property="og:image:width" content="<?php echo $width ?>" />
    <meta property="og:image:height" content="<?php echo $height ?>" />
    <meta name="twitter:card" content="summary_large_image">
</head>
<body>
<div style="overflow-x: hidden"> <!-- Prevent right space on mobile -->
    <!--<div class="background-image" style="background-image: url(<?php
    if (strcmp($place["image"], "") != 0) {
        echo $BASE_URL . "data/photos/release/" . $place["image"];
    } else {
        echo $BASE_URL . "assets/placeholder.jpg";
    }
    ?>)"></div>-->
    <div class="container-fluid animate-bottom">
        <div class="row justify-content-center" id="acton-bar">
            <div class="app-icon"></div>
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
                    <a class="dropdown-item flag-it" href="<?php echo $BASE_URL . $place["id_area"] . "/" . $place["id_string"] ?>">
                        <img src="<?php echo $BASE_URL ?>assets/icons/italy.png"> Italiano</a>
                    <a class="dropdown-item flag-en" href="<?php echo $BASE_URL . "en/" . $place["id_area_en"] . "/" . $place["id_string_en"] ?>">
                        <img src="<?php echo $BASE_URL ?>assets/icons/united_kingdom.png"> English</a>
                </div>
            </div>
        </div>
        <div class="row" style="height: 94%">
            <div class="col-lg-4">
                <div class="row">
                    <div id="photo_filename" style="display: none"><?php echo $place["image"] ?></div>
                    <div class="col-md" id="place_image" 
                    style="background-image: url(<?php echo $BASE_URL . "assets/placeholder.jpg"?>)">
                        <div id="photo-credits"><?php echo $place["img_credits"] ?></div>
                    </div>
                </div>
                    <!--<div class="row">
                        <div class="col-md" style="padding: 0">
                            <div id="map"></div>
                        </div>
                    </div>-->
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-md" id="place_details">
                        <div class="row">
                            <div class="col text-center" id="place_name">
                                <?php echo $name ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md text-center place-area">
                                <?php
                                $link = $BASE_URL.$id_area;
                                if (get_lang() == "en"){
                                    $link = $BASE_URL."en/".$id_area;
                                }
                                ?>
                                <a href="<?php echo $link ?>/" id="place_area"><?php echo $area ?></a>,
                                <?php
                                $link = $BASE_URL.$place["id_region"];
                                if (get_lang() == "en"){
                                    $link = $BASE_URL."en/".$place["id_region_en"];
                                }
                                ?>
                                <a href="<?php echo $link ?>/" id="place_region"><?php echo $region ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm" id="place_description">
                    <div class="row">
                        <div class="col">
                            <?php
                            if (strcmp($description, "") != 0){
                                echo $description;
                            } else {
                                echo t("descrizione-non-disponibile");
                            }
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md" id="links">
                            <div style="position: absolute; left: 20px; bottom: 0" class="addthis_inline_share_toolbox"></div>
                            <a id="wiki-url" href="<?php echo $wiki_url ?>">
                                <img src="<?php echo $BASE_URL ?>assets/icons/wikipedia.png"/>
                            </a>
                            <a id="gmaps" href="<?php echo "https://www.google.com/maps/search/?api=1&query=".$place["latitude"].",".$place["longitude"] ?>">
                                <img src="<?php echo $BASE_URL ?>assets/icons/google-maps.png"/>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col">
                        <h4><?php echo t("vicino-a") . " " . $name ?></h4>
                    </div>
                </div>
                <div class="row" id="random-nearest-places">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h4><?php echo t("altri-luoghi-da") ?> Nice Places</h4>
            </div>
        </div>
        <div class="row" id="random-places">
        </div>
        <div class="row">
            <div class="col-sm store">
                <a href="https://play.google.com/store/apps/details?id=com.niceplaces.niceplaces" target="_blank">
                    <?php echo t("scarica-nice-places") ?> Google Play Store!
                </a>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php' ?>
<script type="text/javascript" src="<?php echo $BASE_URL ?>app/js/place.js"></script>
<script type="text/javascript">

    let myposition = <?php echo "{lat: ".$place["latitude"].", lng: ".$place["longitude"]."}" ?> ;

    function initMap() {
        luoghi = new google.maps.Map(document.getElementById('map'), {
            center: myposition,
            zoom: 16,
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

    console.log(BASE_URL + "data/query.php?version=v3&mode=release&p1=getrandomnearest" +
            "&p2=" + myposition.lat + "&p3=" + myposition.lng + "&p4=8")

    $.ajax({
        url: BASE_URL + "data/query.php?version=v3&mode=release&p1=getrandomnearest" +
            "&p2=" + myposition.lat + "&p3=" + myposition.lng + "&p4=8",
        type: "GET",
        contentType: "application/json",
        success: function (result) {
            console.log(result);
            for (let i = 0; i < result.length; i++) {
                let html = '<div class="col-lg-6 col-md-4 col-sm-6">' +
                    '<a href="" class="place_link">' +
                    '<div class="card-container">' +
                    '<div class="place_image">' +
                    '<div class="place_name_cont">' +
                    '<div class="place_name"></div>' +
                    '<div class="place_area"></div>' +
                    '</div>' +
                    '</div>' +
                    '</div></a>' +
                    '</div>';
                $('#random-nearest-places').append(html);
            }
            for (let i = 0; i < result.length; i++) {
                let el = $('#random-nearest-places').find('.place_image').eq(i)
                if (result[i].image !== "") {
                    el.css("background-image", "url(" + BASE_URL + "data/image.php?mode=release&file=" + result[i].image + 
                    '&w=' + el.width() + '&h=' + el.height() + ")");
                }
                let name = result[i].name;
                if (get_lang() === "en" && result[i].name_en !== ""){
                    name = result[i].name_en;
                }
                let area = result[i].area;
                if (get_lang() === "en" && result[i].area_en !== ""){
                    area = result[i].area_en;
                }
                let region = result[i].region;
                if (get_lang() === "en" && result[i].region_en !== ""){
                    region = result[i].region_en;
                }
                $('#random-nearest-places').find('.place_name').eq(i).html(name);
                $('#random-nearest-places').find('.place_area').eq(i).html(area + ", " + region);
                let id_area = result[i].id_area;
                if (get_lang() === "en"){
                    id_area = result[i].id_area_en;
                }
                let id_string = result[i].id_string;
                if (get_lang() === "en"){
                    id_string = result[i].id_string_en;
                }
                $('#random-nearest-places').find('.place_link').eq(i).attr('href', BASE_URL + '/' + id_area + '/' + id_string);
                if (get_lang() === "en"){
                    $('#random-nearest-places').find('.place_link').eq(i).attr('href', BASE_URL + 'en/' + id_area + '/' + id_string);
                }
            }
        },
        error: function (error) {
            console.log(error);
            alert("Si è verificato un errore!");
        }
    });
</script>
</script>
</body>
</html>