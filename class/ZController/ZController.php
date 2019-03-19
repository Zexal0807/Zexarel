<?php
abstract class ZController{

	public function __construct(){
	}
	
	public static function __callStatic($name, $arg){
		$a = new static();
		return (method_exists(get_called_class(), $name) ? $a->$name($arg) : null);
	}

}
?>
