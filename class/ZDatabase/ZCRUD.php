<?php
require_once __DIR__ . "/../ValidatorSchema/ValidatorSchema.php";

abstract class ZCRUD implements ConnectionInterface{

  protected $table;

  protected $schema = [];
  protected $primaryKey = [];

  protected $availableMode = ["c", "u", "d"];

  private $db;

  public function __construct(){
    $this->db = $this->connect();
  }

  public function generateZRoute(){
    if(in_array("c", $this->availableMode)){
      $this->genInsert();
    }
    if(in_array("u", $this->availableMode)){
      $this->genUpdate();
    }
    if(in_array("d", $this->availableMode)){
      $this->genDelete();
    }
  }

  private function genInsert(){
    $self = $this;
    ZRoute::post("zcrud/".$this->table."/insert", function($data) use ($self){

      $v = new ValidatorSchema();

      $s = $self->schema;
      if($self->primaryKey['autoincrement'] != true){
        $s[] = $self->primaryKey;
      }

      if($v->validate($s, $data)){

        $sql = "INSERT INTO ".$self->table."(";
        $p = [];
        for($i = 0; $i < sizeof($self->schema); $i++){
          if(isset($data[$self->schema[$i]['name']])){
            $sql .= $self->schema[$i]['name']. ", ";
            $p[] = $data[$self->schema[$i]['name']];
          }elseif(isset($self->schema[$i]['nullable'])){
            $sql .= $self->schema[$i]['name'].", ";
            $p[] = null;
          }
        }
        if($self->primaryKey['autoincrement'] != true){
          $sql .= ", ".$self->primaryKey['name'];
          $p[] = $data[$self->primaryKey['name']];
        }
        $sql = substr($sql, 0, -2).") VALUES(?)";

        $sql = build_query($sql, [$p]);

        $self->db->query($sql);

        $ret = [
          "fieldCount" => $self->db->field_count,
          "affectedRows" => $self->db->affected_rows,
          "insertId" => $self->db->insert_id,
          "warningCount" => $self->db->warning_count
        ];

        header('Content-Type: application/json');
        echo json_encode($ret);
      }else{
        header("HTTP/1.0 402 Not Valid");
      }
    });
  }

  private function genUpdate(){
    $self = $this;
    ZRoute::post("zcrud/".$this->table."/update", function($data) use ($self){

      $v = new ValidatorSchema();

      $s = [];
      $s[] = $self->primaryKey;
      $s[0]['required'] = true;
      for($i = 0; $i < sizeof($self->schema); $i++){
        $s[] = $self->schema[$i];
        $s[$i+1]['required'] = false;
      }

      if($v->validate($s, $data)){

        $sql = "UPDATE ".$self->table." SET ";
        $p = [];
        for($i = 0; $i < sizeof($self->schema); $i++){
          if(isset($data[$self->schema[$i]['name']])){
            $sql .= $self->schema[$i]['name']." = ?, ";
            $p[] = $data[$self->schema[$i]['name']];
          }
        }
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE ".$self->primaryKey['name']." = ?";
        $p[] = $data[$self->primaryKey['name']];

        $sql = build_query($sql, $p);

        $self->db->query($sql);

        $ret = [
          "fieldCount" => $self->db->field_count,
          "affectedRows" => $self->db->affected_rows,
          "insertId" => $self->db->insert_id,
          "warningCount" => $self->db->warning_count
        ];

        header('Content-Type: application/json');
        echo json_encode($ret);
      }else{
        header("HTTP/1.0 402 Not Valid");
      }
    });
  }

  private function genDelete(){
    $self = $this;
    ZRoute::post("zcrud/".$this->table."/delete", function($data) use ($self){

      $v = new ValidatorSchema();
      $s = [];
      $s[] = $self->primaryKey;
      for($i = 0; $i < sizeof($s); $i++){
        $s[$i]['required'] = true;
      }

      if($v->validate($s, $data)){

        $sql = build_query(
          "DELETE FROM ".$self->table." WHERE ".$self->primaryKey['name']." = ?",
          [ $data[$self->primaryKey['name']] ]
        );

        $self->db->query($sql);

        $ret = [
          "fieldCount" => $self->db->field_count,
          "affectedRows" => $self->db->affected_rows,
          "insertId" => $self->db->insert_id,
          "warningCount" => $self->db->warning_count
        ];

        header('Content-Type: application/json');
        echo json_encode($ret);
      }else{
        header("HTTP/1.0 402 Not Valid");
      }
    });
  }

}

interface ConnectionInterface {
  public function connect();
}
