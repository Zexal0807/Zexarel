<?php
require_once("TypeBool.php");
require_once("TypeText.php");
require_once("TypeNumeric.php");

class ValidatorSchema
{

  public $validated = true;

  public function validate($schema, $data)
  {
    if (!is_array($schema)) {
      $this->validated = false;
    }
    foreach ($schema as $value) {
      $valid = $this->recursive_walk($value, $data);
      $this->validated = $this->validated && $valid;
    }
  }

  private function recursive_walk($value, $input)
  {

    $rq = (isset($value['required']) ? $value['required'] : false);
    if (!$rq) {
      return false;
    }
    if ($rq && !array_key_exists($value['name'], $input)) {
      return false;
    }

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
        $valid = $t->validate();
        return $valid;
      case 'int':
      case 'float':
        $t = new TypeNumeric($input[$value['name']]);
        $t->setType($value['type']);
        $t->setNullable(isset($value['nullable']) ? $value['nullable'] : false);
        $valid = $t->validate();
        return $valid;
      case 'boolean':
        $t = new TypeBool($input[$value['name']]);
        $valid = $t->validate();
        return $valid;
      case 'array':
        $ass = isset($value['assoc']) ? $value['assoc'] : false;
        $valid = true;
        if ($ass) {
          foreach ($value['schema'] as $sub) {
            $valid = $valid && $this->recursive_walk($sub, $input[$value['name']]);
          }
        } else {
          $s = $value['schema'];
          $t = true;
          for ($i = 0; $i < sizeof($input[$value['name']]); $i++) {
            $s['name'] = $i;
            $valid = $valid && $this->recursive_walk($s, [$i => $input[$value['name']][$i]]);
          }
        }
        return $valid;
      default:
        return false;
    }
  }
}
