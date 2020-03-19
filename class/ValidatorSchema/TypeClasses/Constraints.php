<?php
class Constraints {

  protected $error_nodes = [];

  protected $node_object;

  protected $inputjson;

  public function __construct($node_object, $inputjson) {
    $this->node_object = $node_object;
    $this->inputjson = $inputjson;
  }

  public function isBlank() {
    if (!isset($this->node_object->required)) {
      return false;
    }
    if ($this->node_object->required) {
      if (empty($this->inputjson)) {
        $this->error_nodes[] = sprintf("Value required for '%s'", $this->node_object->name);
        return true;
      }
    } else {
      if (empty($this->inputjson)) {
        return false;
      }
    }
    return false;
  }

  public function validateLength() {
    if (!isset($this->node_object->max_length) || !$this->node_object->max_length)
    return true;

    if (strlen($this->inputjson) > $this->node_object->max_length) {
      $this->error_nodes[] = sprintf("Invalid text length for '%s'", $this->node_object->name);
      return false;
    }

    return true;
  }
  public function getErrors() {
    return $this->error_nodes;
  }

}
