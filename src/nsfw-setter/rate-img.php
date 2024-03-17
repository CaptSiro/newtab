<?php

if (!isset($_GET["type"])) {
    exit();
}

require_once __DIR__ . "/../constants.php";
require_once __DIR__ . "/../classes/Image.php";
require_once __DIR__ . "/../session.php";

$handle = fopen(__DIR__ . "/../fileMap.txt", "r");
/**
 * @var array<Image> $images
 */
$images = [];

while (($buffer = fgets($handle)) !== false) {
    if (strlen($buffer) < 4) {
        break;
    }
    $img = Image::parse($buffer);
    $images[$img->dirIndex . "/" . $img->fileName] = $img;
}

// load all pictures from directories
/**
 * @var array<Image> $backgrounds
 */
$backgrounds = [];

for ($i=0; $i < count(FILE_DIRECTORIES); $i++) {
    if (is_dir(FILE_DIRECTORIES[$i])) {
        if ($folder = opendir(FILE_DIRECTORIES[$i])) {
            while (($file = readdir($folder)) !== false) {
                if (!($file == "." || $file == "..")) {
                    $o = $i . "/" . $file;

                    if (isset($images[$o])) {
                        if (isset($images[$o]->view_type)) {
                            continue;
                        }

                        $backgrounds[] = $images[$o];
                    } else {
                        $backgrounds[] = Image::undocumented($i, $file);
                    }
                }
            }
            closedir($folder);
        }
    }
}

if (count($backgrounds) !== 0) {
    $img = array_shift($backgrounds);
    $img->view_type = SessionViewType::from($_GET["type"]);

    $key = $img->dirIndex . "/" . $img->fileName;
    $images[$key] = $img;

    $fp = fopen(__DIR__ . "/../fileMap.txt", "w");
    foreach ($images as $image) {
        fwrite($fp, $image->stringify() . "\n");
    }
    fclose($fp);
}
