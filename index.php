<?php
require_once "utils.php";
?>
<!doctype html>
<html lang="<?php echo get_lang() ?>">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Phone icons and favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo $BASE_URL ?>assets/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo $BASE_URL ?>assets/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $BASE_URL ?>assets/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo $BASE_URL ?>assets/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $BASE_URL ?>assets/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo $BASE_URL ?>assets/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $BASE_URL ?>assets/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo $BASE_URL ?>assets/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $BASE_URL ?>assets/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo $BASE_URL ?>assets/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $BASE_URL ?>assets/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo $BASE_URL ?>assets/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $BASE_URL ?>assets/favicons/favicon-16x16.png">
    <link rel="manifest" href="<?php echo $BASE_URL ?>manifest.json">
    <meta name="msapplication-TileColor" content="#185D42">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#185D42">

    <link rel="stylesheet" href="<?php echo $BASE_URL ?>css/card_list.css">
    <link rel="stylesheet" href="<?php echo $BASE_URL ?>css/index.css?v=5">
    <link rel="stylesheet" href="<?php echo $BASE_URL ?>css/index_animations.css">
    <link rel="stylesheet" href="<?php echo $BASE_URL ?>css/carousel.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo $BASE_URL ?>lib/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Nice Places</title>
    <meta name="description" content="Nice Places è un'app che ti permette di conoscere i luoghi di interesse storico e culturale nelle tue vicinanze, offrendoti una descrizione sulle opere per soddisfare la tua curiosità." />
    <meta name="keywords" content="app, mobile, android, luoghi, esplora, italia, navigatore, turismo, toscana, siena" />
</head>

