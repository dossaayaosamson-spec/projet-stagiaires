<?php

require __DIR__.'/vendor/autoload.php';
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

$manager = new ImageManager(new Driver);
$heroDir = __DIR__.'/public/images/hero/';

foreach (['hero-1', 'hero-2', 'hero-3'] as $f) {
    $png = $heroDir.$f.'.png';
    $webp = $heroDir.$f.'.webp';
    if (file_exists($png)) {
        $img = $manager->decode(file_get_contents($png));
        $img->save($webp, quality: 80);
        echo "Converted $f\n";
    }
}
