<?php
// Create 192x192 icon
$img192 = imagecreatetruecolor(192, 192);
$green = imagecolorallocate($img192, 16, 185, 129); // #10B981
$white = imagecolorallocate($img192, 255, 255, 255);

imagefill($img192, 0, 0, $green);

// Draw simple book shape
imagesetthickness($img192, 6);

// Left page
imageline($img192, 50, 50, 96, 56, $white);
imageline($img192, 96, 56, 96, 142, $white);
imageline($img192, 96, 142, 50, 142, $white);
imageline($img192, 50, 142, 50, 50, $white);

// Right page
imageline($img192, 142, 50, 96, 56, $white);
imageline($img192, 96, 56, 96, 142, $white);
imageline($img192, 96, 142, 142, 142, $white);
imageline($img192, 142, 142, 142, 50, $white);

// Center line
imageline($img192, 96, 56, 96, 142, $white);

imagepng($img192, __DIR__ . '/public/img/icons/icon-192.png');
imagedestroy($img192);

// Create 512x512 icon
$img512 = imagecreatetruecolor(512, 512);
$green512 = imagecolorallocate($img512, 16, 185, 129);
$white512 = imagecolorallocate($img512, 255, 255, 255);

imagefill($img512, 0, 0, $green512);
imagesetthickness($img512, 16);

// Draw simple book shape (scaled)
imageline($img512, 140, 140, 256, 150, $white512);
imageline($img512, 256, 150, 256, 372, $white512);
imageline($img512, 256, 372, 140, 372, $white512);
imageline($img512, 140, 372, 140, 140, $white512);

imageline($img512, 372, 140, 256, 150, $white512);
imageline($img512, 256, 150, 256, 372, $white512);
imageline($img512, 256, 372, 372, 372, $white512);
imageline($img512, 372, 372, 372, 140, $white512);

imageline($img512, 256, 150, 256, 372, $white512);

imagepng($img512, __DIR__ . '/public/img/icons/icon-512.png');
imagedestroy($img512);

echo "Icons created successfully!\n";
echo "192x192: public/img/icons/icon-192.png\n";
echo "512x512: public/img/icons/icon-512.png\n";
