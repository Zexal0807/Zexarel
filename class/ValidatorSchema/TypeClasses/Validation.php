<?php
class Validation {

  public function __construct(ValidationInterface $validator) {
    $this->validator = $validator;
  }

  public function validate($datatype) {
    if ($datatype != "boolean"){
      if ($this->validator->isBlank()) {
        return false;
      }
    }
    return $this->validator->validateType();
  }
  
}
