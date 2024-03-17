<?php

global $res, $req;
require_once __DIR__ . "/api.php";

$res->download(
    FILE_DIRECTORIES[intval($req->body->dir)]. "/" .$req->body->file
);