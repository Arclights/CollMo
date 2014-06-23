<?php
$im = imagecreatefromstring(file_get_contents($_GET['image']));
header('Content-Type: image/png');
imagepng($im);
imagedestroy($im);
?>