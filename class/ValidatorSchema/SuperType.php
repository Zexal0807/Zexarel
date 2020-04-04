<?php
class SuperType {

  protected $node_object;

  protected $value;

  public function __construct($value) {
    $this->value = $value;
  }

  public function validate(){
    return $this->validateType();
  }

}
