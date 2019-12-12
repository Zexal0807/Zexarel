<?php
class Request{
	private $ip;
	private $url;
	private $method;
	private $parameter;
	private $cookie;
	private $files;
	public function __construct(){
		$this->url = isset($_REQUEST['uri']) ? trim($_REQUEST['uri'], '/\^$') :  '';
		$this->method = $_SERVER['REQUEST_METHOD'];
		switch($this->method){
			case "PUT":
			case "HEAD":
			case "DELETE":
			case "STACK":
				parse_str(file_get_contents("php://input"), ${"_".$this->method});
				$this->parameter = ${"_".$this->method};
				break;
			case "GET":
				$this->parameter = $_GET;
				break;
			case "POST":
				if(array_key_exists('_method', $_POST) || array_key_exists('_METHOD', $_POST)){
					$this->method = strtoupper(isset($_POST['_method']) ? $_POST['_method'] : $_POST['_METHOD']);
					parse_str(file_get_contents("php://input"), ${"_".$this->method});
					$this->parameter = ${"_".$this->method};
				}else{
					$this->parameter = $_POST;
				}
				break;
			default:
		}
		$this->cookie = $_COOKIE;
		$this->files = $_FILES;
		$this->ip = (!empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'] ));
	}
	public function getUrl(){
		return $this->url;
	}
	public function getMethod(){
		return $this->method;
	}
	public function getParameters(){
		return $this->parameter;
	}
	public function getParameter($name){
		if(array_key_exists($name, $this->parameter)){
			return $this->parameter[$name];
		}
		return "";
	}
	public function getCookies(){
		return $this->cookie;
	}
	public function getCookie($name){
		if(array_key_exists($name, $this->cookie)){
			return $this->cookie[$name];
		}
		return "";
	}
	public function getFiles(){
		return $this->files;
	}
	public function getFile($name){
		if(array_key_exists($name, $this->files)){
			return $this->files[$name];
		}
		return "";
	}
	public function getIp(){
		return $this->ip;
	}
}
?>
