<?php
require_once("ValidationInterface.php");
require_once("SuperType.php");

class TypeBool extends SuperType implements ValidationInterface {

  public function validateType() {
    if (boolval($this->value) !== $this->value) {
      return false;
    }
    return true;
  }

}
