<?php
class TypeDate extends Constraints implements ValidationInterface {

  public function validateType() {
    if (!strtotime($this->inputjson)) {
      $this->error_nodes[] = sprintf("Invalid data type for %s. Acceptable format: YYYY-MM-DD HH:ii:ss", $this->node_object->name);
      return false;
    }
    // TODO: <>= date
    return true;
  }

}
