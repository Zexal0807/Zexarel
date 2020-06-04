<?php
require_once("ValidationInterface.php");
require_once("SuperType.php");

class TypeNumeric extends SuperType implements ValidationInterface {

  private $type = "int";

  private $nullable = false;

  public function setType($type){
    $this->type = $type;
  }

  public function setNullable($nullable){
    $this->nullable = $nullable;
  }

  public function validateType() {
    if($this->nullable && $this->value == null){
      return true;
    }
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
    if($f === false){
      return false;
    }
    return true;
  }

}
