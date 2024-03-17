<?php

require_once __DIR__ . "/../absol/import.php";

require_once __DIR__ . "/../src/constants.php";

require_once __DIR__ . "/../src/route-pass/Path.php";
require_once __DIR__ . "/../src/route-pass/Response.php";
require_once __DIR__ . "/../src/route-pass/Request.php";

require_once __DIR__ . "/../src/session/Session.php";

$res = new Response();
$req = new Request($res);

global $res;
global $req;

function respond_with(Image $image, Request $request, Response $response): void {
    if ($request->body->get("ret") === "json") {
        $response->json($image);
    }

    $response->download($image->path());
}