<?php
class ZDatabase extends mysqli{
  protected $user;			//string, username del database
  protected $password;	//string, password del database
	protected $host;			//string, host del database
  protected $database;	//string, nome del database

  private $sql = [];
  private $countSql = -1;

  private static $operator = [
    "=",
    ">",
    ">=",
    "<",
    "<=",
    "LIKE",
    "<>",
    "IN",
    "BETWEEN",
    "IS NULL",
    "IS NOT NULL"];

	public function __construct() {
    if(!isset($this->host)){
	    $this->host = ZConfig::config("DB_HOST", "");
    }
    if(!isset($this->user)){
	    $this->user = ZConfig::config("DB_USER", "");
		}
    if(!isset($this->password)){
	    $this->password = ZConfig::config("DB_PASSWORD", "");
		}
    if(!isset($this->database)){
	    $this->database = ZConfig::config("DB_DATABASE", "");
		}
		if(
			isset($this->host) &&
			isset($this->user) &&
			isset($this->password) &&
			isset($this->database) &&
			$this->host != "" &&
			$this->user != "" &&
			$this->database != ""
		){
			parent::__construct($this->host, $this->user, $this->password, $this->database);
		}else{
			//throw new Exception("Database connection setting not completed");
		}
  }

	public function select(){
		$field = func_get_args();
    $this->sql[]['select'] = [];
    $this->countSql++;
    foreach($field as $f){
      if(preg_match('/[a-zA-Z0-9\(\)]*(\s+(AS|as)+\s+[a-zA-Z0-9]*)?/', $f)){
        array_push($this->sql[$this->countSql]['select'], $f);
      }
    }
		return $this;
	}
	public function selectAll(){
		return $this->select("*");
	}
	public function selectDistinct(){
    $field = func_get_args();
    call_user_func_array("self::select", $field);
    //$this->select($field);
    $this->sql[$this->countSql]['distinct'] = true;
		return $this;
	}
	public function from(){
		$tables = func_get_args();
    if(isset($tables[0]) && $tables[0] instanceof ZDatabase){
      $a = $this->sql[$this->countSql];
      unset($this->sql[$this->countSql]);
      $this->countSql--;
    }else{
      $a = $tables;
    }
    $this->sql[$this->countSql]['from'] = $a;
    return $this;
  }
  public function where($field, $operator, $compare = null, $pre = null, $post = null){
    if(in_array($operator, ZDatabase::$operator)){
      if(isset($compare)){
        $compare = $this->haveErrorChar($compare);
      }
			if(gettype($compare) == 'string'){
				$compare = "'".$compare."'";
			}elseif(!isset($compare)){
        $compare = "NULL";
      }
      if(!isset($this->sql[$this->countSql]['where'])){
        $this->sql[$this->countSql]['where'] = [];
      }
			array_push($this->sql[$this->countSql]['where'], [$field, $operator, $compare, $pre, $post]);
		}
		return $this;
    }
  public function groupBy(){
  	$group_options = func_get_args();
    if(!isset($this->sql[$this->countSql]['groupBy'])){
      $this->sql[$this->countSql]['groupBy'] = [];
    }
    foreach($group_options as $o){
      array_push($this->sql[$this->countSql]['groupBy'], $o);
    }
		return $this;
  }
	public function having($field, $operator, $compare = null, $pre = null, $post = null){
    if(in_array($operator, ZDatabase::$operator)){
      if(isset($compare)){
        $compare = $this->haveErrorChar($compare);
      }
			if(gettype($compare) == 'string'){
				$compare = "'".$compare."'";
			}elseif(!isset($compare)){
        $compare = "NULL";
      }
      if(!isset($this->sql[$this->countSql]['having'])){
        $this->sql[$this->countSql]['having'] = [];
      }
			array_push($this->sql[$this->countSql]['having'], [$field, $operator, $compare, $pre, $post]);
		}
		return $this;
    }
	public function orderBy(){
    if(!isset($this->sql[$this->countSql]['orderBy'])){
      $this->sql[$this->countSql]['orderBy'] = [];
    }
    $order_options = func_get_args();
    foreach($order_options as $o){
      array_push($this->sql[$this->countSql]['orderBy'], $o);
    }
		return $this;
  }
	public function innerJoin($table, $on, $operator, $compare = null){
    if(in_array($operator, ZDatabase::$operator)){
      if(isset($compare)){
        $compare = $this->haveErrorChar($compare);
      }
      if(gettype($compare) == 'string'){
        $compare = "'".$compare."'";
      }elseif(!isset($compare)){
        $compare = "NULL";
      }
      if(!isset($this->sql[$this->countSql]['join'])){
        $this->sql[$this->countSql]['join'] = [];
      }
      array_push($this->sql[$this->countSql]['join'], ["INNER JOIN", $table, $on, $operator, $compare]);
    }
    return $this;
	}
	public function leftJoin($table, $on, $operator, $compare = null){
    if(in_array($operator, ZDatabase::$operator)){
      if(isset($compare)){
        $compare = $this->haveErrorChar($compare);
      }
      if(gettype($compare) == 'string'){
        $compare = "'".$compare."'";
      }elseif(!isset($compare)){
        $compare = "NULL";
      }
      if(!isset($this->sql[$this->countSql]['join'])){
        $this->sql[$this->countSql]['join'] = [];
      }
      array_push($this->sql[$this->countSql]['join'], ["LEFT JOIN", $table, $on, $operator, $compare]);
    }
    return $this;
	}
	public function rightJoin($table, $on, $operator, $compare = null){
    if(in_array($operator, ZDatabase::$operator)){
      if(isset($compare)){
        $compare = $this->haveErrorChar($compare);
      }
      if(gettype($compare) == 'string'){
        $compare = "'".$compare."'";
      }elseif(!isset($compare)){
        $compare = "NULL";
      }
      if(!isset($this->sql[$this->countSql]['join'])){
        $this->sql[$this->countSql]['join'] = [];
      }
      array_push($this->sql[$this->countSql]['join'], ["RIGHT JOIN", $table, $on, $operator, $compare]);
    }
    return $this;
	}

