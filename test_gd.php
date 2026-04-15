<?php
if (extension_loaded('gd')) {
    $img = imagecreatetruecolor(100, 100);
    if ($img) {
        echo "GD extension is working correctly.\n";
        imagedestroy($img);
    } else {
        echo "GD extension is loaded but failed to create an image.\n";
    }
} else {
    echo "GD extension is not loaded.\n";
}
