<?php
require("TypeClasses\ValidationInterface.php");

require("TypeClasses\Constraints.php");

require("TypeClasses\TypeBool.php");
require("TypeClasses\TypeText.php");
require("TypeClasses\TypeNumeric.php");
require("TypeClasses\TypeDate.php");

require("TypeClasses\Validation.php");

class ValidatorSchema {

  private $error_nodes = [];

  public $validated = true;

  public function validate($schema, $json_payload) {
    if (!is_array($schema)) {
      return false;
    }
    $this->recursive_walk($schema, $json_payload);
    return $this->validated;
  }

  private function recursive_walk($schema, $json_payload) {
    foreach ($schema as $nodes => $value) {
      $type = isset($value['type']) ? $value['type'] : 'object';

      $val = false;

      if (!isset($json_payload[$value['name']])) {
        $this->error_nodes[] = sprintf("'%s' does not exists.", $value['name']);
        $this->validated = false;
        return;
      }

      switch ($value['type']) {
        case 'text':
          $this->validated = (new Validation(new TypeText($value, $json_payload[$value['name']])))->validate();
          break;
        case 'numeric':
          $this->validated = (new Validation(new TypeNumeric($value, $json_payload[$value['name']])))->validate();
          break;
        case 'boolean':
          $this->validated = (new Validation(new TypeBool($value, $json_payload[$value['name']])))->validate();
          break;
        case 'date':
          $this->validated = (new Validation(new TypeDate($value, $json_payload[$value['name']])))->validate();
          break;
        case 'array':
          foreach ($json_payload[$value['name']] as $vv) {
            $this->recursive_walk($value['schema']['sub'], $vv);
          }
          break;
        case 'object':
          $this->recursive_walk($value['sub'], $json_payload[$value['name']]);
          break;
        default:
          $this->validated = false;
          return;
          break;
      }
    }
  }

  public function getErrors() {
    return $this->error_nodes;
  }

}
