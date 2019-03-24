<?php
class ZView{

	protected static $viewDir = "view";
	protected static $appFile = "app.html";

	private $html;
	private $dir;

	public function __construct($name, $base = null, $data = null){
		if(!isset($base)){
			$base = "";
		}
		if(!isset($data)){
			$data = [];
		}
		$this->dir = dirname((new ReflectionClass(get_called_class()))->getFilename());
		$this->html = file_get_contents($this->dir . DIRECTORY_SEPARATOR .static::$appFile);
		preg_match_all('/\@include\([\'"]{1}([a-zA-Z\\\'"_]*)[\'"]{1}\)/', $this->html, $match);
		$this->loadContent($name, $match);
		if(find('@base', $this->html) >= 0){
			$this->html = str_replace('@base', ($base == "" ? "" : '<base href="'.$base.'" />'), $this->html);
		}
		while(preg_match('/\@yield\([\'"]{1}([a-zA-Z\\\'"_]*)[\'"]{1}\)/', $this->html, $match)){
			$this->html = str_replace($match[0], $this->loadYield($match[1]), $this->html);
		}
		$this->html = ZBladeCompiler::compile($this->html, $data);
	}

	private function loadContent($name, $match){
		$tmp = file_get_contents($this->dir . DIRECTORY_SEPARATOR . static::$viewDir . DIRECTORY_SEPARATOR .$name.(find(".", $name) < 0 ?".html" : ""));
		for($i = 0; $i < sizeof($match[1]); $i++){
			$t = strlen($this->html);
			$this->html = str_replace("@include('".$match[1][$i]."')", get_string_between($tmp, "@".$match[1][$i], "@end".$match[1][$i]), $this->html);
			if($t == strlen($this->html)){
				$this->html = str_replace('@include("'.$match[1][$i].'")', get_string_between($tmp, "@".$match[1][$i], "@end".$match[1][$i]), $this->html);
			}
		}
	}

	private function loadYield($name){
		return file_get_contents($this->dir . DIRECTORY_SEPARATOR . static::$viewDir . DIRECTORY_SEPARATOR .$name.(find(".", $name) < 0 ? ".html" : ""));
	}

	public function returnHtml(){
		return $this->html;
	}

	public static function get($content, $base = null, $data = null){
		return (new static($content, $base, $data))->returnHtml();
	}

}
?>
