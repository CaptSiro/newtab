<?php

require_once __DIR__ . "/../constants.php";
require_once __DIR__ . "/../classes/Image.php";

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

/**
 * @var array<Image> $backgrounds
 */
// load all pictures from directories
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
    $image = array_shift($backgrounds);
    readfile(FILE_DIRECTORIES[intval($image->dirIndex)] . "\\" . $image->fileName);
} else {
    readfile(__DIR__ . "/bruh.gif");
}
