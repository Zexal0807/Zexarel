<?php
require_once("ValidationInterface.php");
require_once("SuperType.php");

class TypeText extends SuperType implements ValidationInterface
{

  private $type = "string";

  private $nullable = false;

  private $empty = false;

  public function setType($type)
  {
    $this->type = $type;
  }

  public function setNullable($nullable)
  {
    $this->nullable = $nullable;
  }

  public function setEmpty($empty)
  {
    $this->empty = $empty;
  }

  public function validateType()
  {
    if ($this->nullable && $this->value == null) {
      return true;
    }
    if ($this->empty && $this->value == "") {
      return true;
    }
    $f = true;
    switch ($this->type) {
      case "email":
        $f = filter_var($this->value, FILTER_VALIDATE_EMAIL) == $this->value;
        break;
      case "ipv4":
        $f = filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) == $this->value;
        break;
      case "ipv6":
        $f = filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) == $this->value;
        break;
      case "mac":
        $f = filter_var($this->value, FILTER_VALIDATE_MAC) == $this->value;
        break;
      case "date":
        $d = DateTime::createFromFormat('Y-m-d', $this->value);
        if ($d == false) {
          $f = false;
          break;
        }
        $f = ($d->getLastErrors()['error_count'] == 0);
        break;
      case "time":
        $t = DateTime::createFromFormat('H:i:s', $this->value);
        if ($t == false) {
          $f = false;
          break;
        }
        $f = ($t->getLastErrors()['error_count'] == 0);
        break;
      case "datetime":
        $d = DateTime::createFromFormat('Y-m-d H:i:s', $this->value);
        if ($d == false) {
          $f = false;
          break;
        }
        $f = ($d->getLastErrors()['error_count'] == 0);
        break;
    }
    return $f;
  }
}
