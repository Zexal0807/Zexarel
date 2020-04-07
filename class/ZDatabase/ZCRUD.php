<?php
require_once __DIR__ . "/../ValidatorSchema/ValidatorSchema.php";

abstract class ZCRUD implements ConnectionInterface{

  protected $table;

  protected $schema = [];
  protected $primaryKey = [];

  protected $availableMode = ["c", "u", "d"];

  private $db;

  private $insertSchema;
  private $updateSchema;
  private $deleteSchema;

  public function __construct($generate = null){
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

    if($generate == null){
      $this->generateZRoute();
    }
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
      if($v->validate($self->insertSchema, $data)){

        $sql = "INSERT INTO ".$self->table."(";
        $p = [];
        for($i = 0; $i < sizeof($self->insertSchema); $i++){
          $sql .= $self->insertSchema[$i]['name']. ", ";
          $p[] = $data[$self->insertSchema[$i]['name']];
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
      if($v->validate($self->updateSchema, $data)){

        $sql = "UPDATE ".$self->table." SET ";
        $p = [];
        for($i = 0; $i < sizeof($self->updateSchema) - 1; $i++){  
          $sql .= $self->updateSchema[$i]['name']." = ?, ";
          $p[] = $data[$self->updateSchema[$i]['name']];
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
      if($v->validate($self->updateSchema, $data)){

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
