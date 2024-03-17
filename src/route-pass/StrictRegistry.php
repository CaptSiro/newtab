<?php

  abstract class StrictRegistry {
    private $__map = [];

    abstract protected function propNotFound ($propName);
    abstract protected function setValue ($propName, $value): bool;



    public function get ($propName) {
      if (isset($this->__map[$propName])) {
        return $this->__map[$propName];
      }

      return null;
    }

    
    public function unset ($propName): void {
      unset($this->__map[$propName]);
    }


    public function isset ($propName): bool {
      return isset($this->__map[$propName]);
    }


    public function __get ($name) {
      $got = $this->get($name);
      if ($got === null) {
        $this->propNotFound($name);
      }

      return $got;
    }


    public function __set ($name, $value) {
      if ($this->setValue($name, $value)) {
        return $this->__map[$name] = $value;
      }

      return null;
    }
  }