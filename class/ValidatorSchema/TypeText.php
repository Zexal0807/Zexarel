<?php
require_once("ValidationInterface.php");
require_once("SuperType.php");

class TypeText extends SuperType implements ValidationInterface {

  private $type = "string";

  public function setType($type){
    $this->type = $type;
  }

  public function validateType() {
    if (strval($this->value) !== $this->value) {
      return false;
    }
    $f = true;
    switch($this->type){
      case "email":
        $f = filter_var($this->value, FILTER_VALIDATE_EMAIL);
        break;
      case "ipv4":
        $f = filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        break;
      case "ipv6":
        $f = filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        break;
      case "mac":
        $f = filter_var($this->value, FILTER_VALIDATE_MAC);
        break;
      case "date":
        $d = DateTime::createFromFormat('Y-m-d', $this->value);
        if($d == false){
          $f = false;
          break;
        }
        $f = ($d->getLastErrors()['error_count'] == 0);
        break;
      case "time":
        $t = DateTime::createFromFormat('H:i:s', $this->value);
        if($t == false){
          $f = false;
          break;
        }
        $f = ($t->getLastErrors()['error_count'] == 0);
        break;
    }
    return $f;
  }
}
