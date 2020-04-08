<?php
require_once __DIR__ . "/../ValidatorSchema/ValidatorSchema.php";

abstract class ZModel implements ConnectionInterface{

  protected $table;

  protected $schema = [];
  protected $primaryKey = [];

  protected $availableMode = ["c", "u", "d"];

  private $db;

  private $insertSchema;
  private $updateSchema;
  private $deleteSchema;

  public function __construct(){
    $this->db = $this->connect();

    //insert
    $this->insertSchema = $this->schema;
    for($i = 0; $i < sizeof($this->insertSchema); $i++){
      $this->insertSchema[$i]['required'] = isset($this->insertSchema[$i]['nullable']) && $this->insertSchema[$i]['nullable'] ? false : true;
    }
    if($this->primaryKey['autoincrement'] != true){
      $t = $this->primaryKey;
      $t['required'] = true;
      $this->insertSchema[] = $t;
    }

    //update
    $this->updateSchema = $this->schema;
    for($i = 0; $i < sizeof($this->updateSchema); $i++){
      $this->updateSchema[$i]['required'] = isset($this->updateSchema[$i]['nullable']) && $this->updateSchema[$i]['nullable'] ? false : true;
    }
    $t = $this->primaryKey;
    $t['required'] = true;
    $this->updateSchema[] = $t;

    //delete
    $this->deleteSchema = [ $this->primaryKey ];
    $this->deleteSchema[0]['required'] = true;
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

  public function insert($data){
    $v = new ValidatorSchema();
    if($v->validate($this->insertSchema, $data)){

      $sql = "INSERT INTO ".$this->table."(";
      $p = [];
      for($i = 0; $i < sizeof($this->insertSchema); $i++){
        $sql .= $this->insertSchema[$i]['name']. ", ";
        $p[] = $data[$this->insertSchema[$i]['name']];
      }
      $sql = substr($sql, 0, -2).") VALUES(?)";

      $sql = build_query($sql, [$p]);

      $this->db->query($sql);

      $ret = [
        "fieldCount" => $this->db->field_count,
        "affectedRows" => $this->db->affected_rows,
        "insertId" => $this->db->insert_id,
        "warningCount" => $this->db->warning_count
      ];
      return $ret;
    }else{
      return false;
    }
  }

  private function genInsert(){
    $self = $this;
    ZRoute::post("zcrud/".$this->table."/insert", function($data) use ($self){
      $r = $self->insert($data);
      if($r != false){
        header('Content-Type: application/json');
        echo json_encode($r);
      }else{
        header("HTTP/1.0 402 Not Valid");
      }
    });
  }

  public function update($data){
    $v = new ValidatorSchema();
    if($v->validate($this->updateSchema, $data)){

      $sql = "UPDATE ".$this->table." SET ";
      $p = [];
      for($i = 0; $i < sizeof($this->updateSchema) - 1; $i++){
        $sql .= $this->updateSchema[$i]['name']." = ?, ";
        $p[] = $data[$this->updateSchema[$i]['name']];
      }
      $sql = substr($sql, 0, -2);
      $sql .= " WHERE ".$this->primaryKey['name']." = ?";
      $p[] = $data[$this->primaryKey['name']];

      $sql = build_query($sql, $p);

      $this->db->query($sql);

      $ret = [
        "fieldCount" => $this->db->field_count,
        "affectedRows" => $this->db->affected_rows,
        "insertId" => $this->db->insert_id,
        "warningCount" => $this->db->warning_count
      ];

      return $ret;
    }else{
      return false;
    }
  }

  private function genUpdate(){
    $self = $this;
    ZRoute::post("zcrud/".$this->table."/update", function($data) use ($self){
      $r = $self->update($data);
      if($r != false){
        header('Content-Type: application/json');
        echo json_encode($r);
      }else{
        header("HTTP/1.0 402 Not Valid");
      }
    });
  }

  public function delete($data){
    $v = new ValidatorSchema();
    if($v->validate($this->updateSchema, $data)){

      $sql = build_query(
        "DELETE FROM ".$this->table." WHERE ".$this->primaryKey['name']." = ?",
        [ $data[$this->primaryKey['name']] ]
      );

      $this->db->query($sql);

      $ret = [
        "fieldCount" => $this->db->field_count,
        "affectedRows" => $this->db->affected_rows,
        "insertId" => $this->db->insert_id,
        "warningCount" => $this->db->warning_count
      ];

      return $ret;
    }else{
      return false;
    }
  }

  private function genDelete(){
    $self = $this;
    ZRoute::post("zcrud/".$this->table."/delete", function($data) use ($self){
      $r = $self->delete($data);
      if($r != false){
        header('Content-Type: application/json');
        echo json_encode($r);
      }else{
        header("HTTP/1.0 402 Not Valid");
      }
    });
  }

}

interface ConnectionInterface {
  public function connect();
}
