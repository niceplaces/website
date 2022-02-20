<?php
require_once "../utils.php";
?>
<!doctype html>
<html lang="<?php echo get_lang() ?>">

<head>
    <?php include 'header.php' ?>
    <link rel="stylesheet" href="<?php echo $BASE_URL ?>app/css/webapp.css">
    <title>Nice Places</title>
</head>

<body>
    <div class="background-image"></div>
    <div class="container">
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
                <a class="dropdown-item flag-it" href="<?php echo $BASE_URL . "app/" ?>">
                    <img src="<?php echo $BASE_URL ?>assets/icons/italy.png"> Italiano</a>
                <a class="dropdown-item flag-en" href="<?php echo $BASE_URL . "en/app/" ?>">
                    <img src="<?php echo $BASE_URL ?>assets/icons/united_kingdom.png"> English</a>
            </div>
        </div>
        <div class="row align-items-center text-center">
            <div class="col-sm-12 col-md-6">
                <img class="logo" src="<?php echo $BASE_URL ?>assets/logo_website.png">
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="row menu-links">
                    <div class="col">
                        <a href="http://www.niceplaces.it" target="_blank">
                            <img src="<?php echo $BASE_URL ?>app/res/web.png">
                        </a>
                        <a href="https://www.facebook.com/niceplacesapp/" target="_blank">
                            <img src="<?php echo $BASE_URL ?>app/res/facebook.png">
                        </a>
                        <a href="https://www.instagram.com/niceplacesapp/" target="_blank">
                            <img src="<?php echo $BASE_URL ?>app/res/instagram.png">
                        </a>
                    </div>
                </div>
                <?php
                $link = $BASE_URL . "app/luoghi-intorno-a-te";
                if (get_lang() == "en") {
                    $link = $BASE_URL . "en/app/places-around-you";
                }
                ?>
                <a href="<?php echo $link ?>">
                    <div class="row menu-button"><?php echo t("luoghi-intorno-a-te") ?></div>
                </a>
                <?php
                $link = $BASE_URL . "esplora/";
                if (get_lang() == "en") {
                    $link = $BASE_URL . "en/explore/";
                }
                ?>
                <a href="<?php echo $link ?>">
                    <div class="row menu-button"><?php echo t("esplora") ?></div>
                </a>
                <!--<a href="#">
                <div class="row menu-button">Nuovi luoghi</div>
            </a>-->
            </div>
        </div>
        <div class="row mb-3 d-flex justify-content-center">
            <a style="color: #185D42 !important; font-weight: bold; font-size: 12px; text-align: center" href="../privacy_policy.html">Privacy Policy
            </a>
        </div>
    </div>
    <?php include 'footer.php' ?>
</body>

</html>