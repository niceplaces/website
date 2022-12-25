<?php
/**
 * @par GET file: filename of the source image
 * @par GET w: width of the output image
 * @par GET h: height of the output image
 * @par GET shape: if is "circle", output the image as a circle
 * 
 * Partially copied from: https://gist.github.com/janzikan/2994977
 */
$DEBUG = false;

if (!$DEBUG){
    header('Content-Type: image/png');
} else {
    header('Content-Type: text/plain');
}

$im = null;

if (!isset($_GET["file"]) || strcmp($_GET["file"], "") == 0){
    $im =  imagecreatefromstring(file_get_contents("../assets/placeholder.jpg"));
} else if (str_starts_with($_GET['file'], "http")){
    $im =  imagecreatefromstring(file_get_contents($_GET['file']));
} else {
    $im =  imagecreatefromstring(file_get_contents('photos/'.$_GET['mode'].'/'.$_GET['file']));
}

if (isset($_GET["w"]) && isset($_GET["h"])){
    $width  = imagesx($im);
    $height = imagesy($im);
    if ($DEBUG){
        echo "Source image: ".$width."x".$height.PHP_EOL;
    }

    $scaleWidth = intval($_GET['w']);
    $scaleHeight = intval($_GET['h']);
    if ($DEBUG){
        echo "Output image: ".$scaleWidth."x".$scaleHeight.PHP_EOL;
    }

    // Calculate ratio of desired maximum sizes and original sizes.
    $widthRatio = $scaleWidth / $width;
    $heightRatio = $scaleHeight / $height;

    // Ratio used for calculating new image dimensions.
    $ratio = max($widthRatio, $heightRatio);

    // Calculate new image dimensions.
    $scaleWidth  = (int)$width  * $ratio;
    $scaleHeight = (int)$height * $ratio;

    /*$dWidth = abs($scaleWidth - $width);
    $dHeight = abs($scaleHeight - $height);
    if ($DEBUG){
        echo "Difference in width: ".$dWidth.PHP_EOL;
        echo "Difference in height: ".$dHeight.PHP_EOL;
    }

    if ($dHeight > $dWidth){
        if ($DEBUG){
            echo "Differnce in height is greater than diff. in width. Width adapted.".PHP_EOL;
        }
        $scaleWidth = round($scaleHeight * $width / $height); // w : h = nw : nh ---  nh * w / h
    } else if ($dHeight < $dWidth){
        if ($DEBUG){
            echo "Differnce in height is smaller than diff. in width. Height adapted.".PHP_EOL;
        }
        $scaleHeight = round($scaleWidth * $height / $width);
    }*/
    if ($DEBUG){
        echo "Resized image: ".$scaleWidth."x".$scaleHeight.PHP_EOL;
    }

    $im = imagescale($im, $scaleWidth, $scaleHeight, IMG_EFFECT_NORMAL);

    $centreX = round($scaleWidth / 2);
    $centreY = round($scaleHeight / 2);

    $cropWidth  = $_GET['w'];
    $cropHeight = $_GET['h'];
    $cropWidthHalf  = round($cropWidth / 2);
    $cropHeightHalf = round($cropHeight / 2);

    $x1 = max(0, $centreX - $cropWidthHalf);
    $y1 = max(0, $centreY - $cropHeightHalf);

    $temp = imagecreatetruecolor($cropWidth, $cropHeight);

    imagecopy($temp,$im,0,0,$x1,$y1,$scaleWidth,$scaleHeight);

    if ($cropWidth == $cropHeight){

        do {
            $r = rand(0, 255);
            $g = rand(0, 255);
            $b = rand(0, 255);
        }
        while (imagecolorexact($im, $r, $g, $b) < 0);

        if (isset($_GET["shape"]) && $_GET["shape"] == "circle"){
            $mask = imagecreatetruecolor($cropWidth, $cropHeight);
            $transparent = imagecolorallocate($mask, $r, $g, $b);
            $black = imagecolorallocate($mask, 0, 0, 0);
            imagefill($mask, 0, 0, $transparent);
            imagefilledellipse($mask, $cropWidthHalf, $cropHeightHalf, $cropWidth, $cropHeight, $black);
            imagecolortransparent($mask, $black);
            imageantialias($mask, true);
            imagecopymerge($temp, $mask, 0, 0, 0, 0, $cropWidth, $cropHeight, 100);
            imagecolortransparent($temp, $transparent);
        }
    }
    if (!$DEBUG){
        // Output the image
        imagepng($temp);
    }
    // Free up memory
    imagedestroy($temp);
} else {
    // Output the image
    imagepng($im);
}

imagedestroy($im);