<?php
class Route{
	private $method;
	private $url;
	private $pattern;
	private $parameter;
	private $callback;
	private $name;
	public function __construct($method, $url, $callback, $name = null){
		$this->method = $method;
		$this->url = $url;
		$this->parameter = [];
		$this->pattern = RoutePatternCreator::create($this->url, $this->parameter);
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
				$arr = $req->getParameters();
				if(preg_match("#^".$this->pattern."$#", $req->getUrl())){
				$arr["_COOKIE"] = $req->getCookies();
				$arr["_FILES"] = $req->getFiles();
				if($this->url != $req->getUrl()){
					$f = explode("/", substr($this->url, 1));
					$r = explode("/", $req->getUrl());
					for($i = 0, $k = 0; $i < sizeof($r); $i++, $k++){
						if($r[$i] == $f[$k]){
						}else{
							if(preg_match('/\[+[a-zA-Z0-9]*+\]/', $f[$k])){
								$arr[str_replace(["[", "]"], "", $f[$k])] = ($r[$i] == $f[$k]);
								$i--;
							}elseif(preg_match('/\<+[a-zA-Z0-9]*+\>/', $f[$k])){
								$arr[str_replace(["<", ">"], "", $f[$k])] = $r[$i];
							}
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
