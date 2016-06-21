<html>
<head><title>test</title>
</head>
<body>

<?php

$base = '/var/www/magento';
$src = '/media/catalog/product/D/_/D_DistChain_temp_images2_20836_picto-0739.gif.gif';
$test = '/media/catalog/product/test/';

$name = basename($src);

$img = imagecreatefromgif($base . $src);

$srcw = 192;
$srch = 283;

$dstHeight = 96;
$dstWidth = floor($srcw * $dstHeight / $srch);

$newImage = imagecreate($dstWidth, $dstHeight);
imagecopyresampled(
            $newImage,
            $img,
            0, 0,
            0, 0,
            $dstWidth, $dstHeight,
            $srcw, $srch
        );
$def = $test . 'DEF_' . $name;
imagegif($newImage, $base . $def);

$newImagePng = imagecreatetruecolor($srcw, $srch);
imagecopy(
          $newImagePng,
          $img,
          0, 0,
          0, 0,
          $srcw, $srch
      );
$newImage2 = imagecreatetruecolor($dstWidth, $dstHeight);
$png = $test . 'PNG_' . $name . '.png';
imagepng($newImagePng, $base . $png);
imagecopyresampled(
            $newImage2,
            $newImagePng,
            0, 0,
            0, 0,
            $dstWidth, $dstHeight,
            $srcw, $srch
        );
$res = $test . 'RES_' . $name;
imagegif($newImage2, $base . $res);
$newImage3 = imagecreatetruecolor($dstWidth, $dstHeight);
imagecopyresampled(
            $newImage3,
            $img,
            0, 0,
            0, 0,
            $dstWidth, $dstHeight,
            $srcw, $srch
        );
$res2 = $test . 'RES2_' . $name;
imagegif($newImage3, $base . $res2);

?>

<p>Orig: <img src="<?php echo $src ?>" alt="orig" /></p>

<p>Default: <img src="<?php echo $def ?>" alt="defaut" /></p>

<p>New: <img src="<?php echo $res ?>" alt="new" /> (direct) <img src="<?php echo $res2 ?>" alt="new" /></p>


</body>
</html>

