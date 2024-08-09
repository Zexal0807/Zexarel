<?php
require_once __DIR__ . "/../ValidatorSchema/ValidatorSchema.php";

abstract class ZModel {

  const CREATION_MODE_FROM_DATABASE = 1;
  const CREATION_MODE_FROM_CODE = 2;

  protected static $tablename;

  protected static $tableid = "id";

  private static $DB;

  public static function setDB($DB){
    self::$DB = $DB;
  }

  protected $fields = [];
  private $data = [];

  private $mode = ZModel::CREATION_MODE_FROM_CODE;

  public function __construct($data = []) {
    foreach ($this->fields as $field) {
      $this->data[$field] = NULL;
    }

    foreach ($data as $key => $value) {
      $this->data[$key] = $value;
    }
  }

  public function __set($name, $value) {
      $this->data[$name] = $value;
  }

  public function __get($name) {
    if (array_key_exists($name, $this->data)) {
      return $this->data[$name];
    }
    return null;
  }

  public function save() {
    if ($this->mode == ZModel::CREATION_MODE_FROM_CODE) {
        $this->insert();
    } else {
      $this->update();
    }
    return $this;
  }

  protected function preInsert(){}
  protected function postInsert($status, $insert_id){}

  private function insert() {
    $this->preInsert($this);

    $fields = [];
    $values = [];
    $subquery = [];
    foreach($this->data as $k => $v){
      $fields[] = $k;
      $values[] = $v;
      $subquery[] = "?";
    }

    $sql = <<< SQLEND
    INSERT INTO %s (%s) 
    VALUES (%s)
    SQLEND;

    $sql = sprintf($sql, static::$tablename, implode(", ", $fields), implode(", ", $subquery));
    var_dump($sql);

		$query = self::$DB->prepare($sql);

		$f = str_repeat("s", sizeof($values));
    $query->bind_param($f, ...$values);
		$query->execute();

    $status = $query->affected_rows == 1;
    $insert_id = $query->insert_id;

    $this->postInsert($status, $insert_id);
    return $insert_id;
}

  protected function preUpdate(){}
  protected function postUpdate($status){}

  private function update() {
    $this->preUpdate();

    $subquery = [];
    $values = [];
    
    foreach($this->data as $k => $v){
      if($k != static::$tableid) {
        $s = '%s = ?';
        $subquery[] = sprintf($s, $k);
        $values[] = $v;
      }
    }
    $subquery = implode(', ', $subquery);

    $sql = <<< SQLEND
    UPDATE %s
    SET $subquery
    WHERE %s = ?
    SQLEND;

    $sql = sprintf($sql, static::$tablename, static::$tableid);
		$query = self::$DB->prepare($sql);

		$f = str_repeat("s", sizeof($values) + 1);
    $values[] =$this->id;
    $query->bind_param($f, ...$values);
		$query->execute();

    $status = $query->affected_rows == 1;
    
    $this->postUpdate($status);
    return $status;
  }

  public static function findById($id){
    $sql = <<< SQLEND
    SELECT *
    FROM %s
    WHERE %s = ?
    SQLEND;

    $sql = sprintf($sql, static::$tablename, static::$tableid);

		$query = self::$DB->prepare($sql);

		$query->bind_param("s", $id);
		$query->execute();
		$query = $query->get_result();
		$data = $query->fetch_assoc();
    return static::toModel($data);
  }

  public static function toModel($data){
    $model = new static($data);
    $model->mode = ZModel::CREATION_MODE_FROM_DATABASE;

		return $model;
  }

  public static function arrayToModel($dataArray){
    $d = [];
    foreach($dataArray as $data){
      $model = new static($data); 
      $model->mode = ZModel::CREATION_MODE_FROM_DATABASE;
      $d[] = $model;
    }
		return $d;
  }

}