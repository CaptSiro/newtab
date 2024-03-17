<?php

  require_once __DIR__ . "/RequestRegistry.php";

  class SessionRegistry extends RequestRegistry {
    function __construct (Request $request) {
      parent::__construct($request);

//      if (session_status() !== PHP_SESSION_ACTIVE) {
//        session_start();
//      }
    }

    protected function setValue ($propName, $value): bool {
      $_SESSION[$propName] = $value;
      return true;
    }
  }