<body>
    <div style="overflow-x: hidden">
        <!-- Prevent right space on mobile -->
        <div class="background-image"></div>
        <!--<div class="row" id="video-background" style="position: absolute; top: 0; left: 0; right: 0">
        <video src="<?php /*echo $BASE_URL */ ?>assets/trailer.mp4" type="video/mp4" autoplay loop preload="auto" muted>
        </video>
    </div>-->
        <div class="container">
            <nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light">
                <div class="container container-navbar">
                    <a class="navbar-brand" href="#"><img id="logo" src="<?php echo $BASE_URL ?>assets/logo_website.png" /></a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="flex-row-reverse collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="#home">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="http://www.niceplaces.it/blog/">Blog</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#progetto"><?php echo t("il-progetto") ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#funzioni"><?php echo t("funzioni") ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#screenshot"><?php echo t("screenshot") ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#luoghi"><?php echo t("luoghi") ?></a>
                            </li>
                            <li class="nav-item dropdown lang">
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
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item flag-it" href="<?php echo $BASE_URL ?>">
                                        <img src="<?php echo $BASE_URL ?>assets/icons/italy.png"> Italiano</a>
                                    <a class="dropdown-item flag-en" href="<?php echo $BASE_URL ?>en/">
                                        <img src="<?php echo $BASE_URL ?>assets/icons/united_kingdom.png"> English</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <section>
                <div class="row">
                    <div class="col">
                        <a class="anchor" name="home"></a>
                    </div>
                </div>
                <div class="row home align-items-center">
                    <div class="col-md-12 col-lg-6">
                        <p class="slogan"><?php echo t("slogan") ?></p>
                        <p class="sub-slogan"><?php echo t("sub-slogan") ?></p>
                        <p class="p-badge">
                            <?php switch (get_lang()) {
                                case "it": ?>
                                    <a href='https://play.google.com/store/apps/details?id=com.niceplaces.niceplaces'>
                                        <img alt='Disponibile su Google Play' class='lang-it google-play-badge-it' src='https://play.google.com/intl/en_us/badges/images/generic/it_badge_web_generic.png' />
                                    </a>
                                    <a href="<?php echo $BASE_URL ?>app/">
                                        <img alt='Disponibile come web app' class='lang-it google-play-badge' src='<?php echo $BASE_URL ?>assets/webapp-badge.png' />
                                    </a>
                                <?php break;
                                case "en": ?>
                                    <a href='https://play.google.com/store/apps/details?id=com.niceplaces.niceplaces'>
                                        <img alt='Get it on Google Play' class='google-play-badge' src='https://play.google.com/intl/en_us/badges/images/generic/en_badge_web_generic.png' />
                                    </a>
                                    <a href="<?php echo $BASE_URL ?>en/app/">
                                        <img alt='Available as web app' class='google-play-badge' src='<?php echo $BASE_URL ?>assets/webapp-badge.png' />
                                    </a>
                            <?php break;
                            } ?>
                        </p>
                        <div class="text-center">
                            <a class="social-icon" href="https://www.facebook.com/niceplacesapp/">
                                <i class="fab fa-facebook fa-2x"></i>
                            </a>
                            <a class="social-icon" href="https://www.instagram.com/niceplacesapp/">
                                <i class="fab fa-instagram fa-2x"></i>
                            </a>
                            <a class="social-icon" href="https://twitter.com/niceplacesapp">
                                <i class="fab fa-twitter fa-2x"></i>
                            </a>
                            <a class="social-icon" href="https://t.me/niceplacesapp/">
                                <i class="fab fa-telegram fa-2x"></i>
                            </a>
                            <a class="social-icon" href="https://discord.gg/p9fC72mzDX">
                                <i class="fab fa-discord fa-2x"></i>
                            </a>
                            <a class="social-icon" href="https://github.com/niceplaces">
                                <i class="fab fa-github fa-2x"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-6 text-center">
                        <img class="main-screen" src="<?php echo $BASE_URL ?>assets/devices-<?php echo get_lang() ?>.png">
                    </div>
                </div>
            </section>
            <section>
                <div class="row">
                    <div class="col">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/soq79b7UISo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </section>
            <section>
                <div class="row">
                    <div class="col">
                        <a class="anchor" name="progetto"></a>
                        <h2 class="section-header"><?php echo t("il-progetto") ?></h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <?php switch (get_lang()) {
                            case "it": ?>
                                <p>La ricchezza artistica, storica e culturale è un bene molto prezioso per tutto il nostro
                                    Paese, che
                                    secondo questo parametro si pone al primo posto al mondo. </p>
                                <p>Noi crediamo che conoscere la storia e le particolarità dei numerosi luoghi intorno a noi sia
                                    un modo
                                    per apprezzare di più ciò che ci circonda.
                                    Può poi essere anche un modo divertente per viaggiare, appassionarsi e scoprire luoghi anche
                                    poco
                                    conosciuti, ma
                                    non per questo meno interessanti.</p>
                                <p>Con Nice Places, per esempio, si può passeggiare nel centro storico di una città, si può
                                    consultare
                                    l'app per scoprire quali sono i luoghi da visitare, sceglierne uno e attivare il navigatore
                                    per
                                    raggiungerlo e
                                    quando siamo nei pressi di un punto di interesse, si può aprire la
                                    descrizione sul cellulare per avere una breve panoramica di ciò che stiamo osservando.</p>
                                <blockquote>
                                    <div class="quote">Sono meraviglie distribuite un po' in tutte le nostre regioni, cosa rara
                                        perché
                                        all'estero non è così. E poi sono meraviglie distribuite un po' in tutto il tempo:
                                        si parte dall'antichità, per arrivare poi al Medioevo e poi al Rinascimento, '600, '700,
                                        '800 e
                                        oltre.
                                        Anche questa è una rarità perché all'estero ci sono delle meraviglie ma, di solito, sono
                                        confinate a
                                        certi periodi specifici in ogni Paese. Da noi no, da noi ogni generazione, da più di
                                        venticinque
                                        secoli è stata capace di creare delle opere che emozionano, delle opere che l'UNESCO
                                        definisce
                                        "patrimonio dell'umanità" e questo ci rende certo molto orgogliosi di essere italiani,
                                        però ci obbliga anche a dover tutelare, proteggere, valorizzare questo patrimonio e
                                        soprattutto
                                        regalarlo intatto alle generazioni successive di tutto il pianeta.
                                    </div>
                                    <div class="author">
                                        <small>Alberto Angela, "Meraviglie", Rai 2018</small>
                                    </div>
                                </blockquote>
                            <?php break;
                            case "en": ?>
                                <p>The artistic, historical and cultural richness is a very precious asset for all of our
                                    country, Italy.
                                    According to this parameter, Italy is the first country in the world. </p>
                                <p>We believe that knowing the history and the particularities of the numerous places around us
                                    is a way
                                    to appreciate more what surrounds us.
                                    It can also be a fun way to travel, get excited and discover places that are not very well
                                    known, but
                                    no less interesting.</p>
                                <p>With Nice Places, for example, you can walk in the historic center of a city, you can consult
                                    the app
                                    to find out which places to visit, choose one and activate the navigator to reach it and
                                    when you are
                                    near a point of interest, you can open the description on the mobile phone to get a brief
                                    overview of
                                    what you are observing.</p>
                                <blockquote>
                                    <div class="quote">They are wonders distributed a little in all our regions, which is rare
                                        because
                                        abroad it's not
                                        like that, and then they are distributed wonders all the time: it starts from antiquity,
                                        then to the
                                        Middle Ages and then to the Renaissance, '600,' 700, '800 and beyond.
                                        This is also a rarity because abroad there are wonders but, usually, they are confined
                                        to
                                        certain
                                        specific periods in each country. In Italy every generation, for more than twenty-five
                                        centuries has been able to create works that excite, of the works that UNESCO defines
                                        "heritage of
                                        humanity" and this makes us very proud of being Italian, but it also obliges us to
                                        protect
                                        and enhance
                                        this heritage and above all give it intact to the next generations of the entire planet.
                                    </div>
                                    <div class="author">
                                        <small>Alberto Angela, "Meraviglie", Rai 2018</small>
                                    </div>
                                </blockquote>
                        <?php break;
                        } ?>
                    </div>
                </div>
            </section>
            <section>
                <div class="row">
                    <div class="col">
                        <a class="anchor" name="funzioni"></a>
                        <h2 class="section-header"><?php echo t("funzioni") ?></h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <h3><?php echo t("luoghi-intorno-a-te") ?></h3>
                        <p class="p-function">
                            <?php switch (get_lang()) {
                                case "it": ?>
                                    Con i servizi di localizzazione del tuo smartphone puoi vedere una mappa che mostra
                                    dove si trovano i luoghi registrati su Nice Places. In basso si trova una lista con i luoghi ordinati in base alla distanza:
                                    i
                                    primi sono più vicini.
                                    Toccando un luogo sulla mappa o nella lista puoi aprire la scheda del luogo e successivamente il
                                    navigatore.
                                <?php break;
                                case "en": ?>
                                    Using location services provided by your mobile phone you can view a map that shows where you can
                                    find the places that are registered on Nice Places. Below the map you can view a list of places
                                    ordered by distance from you.
                                    Touching a place on the map or from the list you can open the card of the place and then the
                                    navigator.
                            <?php } ?></p>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-6">
                        <h3><?php echo t("esplora") ?></h3>
                        <p class="p-function">
                            <?php switch (get_lang()) {
                                case "it": ?>
                                    Con questa funzione si può consultare la lista dei luoghi divisi per area di
                                    appartenenza. Anche da qui,
                                    con un "tap" su un luogo si può aprire la relativa scheda e consultarne la descrizione.
                                <?php break;
                                case "en": ?>
                                    With this feature you can consult the list of places divided by areas. Touching a place on the list,
                                    like the previous function, you can open its card and read the description.
                            <?php } ?></p>
                    </div>
                    <div class="col-md-12 col-lg-6">
                        <h3><?php echo t("nuovi-luoghi") ?></h3>
                        <p class="p-function">
                            <?php switch (get_lang()) {
                                case "it": ?>
                                    Aggiungiamo periodicamente nuovi punti di interesse e nuove descrizioni. Con questa funzione puoi
                                    vedere quali sono gli ultimi luoghi inseriti e gli ultimi luoghi aggiornati.
                                <?php break;
                                case "en": ?>
                                    We periodically add new points of interest and new descriptions. With this function you can see
                                    which are the latest places inserted and the latest places updated.
                            <?php } ?>

                        </p>
                    </div>
                    <div class="col-md-12 col-lg-6">
                        <h3><?php echo t("notifiche") ?></h3>
                        <p class="p-function">
                            <?php switch (get_lang()) {
                                case "it": ?>
                                    L'applicazione ci permette di comunicarti tutte le ultime novità su Nice Places tramite le notifiche
                                    sul tuo dispositivo, in questo modo potrai rimanere sempre aggiornato! Se però non hai un dispositivo
                                    Android su cui installare l'app, ti consigliamo di iscriverti al nostro
                                    <a href="https://t.me/niceplacesapp/">canale Telegram</a> per
                                    ricevere comunque i nostri aggiornamenti.
                                <?php break;
                                case "en": ?>
                                    The application allows us to tell you all the latest news about Nice Places through notifications
                                    on your device, so you can stay always up-to-date!
                            <?php } ?>
                        </p>
                    </div>
                </div>
            </section>
            <section>
                <div class="row">
                    <div class="col">
                        <a class="anchor" name="screenshot"></a>
                        <h2 class="section-header"><?php echo t("screenshot") ?></h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="row text-center my-3">
                            <div class="row mx-auto my-auto">
                                <div id="recipeCarousel" class="carousel slide w-100" data-ride="carousel">
                                    <div class="carousel-inner w-100" role="listbox">
                                        <?php
                                        $img = array(
                                            "screen1.png", "screen2.png", "screen3.png", "screen4.png", "screen5.png",
                                            "screen6.png", "screen7.png", "screen8.png"
                                        );
                                        for ($i = 0; $i < count($img); $i++) {
                                            echo
                                            '<div class="carousel-item ' . (($i == 0) ? "active" : "") . '">
                                        <img class="d-block col-lg-3 col-md-4 col-sm-6 img-fluid"
                                             src="' . $BASE_URL . 'assets/screens/' . get_lang() . '/' . $img[$i] . '">
                                    </div>';
                                        }
                                        ?>
                                    </div>
                                    <a class="carousel-control-prev" href="#recipeCarousel" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#recipeCarousel" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="container-fluid" style="padding-left: 0; padding-right: 0">
    <section>
        <div class="row">
            <div class="col">
                <a class="anchor" name="luoghi"></a>
                <h2 class="section-header"><?php echo t("luoghi") ?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <iframe frameborder="0" height="600px" width="100%" src="<?php echo $BASE_URL ?>map.php">
                </iframe>
            </div>
        </div>
    </section>
