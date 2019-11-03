<?php
class ZModel{

  protected $table;
  protected $databaseClass;

  private $field = [];
  private $mode;
  /*
    Mode:
      d: Database Mode, Model load from database
      u: User Mode, Model create from user
  */
  private $originalData = [];
  private $actualData = [];

  public function __construct($data = null){
    if(!isset($this->table)){
      $this->table = get_called_class();
    }

    $sql = "DESCRIBE ".$this->table;
    $DB = new $this->databaseClass;
    $ret = $DB->executeSql($sql);

    foreach($ret as $r){
      $this->field[$r["Field"]] = [
        "type" => $r["Type"],
        "null" => ($r["Null"] == "YES" ? true : false),
        "default" => $r['Default'],
        "key" => $r["Key"],
        "extra" => $r["Extra"]
      ];
    }

    if(isset($data)){
      $this->mode = "d";
      $this->originalData = $data;
    }else{
      $this->mode = "u";
      $data = [];
      foreach($this->field as $k => $v){
        $data[$k] = $v['default'];
      }
    }
    $this->actualData = $data;
  }

  public function __get($name){
		if(isset($this->actualData[$name])){
			return $this->actualData[$name];
		}else{
			return null;
		}
	}

	public function __set($name, $value){
		if(array_key_exists($name, $this->actualData) && $this->field[$name]["key"] == ""){
			$this->actualData[$name] = $value;
		}
	}

  public function save(){
    $DB = new $this->databaseClass;
    switch($this->mode){
      case "d":
        $arr = [
          "update" => $this->table,
          "set" => [
          ]
        ];
        foreach($this->filed as $k => $v){
          if($this->actualData[$k] != $this->originalData[$k] && $v['extra'] != "auto_increment"){
            $arr['set'][$k] = $this->actualData[$k];
          }
        }
        return $DB->execFromArray($arr);
        break;
      case "u":
        $arr = [
          "insert" => [
            "table" => $this->table,
            "field" => [
            ]
          ],
          "value" => [
          ]
        ];
        $ai;
        foreach($this->filed as $k => $v){
          if($v['extra'] != "auto_increment"){
            $arr['field'][] = $k;
            $arr['value'][] = $this->actualData[$k];
          }else{
            $ai = $k;
          }
        }
        $r = $DB->execFromArray($arr);
        if($r == false){
          return false;
        }else{
          $this->actualData[$ai] = $r;
          return true;
        }
        break;
    }
  }

  public static function find($where = null){
    $DB = new $this->databaseClass;
    $DB->selectAll()
      ->from($this->table);

    foreach($where as $v){
      $DB->where();
    }
    $d = $DB->execute()[0];

    $obj = new static($d);
    return $obj;
  }

}
