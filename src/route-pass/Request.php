<?php

  require_once __DIR__ . "/Path.php";
  require_once __DIR__ . "/RequestRegistry.php";
  require_once __DIR__ . "/SessionRegistry.php";

  class Request {
    static function POST ($url, array $post = NULL, array $options = []) {
      $defaults = array(
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_URL => $url,
        CURLOPT_FRESH_CONNECT => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FORBID_REUSE => 1,
        CURLOPT_TIMEOUT => 4,
        CURLOPT_POSTFIELDS => http_build_query($post)
      );
    
      $chandler = curl_init();
      curl_setopt_array($chandler, ($options + $defaults));
      if (!$result = curl_exec($chandler)) {
        trigger_error(curl_error($chandler));
      }
      curl_close($chandler);
      return $result;
    }

    static function GET ($url, array $get = NULL, array $options = array()) {   
      $defaults = array(
        CURLOPT_URL => $url . ((strpos($url, '?') === FALSE) ? '?' : '') . http_build_query($get),
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 4
      );
       
      $chandler = curl_init();
      curl_setopt_array($chandler, ($options + $defaults));
      if (!$result = curl_exec($chandler)){
        trigger_error(curl_error($chandler));
      }
      curl_close($chandler);
      return $result;
    }



    public $body, $method, $res, $gateway, $session;
    function __construct (Response &$res) {
      $this->res = $res;

      $this->method = $_SERVER['REQUEST_METHOD'];

      $scriptPath = Path::breakdown($_SERVER['SCRIPT_FILENAME']);
      $this->gateway = end($scriptPath);

      $this->body = new RequestRegistry($this);

//      $this->session = new SessionRegistry($this);
//      foreach ($_SESSION as $key => $value) {
//        $this->session->$key = $value;
//      }


      // $isIncorrectHTTPMethod = !(strpos(
      //   $this->gateway,
      //   "-" . $this->method . ".php"
      // ) !== false);

      // if ($isIncorrectHTTPMethod) {
      //   $this->res->setStatusCode(Response::BAD_REQUEST);
      //   $this->res->error("Used HTTP method: " . $this->method . " does not match disered method of script: " . $this->gateway);
      // }


      $paramArrays = [&$_GET, &$_POST, &$_FILES];
      foreach ($paramArrays as $array) {
        foreach ($array as $key => $value) {
          $this->body->$key = $value;
        }
      }
    }
  }