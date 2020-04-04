<?php
require_once("ValidationInterface.php");
require_once("SuperType.php");

class TypeNumeric extends SuperType implements ValidationInterface {

  private $type = "int";

  public function setType($type){
    $this->type = $type;
  }

  public function validateType() {
    $f = false;
    switch($this->type){
      case "int":
        $f = filter_var($this->value, FILTER_VALIDATE_INT);
        break;
      case "float":
        $f = filter_var($this->value, FILTER_VALIDATE_FLOAT);
        break;
      default:
        return false;
    }
    if(!$f)
      return false;
    return true;
  }

}
