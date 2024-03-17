<?php

require_once __DIR__ . "/constants.php";
require_once __DIR__ . "/BackgroundImage.php";



/**
 * @return Generator<BackgroundImage>
 */
function backgrounds_iter(): Generator {
    for ($i = 0; $i < count(FILE_DIRECTORIES); $i++) {
        if (!is_dir(FILE_DIRECTORIES[$i])) {
            continue;
        }

        $dir_handle = opendir(FILE_DIRECTORIES[$i]);
        if ($dir_handle === false) {
            continue;
        }

        while (($file = readdir($dir_handle)) !== false) {
            if (!($file == "." || $file == "..")) {
                yield new BackgroundImage($i, $file);
            }
        }

        closedir($dir_handle);
    }
}



/**
 * @return array<BackgroundImage>
 */
function backgrounds(): array {
    return iterator_to_array(backgrounds_iter());
}
