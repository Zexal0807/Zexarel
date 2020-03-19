<?php
class TypeNumeric extends Constraints implements ValidationInterface {

  public function validateType() {
    if (!is_numeric($this->inputjson)) {
      $this->error_nodes[] = sprintf("Invalid data type for %s", $this->node_object->name);
      return false;
    }
    // TODO: isFloat, isDouble, isInt....
    return parent::numRangeCheck();
  }

}
