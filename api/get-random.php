<?php

global $res, $req;
require_once __DIR__ . "/api.php";
require_once __DIR__ . "/../src/data/Database.php";
require_once __DIR__ . "/../src/availability.php";
require_once __DIR__ . "/../src/fs.php";



if (!$req->body->isset("theme") || $req->body->theme == "r") {
    try {
        $req->body->theme = random_int(0, 1)
            ? "l"
            : "d";
    } catch (Exception $e) {
        $req->body->theme = "d";
    }
}



$images = Database::load(DATA_FILE);
$session = Session::load();

/** @var array<Image> $filtered */
$filtered = [];

foreach (backgrounds_iter() as $background_image) {
    if (!isset($images[$background_image->path()])) {
        if ($session->view_type !== SessionViewType::SFW) {
            $filtered[] = $background_image->undocumented();
        }

        continue;
    }

    $image = $images[$background_image->path()];
    if (!is_available($image, $req, $session)) {
        continue;
    }

    $filtered[] = $image;
}


$count = count($filtered);

try {
    if ($count === 0) {
        throw new Exception();
    }

    $i = random_int(0, $count);
} catch (Exception $e) {
    $res->setStatusCode(Response::BAD_REQUEST);
    $res->error("Cannot serve random image, because none are available for current settings. [svt={$session->view_type->value}, theme={$req->body->theme}]\n");
    exit;
}

if ($filtered[$i]->state === ImageState::DOCUMENTED) {
    respond_with($filtered[$i], $req, $res);
}



$found = find_available($filtered, $i, $req, $session);
Database::save();

if (!isset($found)) {
//    $res->setStatusCode(Response::NOT_FOUND);
    $res->error("Could not find suitable image. Checked $count images.\n");
}

respond_with($filtered[$i], $req, $res);
