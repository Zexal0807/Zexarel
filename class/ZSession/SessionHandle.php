<?php
class SessionHandle{

  private $conn;

  public function __construct(){
    $this->conn = new DatabaseSession();
    $this->createTable();
  }

  private function createTable(){
    $this->conn->executeSql('CREATE TABLE IF NOT EXISTS '.ZConfig::config("SESSION_DB_TABLE", "session").'(
      id varchar(255) NOT NULL,
      data mediumtext NOT NULL,
      time int(255) NOT NULL,
      ip varchar(255) NOT NULL,
      PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8');
  }


  /*
  The open callback works like a constructor in classes and is executed when the session is being opened. It is the first callback function executed when the session is started automatically or manually with session_start(). Return value is TRUE for success, FALSE for failure.
  */
  public function open($savePath, $sessionName){
    $limit = time() - (3600 * 24 * 7);
    $this->conn
      ->delete()
      ->from(ZConfig::config("SESSION_DB_TABLE", "session"))
      ->where("time", "<", $limit)
      ->execute(null, function($sql, $result, $row){
        return $result;
      });
  }

  /*
  The close callback works like a destructor in classes and is executed after the session write callback has been called. It is also invoked when session_write_close() is called. Return value should be TRUE for success, FALSE for failure.
  */
  public function close(){
    return $this->conn->close();
  }
  }

}
?>