  public function build(){
    $sql = "";
    if(isset($this->sql[0]['select'])){
      $sql = $this->buildQuerySelect($this->sql[0]);
    }elseif(isset($this->sql[0]['insert'])){
      $sql = $this->buildQueryInsert($this->sql[0]);
    }elseif(isset($this->sql[0]['update'])){
      $sql = $this->buildQueryUpdate($this->sql[0]);
    }elseif(isset($this->sql[0]['delete'])){
      $sql = $this->buildQueryDelete($this->sql[0]);
    }
    unset($this->sql[0]);
    $this->countSql = 0;
    return $sql;
  }
  private function buildQuerySelect($data){
    $sql = $this->buildSelect($data);
    $sql .= $this->buildFrom($data);
    $sql .= $this->buildJoin($data);
    $sql .= $this->buildWhere($data);
    $sql .= $this->buildGroupBy($data);
    $sql .= $this->buildOrderBy($data);
    return $sql;
  }
  private function buildSelect($data){
    return "SELECT ".(isset($data['distinct']) ? "DISTINCT " : "").implode(", ", $data['select']);
  }
  private function buildFrom($data){
    $sql = " FROM ";
    if(isset($data['from']['select'])){
      $sql .= "(".$this->buildQuerySelect($data['from']).")";
    }else{
      $sql .= implode(", ", $data['from']);
    }
    return $sql;
  }
  private function buildJoin($data){
    $sql = "";
    if(isset($data['join'])){
      for($i = 0; $i < sizeof($data['join']); $i++){
        $sql .=  " ".$data['join'][$i][0]." ".$data['join'][$i][1]." ON ".$data['join'][$i][2]." ".$data['join'][$i][3]." ".$data['join'][$i][4];
      }
    }
    return $sql;
  }
  private function buildWhere($data){
    $sql = "";
    if(isset($data['where'])){
      for($i = 0; $i < sizeof($data['where']); $i++){
        if($i == 0){
          $sql .= " WHERE";
        }
        if(isset($data['where'][$i]['pre'])){
          $sql .= " ".$data['where'][$i]['pre']." ";
        }
        $sql .= " ".implode(" ", $data['where'][$i]);
        if(isset($data['where'][$i]['post'])){
          $sql .= " ".$data['where'][$i]['post']." ";
        }
      }
    }
    return $sql;
  }
  private function buildGroupBy($data){
    $sql = "";
    if(isset($data['groupBy'])){
      $sql .= " GROUP BY ".implode(", ", $data['groupBy']);
      //having
      if(isset($data['having'])){
        for($i = 0; $i < sizeof($data['having']); $i++){
          if($i == 0){
            $sql .= " HAVING";
          }
          if(isset($data['having'][$i]['pre'])){
            $sql .= " ".$data['having'][$i]['pre']." ";
          }
          $sql .= " ".implode(" ", $data['having'][$i]);
          if(isset($data['having'][$i]['post'])){
            $sql .= " ".$data['having'][$i]['post']." ";
          }
        }
      }
    }
    return $sql;
  }
  private function buildOrderBy($data){
    $sql = "";
    //orderBy
    if(isset($data['orderBy'])){
      $sql .= " ORDER BY ".implode(", ", $data['orderBy']);
    }
    return $sql;
  }

