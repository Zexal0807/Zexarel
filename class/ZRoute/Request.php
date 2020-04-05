<?php
class Request{

	private $url;
	private $method;
	private $parameter;

	private $cookie;
	private $files;
	private $ip;

	private $type = "this";

	public function __construct($url = null, $method = null, $parameter = null){
		if(isset($url)){
			$this->url = $url;
			$this->method = isset($method) ? $method :  "GET";
			$this->parameter = isset($parameter) ? $parameter :  [];
			$this->type = "new";
		}else{
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

	public function send($options = null){
		if($this->type != "new"){
			return "";
		}
		if(!isset($options)){
			$options = [
				'http' => [
					'header'  => [
						"Content-type: application/x-www-form-urlencoded",
					],
					'method'  => $this->method,
					'content' => http_build_query($this->parameter)
				]
			];
		}

		$context  = stream_context_create($options);
		return file_get_contents($this->url, false, $context);
	}
}
?>
