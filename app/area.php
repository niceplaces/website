<?php
require_once "../data/requires.php";
require_once '../utils.php';
error_reporting(E_ALL && ~E_NOTICE);

const IS_REGION = 0;
const IS_AREA = 1;
const IS_LIST = 2;

$conn = mySqlConnect();
$daoRegions = new DaoRegions($conn, VERSION, MODE);
$daoAreas = new DaoAreas($conn, VERSION, MODE);
$daoPlaces = new DaoPlacesV3($conn, VERSION, MODE);
$daoLists = new DaoLists($conn, VERSION, MODE);

$TYPE = IS_REGION;
$id = $_GET["id"];
$id_string = $daoRegions->getOne($id)["id_string"];
$id_string_en = $id_string;
if ($daoRegions->getOne($id)["id_string_en"] != "") {
    $id_string_en = $daoRegions->getOne($id)["id_string_en"];
}
$name = $daoRegions->getOne($id)["name"];
if (get_lang() == "en" && $daoRegions->getOne($id)["name_en"] != "") {
    $name = $daoRegions->getOne($id)["name_en"];
}
$items = $daoAreas->getByRegionIdString($id_string);
if (count($items) == 0) {
    $TYPE = IS_AREA;
    $id_string = $daoAreas->getOne($id)["id_string"];
    $id_string_en = $id_string;
    if ($daoAreas->getOne($id)["id_string_en"] != "") {
        $id_string_en = $daoAreas->getOne($id)["id_string_en"];
    }
    $name = $daoAreas->getOne($id)["name"];
    if (get_lang() == "en" && $daoAreas->getOne($id)["name_en"] != "") {
        $name = $daoAreas->getOne($id)["name_en"];
    }
    $items = $daoPlaces->getByAreaIdString($id_string);
}
if (count($items) == 0) {
    $TYPE = IS_LIST;
    $id_string = $daoLists->getOne($id)["id_string"];
    $id_string_en = $id_string;
    if ($daoLists->getOne($id)["id_string_en"] != "") {
        $id_string_en = $daoLists->getOne($id)["id_string_en"];
    }
    $name = $daoLists->getOne($id)["name"];
    if (get_lang() == "en" && $daoLists->getOne($id)["name_en"] != "") {
        $name = $daoLists->getOne($id)["name_en"];
    }
    $items = $daoLists->getPlacesByListIdString($id_string);
}
?>
<!DOCTYPE html>
<html lang="<?php echo get_lang() ?>">

<head>
    <?php include 'header.php' ?>
    <link rel="stylesheet" href="<?php echo $BASE_URL ?>app/css/explore.css">
    <title><?php echo $name ?> | Nice Places</title>
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
                        <a class="dropdown-item flag-it" href="<?php echo $BASE_URL . $id_string ?>/">
                            <img src="<?php echo $BASE_URL ?>assets/icons/italy.png"> Italiano</a>
                        <a class="dropdown-item flag-en" href="<?php echo $BASE_URL . "en/" . $id_string_en ?>/">
                            <img src="<?php echo $BASE_URL ?>assets/icons/united_kingdom.png"> English</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <h3><?php echo $name ?></h3>
                </div>
            </div>
            <?php
            if ($TYPE == IS_REGION) {
                for ($i = 0; $i < count($items); $i++) {
                    $item_name = $items[$i]["name"];
                    $link = $BASE_URL . $items[$i]["id_string"];
                    if (get_lang() == "en") {
                        if ($items[$i]["name_en"] != "") {
                            $item_name = $items[$i]["name_en"];
                        }
                        $link = $BASE_URL . "en/" . $items[$i]["id_string_en"];
                    }
                    echo '<a href="' . $link . '/"><div class="row"><div class="col section-item">
                    <span>' . $item_name . '</span>
                    <span class="float-right">(' . $items[$i]["count"] . ')</span>
                    </div></div></a>';
                }
            } else if ($TYPE == IS_AREA || $TYPE == IS_LIST) {
                for ($i = 0; $i < count($items); $i++) {
                    $item_name = $items[$i]["name"];
                    $link = $BASE_URL . $items[$i]["id_area"] . '/' . $items[$i]["id_string"];
                    if (get_lang() == "en") {
                        if ($items[$i]["name_en"] != "") {
                            $item_name = $items[$i]["name_en"];
                        }
                        $link = $BASE_URL . "en/" . $items[$i]["id_area_en"] . '/' . $items[$i]["id_string_en"];
                    }
                    $bg_image_50x50 = $items[$i]["image"] != "" ? 'style="background-image: url(' . $BASE_URL . 'data/image.php?file=' . $items[$i]["image"] . '&w=50&h=50)"' : "";
                    /*$bg_image_720p = $items[$i]["image"] != "" ? 'style="background-image: url(' . $BASE_URL .'data/image.php?file=' . $items[$i]["image"] . '&w=1280&h=720)"' : "";
            echo '<div class="d-none d-md-none col-md-6 col-lg-4">
                    <a href="' . $link . '">
                    <div class="card-container">
                    <div class="place_image lazy" ' . $bg_image_720p . '> 
                        <span class="float-right">';
            if ($items[$i][(get_lang() == "it") ? "has_description" : "has_description_en"]) {
                switch ($items[$i]["author"]) {
                    case 1:
                        echo '<img class="author-badge" src="' . $BASE_URL . '/assets/logo_website.png">';
                        break;
                    case 2:
                        echo '<img class="author-badge" src="' . $BASE_URL . '/assets/icons/proloco.png">';
                        break;
                    case 3:
                        echo '<img class="author-badge" src="' . $BASE_URL . '/assets/icons/via_sacra_etrusca.png">';
                        break;
                }
            }
            echo '</span>
                        <div class="place_name_cont">';
            if (IS_LIST && $id_string == "top-100"){
                echo '<div class="place_name">' . ($i+1) . ". " . $item_name . ' (' . $items[$i]["area"] .')</div>';
            } else {
                echo '<div class="place_name">' . $item_name . '</div>';
            }
            echo        '</div> 
                    </div> 
                    </div>
                    </a>
                    </div>';*/
            ?>
                    <div class="row">
                        <div class="d-block d-md-block col section-item">
                            <a href="<?php echo $link ?>">
                                <table style="width: 100%">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="img lazy" <?php echo $bg_image_50x50 ?>></div>
                                            </td>
                                            <td style="width: 100%; padding: 8px">
                                            <div style="display: inline;">
                                                <?php
                                                if (IS_LIST && $id_string == "top-100") {
                                                    echo ($i + 1) . ". " . $item_name;
                                                } else {
                                                    echo $item_name;
                                                } ?>
                                            </div>
                                            </td>
                                            <td>
                                                <?php
                                                if ($items[$i][(get_lang() == "it") ? "has_description" : "has_description_en"]) {
                                                    switch ($items[$i]["author"]) {
                                                        case 1:
                                                            echo '<img class="author-badge" src="' . $BASE_URL . '/assets/logo_website.png">';
                                                            break;
                                                        case 2:
                                                            echo '<img class="author-badge" src="' . $BASE_URL . '/assets/icons/proloco.png">';
                                                            break;
                                                        case 3:
                                                            echo '<img class="author-badge" src="' . $BASE_URL . '/assets/icons/via_sacra_etrusca.png">';
                                                            break;
                                                    }
                                                } ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </a>
                        </div>
                        </a>
                    </div>
                <?php } ?>
        </div>
    <?php } ?>
    </div>
    </div>
    </div>
    </div>
    <?php include 'footer.php' ?>
</body>

</html>