<?php
class TypeBool extends Constraints implements ValidationInterface {

  public function validateType() {
    if (filter_var($this->inputjson, FILTER_VALIDATE_BOOLEAN, ['flags' => FILTER_NULL_ON_FAILURE]) === null) {
      $this->error_nodes[] = sprintf("Invalid data type for '%s'", $this->node_object->name);
      return false;
    }
    return true;
  }

}
