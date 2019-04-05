<?php
class SessionHandle{

  private $conn;

  public function __construct(){
    $this->conn = new DatabaseSession();
  }
}
?>
