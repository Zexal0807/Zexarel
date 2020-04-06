<?php
class ZRoute{

	private static $_route = [];

	private static $_middleware = [];

	public static function __callStatic($name, $arg){
		if(in_array(strtoupper($name), ["GET", "POST", "PUT", "DELETE", "HEAD"])){
			ZRoute::$_route[] = new Route(strtoupper($name), $arg[0], $arg[1], (isset($arg[2]) ? $arg[2] : null));
		}
	}

	public static function addMiddleware($fx){
		ZRoute::$_middleware[] = $fx;
	}

	public static function getUri($name){
		/*
		Metodo getUri
		ha un argomento
		cerca tra i nomi registrati url della route corrispondente
		*/
		foreach(ZRoute::$_route as $r){
			if($r->getName() == $name){
				return $r->getUrl();
			}
		}
		return "";
	}

	public static function listen(){
		/*
		Metodo listen
		non ha argomenti
		legge le richieste e a seconda del REQUEST_METHOD, controlla se tale route esiste e chiama la callable corrispondente altrimenti ritorna HTTP 1.1 404 Route Not Found
		*/
		$req = new Request();
		$r = explode("/", $req ->getUrl());
		/*
		if(isset($r[0]) && class_exists($r[0]) && get_parent_class($r[0]) == "ZController"){
			if(isset($r[1]) && method_exists($r[0], $r[1])){
				$arg = $r;
				array_shift($arg);
				array_shift($arg);
				$method = $r[1];
				(new $r[0])->$r[1]($arg);
				exit;
			}elseif(isset($r[1]) && method_exists($r[0], "index")){
				$arg = $r;
				array_shift($arg);
				$method = "index";
				(new $r[0])->index($arg);
				exit;
			}
		}
		*/
		for($i = 0; $i < sizeof(ZRoute::$_route); $i++){
			if(ZRoute::$_route[$i]->compare($req)){
				ZRoute::runMiddleware(ZRoute::$_route[$i]);
				exit();
			}
		}
		http_response_code(404);
		include(__DIR__ . "/../../error/404.html");
		exit();
	}

	private static function runMiddleware(Route $route){
		$ret = true;
		for($i = 0; $i < sizeof(ZRoute::$_middleware) && $ret; $i++){
			$ret = call_user_func_array(ZRoute::$_middleware[$i], [ $route->getData() ]);
		}
		if($ret){
			$route->run();
		}
	}
}
?>