</div>
        <div class="container" style="padding-top: 0">
            <section>
                <div class="row">
                    <div class="col">
                        <a class="anchor" name="contatti"></a>
                        <h2 class="section-header"><?php echo t("contatti") ?></h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <p>
                            <?php switch (get_lang()) {
                                case "it": ?>
                                    Se vuoi sapere di più su di noi o sul progetto, se vuoi segnalarci un problema oppure <i>se vuoi
                                        collaborare
                                        con noi</i> puoi contattarci inviando un messaggio alla nostra pagina Facebook o al nostro
                                    profilo
                                    Instagram, oppure
                                    inviaci una e-mail a <a href="mailto:niceplacesit@gmail.com">niceplacesit@gmail.com</a>.</p>
                    <?php break;
                                case "en": ?>
                        If you want to know more about us or about the project, if you want to report a problem or <i>if you
                            want to collaborate with
                            us</i> you can contact us by sending a message to our Facebook page, to our
                        Instagram profile, or send us an e-mail to <a href="mailto:niceplacesit@gmail.com">niceplacesit@gmail.com</a>.
                <?php } ?>
                    </div>
                </div>
            </section>
            <section>
                <div class="row">
                    <div class="col">
                        <a class="anchor" name="partners"></a>
                        <h2 class="section-header"><?php echo t("parlano-di-noi") ?></h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <blockquote>
                            <h4>
                                <?php switch (get_lang()) {
                                    case "it": ?>
                                        <div class="quote">Vi invito a provarla: sono pronto a scommettere che vi piacerà!</div>
                                    <?php break;
                                    case "en": ?>
                                        <div class="quote">I invite you to try it: I am ready to bet you will like it!</div>
                                <?php } ?>
                            </h4>
                            <h6>
                                <div class="author">
                                    <a href="https://www.zerozone.it/provati-per-voi/nice-places-lapp-per-scoprire-posti-nuovi/14320" target="_blank">
                                        <h6>Il Blog di Michele Pinassi</h6>
                                    </a>
                                </div>
                            </h6>
                        </blockquote>
                        <blockquote>
                            <h4>
                                <?php switch (get_lang()) {
                                    case "it": ?>
                                        <div class="quote">Consiglio un bel giro di prova, ne vale la pena <span style="font-style: normal">😉</span></div>
                                    <?php break;
                                    case "en": ?>
                                        <div class="quote">I recommend a test spin, it's worth it <span style="font-style: normal">😉</span></div>
                                <?php } ?>
                            </h4>
                            <h6>
                                <div class="author">
                                    <a href="https://xantarmob.altervista.org/nice-places-una-guida-artistica-italiana-da-provare-su-android/" target="_blank">
                                        Xantarmob
                                    </a>
                                </div>
                            </h6>
                        </blockquote>
                    </div>
                </div>
            </section>
            <?php
            function contributor($name, $img, $link)
            {
            ?>
                <div class="col-12 col-sm-4 col-lg-3">
                    <div style="margin: 10px">
                        <div class="row">
                            <div class="col d-flex justify-content-center">
                                <a href="<?php echo $link ?>">
                                    <div class="profile-picture" style="background-image: url(<?php echo $img ?>)">
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col d-flex justify-content-center">
                                <a style="font-size: 12px" href="<?php echo $link ?>"><?php echo $name ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php
            function contributor_small($name, $img, $link)
            {
            ?>
                <div style="margin: 10px">
                        <div class="row">
                            <div class="col d-flex justify-content-center">
                                <a href="<?php echo $link ?>">
                                    <div title="<?php echo $name ?>" class="profile-picture-small" style="background-image: url(<?php echo $img ?>)">
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
            <?php } ?>
            <section>
                <div class="row">
                    <div class="col">
                        <h2 class="section-header"><?php echo t("contributori") ?></h2>
                    </div>
                </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="colrow">
                    <div class="col" style="margin-bottom: 10px">
                        <h5 style="text-align: center"><?php echo t("persone") ?></h5>
                    </div>
                </div>
                <div class="row about-us">
                    <div class="col d-flex justify-content-center">
                        <?php
                        contributor_small(
                            "Lorenzo",
                            $BASE_URL . "assets/profile-pictures/lorenzo.jpg",
                            "https://www.lorenzovainigli.com"
                        );
                        contributor_small(
                            "Dario",
                            $BASE_URL . "assets/profile-pictures/dario.jpg",
                            "https://www.instagram.com/chesidario/"
                        );
                        contributor_small(
                            "Francesca",
                            $BASE_URL . "assets/profile-pictures/francesca.jpg",
                            "https://www.linkedin.com/in/f-mecacci/"
                        );
                        contributor_small(
                            "Melania",
                            $BASE_URL . "assets/profile-pictures/melania.jpg",
                            "https://www.facebook.com/melania.anichini"
                        );
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="colrow">
                    <div class="col" style="margin-bottom: 10px">
                        <h5 style="text-align: center"><?php echo t("associazioni") ?></h5>
                    </div>
                </div>
                <div class="row justify-content-center about-us">
                    <?php
                    contributor_small(
                        "Pro Loco Sovicille",
                        $BASE_URL . "assets/icons/proloco.png",
                        "https://www.prolocosovicille.it/"
                    );
                    contributor_small(
                        "Cammino d'Etruria",
                        $BASE_URL . "assets/icons/cammino-d-etruria.jpg",
                        "https://www.facebook.com/camminodetruria/"
                    );
                    contributor_small(
                        "Pro Loco Murlo",
                        $BASE_URL . "assets/icons/proloco-murlo.jpeg",
                        "https://prolocomurlo.it/"
                    );
                    ?>
                </div>
            </div>
        </div>
        </section>
        <section class="mt-5">
            <div class="row counters">
                <div class="col">
                    <h4><?php echo t("luoghi") ?></h4>
                    <h1 class="counter-plus" data-count="500">0</h1>
                </div>
                <div class="col">
                    <h4><?php echo t("descrizioni") ?></h4>
                    <h1 class="counter-plus" data-count="200">0</h1>
                </div>
                <div class="col">
                    <h4><?php echo t("download") ?></h4>
                    <h1 class="counter-plus" data-count="1100">0</h1>
                </div>
            </div>
        </section>
        <div class="row mb-3" style="text-align: center">
            <div class="col col-net-link text-center">
                <a class="social-icon" href="https://www.facebook.com/niceplacesapp/">
                    <i class="fab fa-facebook fa-2x"></i>
                </a>
                <a class="social-icon" href="https://www.instagram.com/niceplacesapp/">
                    <i class="fab fa-instagram fa-2x"></i>
                </a>
                <a class="social-icon" href="https://twitter.com/niceplacesapp">
                    <i class="fab fa-twitter fa-2x"></i>
                </a>
                <a class="social-icon" href="https://t.me/niceplacesapp/">
                    <i class="fab fa-telegram fa-2x"></i>
                </a>
                <a class="social-icon" href="https://discord.gg/p9fC72mzDX">
                    <i class="fab fa-discord fa-2x"></i>
                </a>
                <a class="social-icon" href="https://github.com/niceplaces">
                    <i class="fab fa-github fa-2x"></i>
                </a>
            </div>
        </div>
        <div class="row mb-2" style="text-align: center">
            <div class="col col-net-link text-center">
                <?php switch (get_lang()) {
                    case "it": ?>
                        <a href="https://www.niceplaces.it/privacy_policy.html">Privacy Policy</a>
                    <?php break;
                    case "en": ?>
                        <a href="https://www.niceplaces.it/en/privacy_policy.html">Privacy Policy</a>
                <?php } ?>
            </div>
        </div>
    </div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="<?php echo $BASE_URL ?>lib/js/jquery-3.3.1.min.js"></script>
    <script src="<?php echo $BASE_URL ?>lib/js/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="<?php echo $BASE_URL ?>lib/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="<?php echo $BASE_URL ?>lib/js/jquery.fitvids.js"></script>
    <script src="<?php echo $BASE_URL ?>js/index.js?v=1"></script>
    <script src="<?php echo $BASE_URL ?>js/carousel.js"></script>
    <?php include "data/protected/ganalytics.php" ?>
    <script type="text/javascript" id="cookieinfo" src="//cookieinfoscript.com/js/cookieinfo.min.js" data-bg="#645862" data-fg="#FFFFFF" data-message="<?php echo t("usiamo-i-cookie") ?>" data-link="#F1D600" data-linkmsg="" data-cookie="CookieInfoScript" data-text-align="left" data-close-text="<?php echo t("ho-capito") ?>">
    </script>
    <script src="https://kit.fontawesome.com/70291d2ec9.js" crossorigin="anonymous"></script>
</body>

</html>