  public function insert(){
    $arg = func_get_args();
    $this->sql[]['insert'] = ['table' => [], 'field' => []];
    $this->countSql++;
		if(sizeof($arg) > 0){
			$this->sql[$this->countSql]['insert']['table'] = $arg[0];
		}
		if(sizeof($arg) > 0){
			for($i = 1; $i < sizeof($arg); $i++){
        array_push($this->sql[$this->countSql]['insert']['field'], $arg[$i]);
			}
		}
		return $this;
	}
  public function value(){
    $value = func_get_args();
    if(isset($value[0]) && $value[0] instanceof ZDatabase){
      $a = $this->sql[$this->countSql];
      unset($this->sql[$this->countSql]);
      $this->countSql--;
    }else{
      $a = [];
      foreach($value as $v){
        $b = $this->haveErrorChar($v);
  			if($b == false && $b != 0){
  				return $this;
  			}else{
  				if(gettype($b) == 'string'){
  					$b = "'".$b."'";
  				}elseif(!isset($b)){
            $b = "NULL";
          }
  			}
        $a[] = $b;
      }
    }
    $this->sql[$this->countSql]['value'] = $a;
    return $this;
	}

  private function buildQueryInsert($data){
    $sql = $this->buildInsert($data);
    $sql .= $this->buildValue($data);
    return $sql;
  }
  private function buildInsert($data){
    $sql = "INSERT INTO ".$data['insert']['table'];
    if(sizeof($data['insert']['field']) > 0){
      $sql .= " (".implode(", ", $data['insert']['field']).")";
    }
    return $sql;
  }
  private function buildValue($data){
    $sql = " VALUES(";
    if(isset($data['value']['select'])){
      $sql .= $this->buildSelect($data['value']);
    }else{
      $sql .= implode(", ", $data['value']);
    }
    $sql .= ")";
    return $sql;
  }

	public function update($table){
    $this->sql[]['update'] = $table;
    $this->countSql++;
		return $this;
	}
	public function set($field, $value){
		$f = $this->haveErrorChar($field);
    $v = $this->haveErrorChar($value);
		if(!($f == false && $f != 0 && $v == false && $v != 0)){
			if(gettype($v) == 'string'){
				$v = "'".$v."'";
			}elseif(!isset($v)){
        $v = "NULL";
      }
      if(!isset($this->sql[$this->countSql]['set'])){
        $this->sql[$this->countSql]['set'] = [];
      }
			array_push($this->sql[$this->countSql]['set'], [$f, $v]);
			return $this;
		}
	}

