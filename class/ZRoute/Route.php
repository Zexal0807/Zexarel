<?php
class Route{
	private $method;
	private $url;
	private $pattern;
	private $callback;
	private $name;
	public function __construct($method, $url, $callback, $name = null){
		$this->method = $method;
		$this->url = $url;
		$p = explode("/", $this->url);
		$this->pattern = "";
		$f = false;
		for($i = 0; $i < sizeof($p); $i++){
			if(preg_match('/\<+[a-zA-Z0-9]*+\>/', $p[$i])){
				if($f){
					$this->pattern .= "+\/";
				}
				$f = true;
				$this->pattern .= '+[a-zA-Z0-9]*';
			}elseif(preg_match('/\\[+[a-zA-Z0-9]*+\]/', $p[$i])){
				$this->pattern .= "+(\/+".str_replace(["[", "]"], "", $p[$i]).')?';
			}else{
				if($f){
					$this->pattern .= "+\/";
				}
				$this->pattern .= $p[$i];
				$f = true;
			}
		}
		$this->callback = $callback;
		$this->name = $name;
	}
	public function getUrl(){
		return $this->url;
	}
	public function getName(){
		return $this->name;
	}
	public function getPattern(){
		return $this->pattern;
	}
	public function compareRequestAndRun(Request $req){
		if($req->getMethod() == $this->method){
			$routeUrl = preg_replace('/{{+[a-zA-Z0-9-]*+}}/', '[a-zA-Z0-9-]*', $this->url);
			if(preg_match("#^".$routeUrl."$#", $req->getUrl())){
				$arr = $req->getParameters();
				array_merge($arr, $req->getCookies(), $req->getFiles());
				if($this->url != $req->getUrl()){
					$f = explode("/", $this->url);
					$r = explode("/", $req->getUrl());
					for($i = 0; $i < sizeof($f); $i++){
						if(preg_match('/{{+[a-zA-Z0-9-]*+}}/', $f[$i])){
							$arr[str_replace(["{", "}", '"', "[", "]"], "", $f[$i])] = $r[$i];
						}
					}
				}
				$this->run($arr);
				if($req->getMethod() == "HEAD"){
					ob_end_clean();
				}
				return true;
			}
		}
		return false;
	}
	private function run($data){
		$arr[0] = $data;
		if(gettype($this->callback) == "string"){
			$cal = explode("@", $this->callback);
			if(sizeof($a) == 2){
				call_user_func_array($cal, $arr);
			}
		}elseif(is_callable($this->callback)){
			call_user_func_array($this->callback, $arr);
		}
	}
}
?>
