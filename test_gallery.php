<?php
$uploadDir = './uploads/';
$files = array_diff(scandir($uploadDir), array('.', '..'));

foreach ($files as $file) {
    $filePath = $uploadDir . $file;
    echo '<div>';
    echo '<img src="' . $filePath . '" alt="' . $file . '" style="max-width: 100px; max-height: 100px; margin: 10px;">';
    echo '</div>';
}
?>
