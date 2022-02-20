<?php
require_once "../data/requires.php";
require_once '../utils.php';
error_reporting(E_ALL && ~E_NOTICE);
$conn = mySqlConnect();
$dao = new DaoPlacesV3($conn, VERSION, MODE);
$placeOfTheDay = $dao->getPlaceOfTheDay(strtoupper(get_lang()));
$dao = new DaoLists($conn, VERSION, MODE);
$lists = $dao->getLists();
$dao = new DaoRegions($conn, VERSION, MODE);
$regions = $dao->getAll();
if (get_lang() == "en") {
    if ($placeOfTheDay["name_en"] != "") {
        $placeOfTheDay["name"] = $placeOfTheDay["name_en"];
    }
    $placeOfTheDay["region"] = $placeOfTheDay["region_en"];
    $placeOfTheDay["description"] = $placeOfTheDay["description_en"];
    for ($i = 0; $i < count($lists); $i++) {
        $lists[$i]["name"] = $lists[$i]["name_en"];
    }
    for ($i = 0; $i < count($regions); $i++) {
        if ($regions[$i]["name_en"] != "") {
            $regions[$i]["name"] = $regions[$i]["name_en"];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo get_lang() ?>">

<head>
    <?php include 'header.php' ?>
    <link rel="stylesheet" href="<?php echo $BASE_URL ?>app/css/explore.css">
    <title><?php echo t('esplora') ?> | Nice Places</title>
</head>

<body>
    <div style="overflow-x: hidden">
        <!-- Prevent right space on mobile -->
        <div class="container-fluid">
            <div class="row" id="acton-bar">
                <div class="app-icon"></div>
                <div class="app-name">
                    <span><?php echo t("esplora") ?></span>
                </div>
                <div class="nav-item dropdown lang">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                        <a class="dropdown-item flag-it" href="<?php echo $BASE_URL ?>esplora/">
                            <img src="<?php echo $BASE_URL ?>assets/icons/italy.png"> Italiano</a>
                        <a class="dropdown-item flag-en" href="<?php echo $BASE_URL ?>en/explore/">
                            <img src="<?php echo $BASE_URL ?>assets/icons/united_kingdom.png"> English</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-4">
                    <div class="row">
                        <div class="col">
                            <h3><?php echo t("luogo-del-giorno") ?></h3>
                        </div>
                    </div>
                    <div class="row" id="place_of_the_day_bg" style="background-image: url('<?php echo $BASE_URL . "data/photos/" . MODE . "/" . $placeOfTheDay["image"] ?>')">
                        <div class="col-6">
                        </div>
                        <div class="col-6" style="padding-top: 10px; padding-bottom: 10px">
                            <div class="row">
                                <div class="col">
                                    <?php echo "<h4>" . $placeOfTheDay["name"] . "</h4>" ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <?php echo $placeOfTheDay["area"] . ", " . $placeOfTheDay["region"] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <?php
                                    $str = $placeOfTheDay["description"];
                                    echo (strlen($str) > 150) ? substr($str, 0, 150) . "..." : $str;
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <a href="<?php echo $BASE_URL . $placeOfTheDay["id_area"] . "/" . $placeOfTheDay["id_string"] ?>">
                                        <?php echo t("scopri-di-piu") ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <h3><?php echo t("in-primo-piano") ?></h3>
                        </div>
                    </div>
                    <?php
                    for ($i = 0; $i < count($lists); $i++) {
                        $link = $BASE_URL . $lists[$i]["id_string"] . "/";
                        if (get_lang() == "en") {
                            $link = $BASE_URL . "en/" . $lists[$i]["id_string_en"] . "/";
                        }
                        echo '<a href="' . $link . '">
                        <div class="row"><div class="col section-item">
                        <span>' . $lists[$i]["name"] . '</span>
                        <span class="float-right">(' . $lists[$i]["count"] . ')</span>
                        </div></div></a>';
                    }
                    ?>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="row">
                        <div class="col">
                            <h3><?php echo t("regioni") ?></h3>
                        </div>
                    </div>
                    <?php
                    for ($i = 0; $i < count($regions); $i++) {
                        $link = $BASE_URL . $regions[$i]["id_string"] . "/";
                        if (get_lang() == "en") {
                            $link = $BASE_URL . "en/" . $regions[$i]["id_string_en"] . "/";
                        }
                        echo '<a href="' . $link . '"><div class="row"><div class="col section-item">
                        <span>' . $regions[$i]["name"] . '</span>
                        <span class="float-right">(' . $regions[$i]["count"] . ')</span>
                        </div></div></a>';
                    }
                    ?>
                </div>
                <div class="d-none d-lg-block col-lg-4">
                    <div class="row">
                        <div class="col">
                            <h3><?php echo t("altri-luoghi") ?></h3>
                        </div>
                    </div>
                    <div class="row" id="random-places"></div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php' ?>
    <script src="<?php echo $BASE_URL ?>app/js/explore.js"></script>
</body>

</html>