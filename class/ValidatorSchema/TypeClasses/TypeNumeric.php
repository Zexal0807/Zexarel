<?php
class TypeNumeric extends Constraints implements TypeCheck {

  protected $error_nodes = [];

  protected $node_object;

  protected $inputjson;

  public function __construct($node_object, $inputjson) {
    $this->node_object = $node_object;
    $this->inputjson = $inputjson;
  }

  public function validateType() {
    if (!is_numeric($this->inputjson)) {
      $this->error_nodes[] = sprintf("Invalid data type for %s", $this->node_object->name);
      return false;
    }
    return parent::numRangeCheck();
  }

}
