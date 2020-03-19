<?php
class Validation {

  public function __construct(ValidationInterface $validator) {
    $this->validator = $validator;
  }

  public function validate() {
    if ($this->validator->isBlank()) {
      return false;
    }
    return $this->validator->validateType();
  }

}