  private function buildQueryUpdate($data){
    $sql = $this->buildUpdate($data);
    $sql .= $this->buildSet($data);
    $sql .= $this->buildWhere($data);
    return $sql;
  }
  private function buildUpdate($data){
    return "UPDATE ".$data['update'];
  }
  private function buildSet($data){
    $sql = " SET ";
    foreach($data['set'] as $v){
      $sql .= implode(" = ", $v).", ";
    }
    return substr($sql, 0, -2);
  }

  public function delete(){
    $arg = func_get_args();
    $this->sql[]['delete'] = ['table' => []];
    $this->countSql++;
		if(sizeof($arg) > 0){
			$this->sql[$this->countSql]['delete']['table'] = $arg[0];
		}
		return $this;
  }

  private function buildQueryDelete($data){
    $sql = $this->buildDelete($data);
    $sql .= $this->buildWhere($data);
    return $sql;
  }
  private function buildDelete($data){
    return "DELETE FROM ".$data['delete']['table'];
  }

  public function execute($beforeExecute = null, $afterExecute = null){
    /*
		Metodo execute
		non ha argomenti
		richiama il metodo getSQL e executeSql passandogli la sql creata
		*/
		$sql = "";
		try{
			$sql = $this->build();
      d_var_dump($sql);
			return $this->executeSql($sql, $beforeExecute, $afterExecute);
		}catch(Exception $e){
		}
	}
	private function haveErrorChar($str){
		/*
		Metodo haveErrorChar
		ha un argomento
		ritorna false nel caso nell'argomento ci sia uno dei caratteri che possono "rompere" una sql, altrimenti trasforma tutti gli ' in \' e ritorna la nuova stringa
		*/
		if(gettype($str) == 'string'){
			return str_replace(
				["'", "--", ";", "/*", "*/", "<?", "?>"],
				["\'", "\-\-", "&pv", "&sc", "&ec", "&sp", "&ep"],
				$str);
		}else{
			return $str;
		}
	}
	protected function beforeExecute($sql){
		/*
		Metodo beforeExecute
		metodo che viene richimato prima dell'esecuzione della query, utile per dei log
		*/
	}
	protected function afterExecute($sql, $result, $rowAffected){
		/*
		Metodo afterExecute
		metodo che viene richimato dopo dell'esecuzione della query, utile per dei log
		*/
	}
	public function executeSql($sql, $beforeExecute = null, $afterExecute = null){
		/*
		Metodo executeSql
		ha un argomento
		richiama il metodo beforeExecute passandogli la sql ricevuta in input, esegue la sql e nel caso essa inizi con "SELECT" mette in un array tutti i record ottenuti come risultato e ritorna esso altrimenti ritorna un array vuoto
		*/
    if(isset($beforeExecute)){
      call_user_func_array($beforeExecute, [$sql]);
    }else{
      $this->beforeExecute($sql);
    }
    $result = $this->query($sql);
    if(isset($afterExecute)){
      call_user_func_array($afterExecute, [$sql, $result, $this->affected_rows]);
    }else{
      $this->afterExecute($sql, $result, $this->affected_rows);
    }
		$resultset = [];
		if(substr($sql, 0, 6) == "SELECT"){
      if($result->num_rows > 0){
  			$fields = $result->field_count;
  			while($row = $result->fetch_assoc()) {
  				$r = $row;
  				array_push($resultset, $r);
  			}
  			if(!empty($resultset)){
  				return $resultset;
  			}
      }
      return [];
		}elseif(substr($sql, 0, 6) == "INSERT"){
      if($result){
        return $this->insert_id;
      }else{
        return false;
      }
    }else{
			return $result;
		}
  }

  public function execFromArray($arr, $beforeExecute = null, $afterExecute = null){
    $this->sql[] = $arr;
    return $this->execute($beforeExecute, $afterExecute);
  }

}
?>
