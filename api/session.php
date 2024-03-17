<?php

require_once __DIR__ . "/../src/session/Session.php";



function log_request(): void {
    $request = "";

    foreach ($_GET as $key => $value) {
        $request .= "$key: $value, ";
    }

    file_put_contents(__DIR__  ."/../session-logs.txt", "$request\n", FILE_APPEND);
}
//log_request();



const METHOD_RESET = "r";
const METHOD_SET_VIEW_TYPE = "svt";



if (!isset($_GET["m"])) {
    exit();
}

$session = Session::load();
$method = $_GET["m"];

switch ($method) {
    case METHOD_RESET: {
        if (!isset($_GET["sid"]) || $session->id === $_GET["sid"]) {
            break;
        }

        $session->id = $_GET["sid"];
        $session->view_type = SessionViewType::SFW;
        $session->save();
        break;
    }

    case METHOD_SET_VIEW_TYPE: {
        if (!isset($_GET["svt"])) {
            break;
        }

        try {
            $view_type = SessionViewType::from($_GET["svt"]);
            $session->view_type = $view_type;
            $session->save();

            echo $view_type->humanReadable();
        } catch (ValueError $error) {
            echo "Not known view type: $_GET[svt]";
        }

        break;
    }
}

