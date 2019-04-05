<?php
class SessionHandle{

  private $conn;

  public function __construct(){
    $this->conn = new DatabaseSession();
  }

  /*
  The open callback works like a constructor in classes and is executed when the session is being opened. It is the first callback function executed when the session is started automatically or manually with session_start(). Return value is TRUE for success, FALSE for failure.
  */
  public function open($savePath, $sessionName){

  }

}
?>
