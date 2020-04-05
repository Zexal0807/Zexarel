<?php
require_once("TypeBool.php");
require_once("TypeText.php");
require_once("TypeNumeric.php");

class ValidatorSchema {

  public $validated = true;

  public function validate($schema, $data) {
    if (!is_array($schema)){
      $this->validated = false;
      return $this->validated;
    }
    foreach ($schema as $value) {
      $this->recursive_walk($value, $data);
    }
    return $this->validated;
  }

  private function recursive_walk($value, $input) {

    $rq = (isset($value['required']) ? $value['required'] : false);
    if(!$rq){
      return;
    }
    if($rq && !array_key_exists($value['name'], $input)){
      $this->validated = false;
      return;
    }

    $t = null;
    switch ($value['type']) {
      case 'ipv4':
      case 'ipv6':
      case 'mac':
      case 'email':
      case 'date':
      case 'time':
      case 'datetime':
      case 'string':
        $t = new TypeText($input[$value['name']]);
        $t->setType($value['type']);
        $t->setNullable(isset($value['nullable']) ? $value['nullable'] : false);
        $t->setEmpty(isset($value['empty']) ? $value['empty'] : false);
        $this->validated = $t->validate();
        break;
      case 'int':
      case 'float':
        $t = new TypeNumeric($input[$value['name']]);
        $t->setType($value['type']);
        $t->setNullable(isset($value['nullable']) ? $value['nullable'] : false);
        $this->validated = $t->validate();
        break;
      case 'boolean':
        $t = new TypeBool($input[$value['name']]);
        $this->validated = $t->validate();
        break;
      case 'array':
        $ass = isset($value['assoc']) ? $value['assoc'] : false;
        if($ass){
          foreach ($value['schema'] as $sub) {
            $this->recursive_walk($sub, $input[$value['name']]);
          }
        }else{
          $s = $value['schema'];
          for($i = 0; $i < sizeof($input[$value['name']]); $i++){
            $s['name'] = $i;
            $this->recursive_walk($s, [$i => $input[$value['name']][$i]]);
          }
        }
        break;
      default:
        $this->validated = false;
        break;
    }
  }
}
