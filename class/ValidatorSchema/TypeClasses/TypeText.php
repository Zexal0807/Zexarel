<?php
class TypeText extends Constraints implements ValidationInterface {

  public function validateType() {
    if (!is_string($this->inputjson)) {
      $this->error_nodes[] = sprintf("Invalid data type for '%s'", $this->node_object->name);
      return false;
    }
    // TODO: Add isIp, isEmail, isMAC....
    return parent::validateLength();
  }
}
