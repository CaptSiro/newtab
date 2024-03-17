<?php

global $req, $res;
require_once __DIR__ . "/api.php";

require_once __DIR__ . "/../src/constants.php";
require_once __DIR__ . "/../src/session/Session.php";
require_once __DIR__ . "/../src/data/Image.php";
require_once __DIR__ . "/../src/data/Database.php";

require_once __DIR__ . "/../src/fs.php";

ob_start();
$i = $_GET["i"];
$file = $_GET["file"];
$path = FILE_DIRECTORIES[$i] ."/$file";

$svt_lookup = Database::load(__DIR__ . "/../svt-lookup.txt");

foreach (backgrounds_iter() as $background_image) {
    if ($background_image->path() !== $path) {
        continue;
    }

    $image = $background_image->undocumented();

    if (isset($svt_lookup[$image->path()])) {
        $image->view_type = $svt_lookup[$image->path()]->view_type;
    }

    $image->document();

    Database::save();

    ob_clean();
    $res->json($image);
}
