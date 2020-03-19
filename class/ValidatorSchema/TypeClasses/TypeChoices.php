<?php
class TypeChoices extends Constraints implements TypeCheck {

  protected $error_nodes = [];

  protected $node_object;

  protected $inputjson;

  public function __construct($node_object, $inputjson) {
    $this->node_object = $node_object;
    $this->inputjson = $inputjson;
  }

  public function validateType() {
    if (in_array($this->inputjson, $this->node_object->choices)) {
      return true;
    }
    $this->error_nodes[] = sprintf("Invalid value for '%s'. Must be one of the values in {%s}", $this->node_object->name, join(",", $this->node_object->choices));
    return false;
  }

}
