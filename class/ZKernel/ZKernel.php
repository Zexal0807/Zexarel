<?php
class ZKernel{

	public static function enable(){
		error_reporting(0);
		register_shutdown_function([__CLASS__, 'shutdownHandler']);
		set_exception_handler([__CLASS__, 'exceptionHandler']);
		set_error_handler([__CLASS__, 'errorHandler']);
	}

	/*
	Shutdown handler to catch fatal errors and execute of the planned activities.
	*/
	public static function shutdownHandler(){
		$error = error_get_last();
		if(in_array($error['type'], [
			E_ERROR,
			E_CORE_ERROR,
			E_COMPILE_ERROR,
			E_PARSE,
			E_RECOVERABLE_ERROR,
			E_USER_ERROR
		], true)){
			$e = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
			if(function_exists('xdebug_get_function_stack')){
				$stack = [];
				foreach(array_slice(array_reverse(xdebug_get_function_stack()), 2, -1) as $row){
					$frame = [
						'file' => $row['file'],
						'line' => $row['line'],
						'function' => $row['function'] ? $row['function'] : '*unknown*',
						'args' => [],
					];
					if(!empty($row['class'])){
						$frame['type'] = isset($row['type']) && $row['type'] === 'dynamic' ? '->' : '::';
						$frame['class'] = $row['class'];
					}
					$stack[] = $frame;
				}
				$ref = new ReflectionProperty('Exception', 'trace');
				$ref->setAccessible(true);
				$ref->setValue($e, $stack);
			}
			static::exceptionHandler($e, false);
		}
	}
	/*
	Handler to catch uncaught exception.
	*/
	public static function exceptionHandler($exception, $exit = null){
	}
	/*
	Handler to catch warnings and notices.
	*/
	public static function errorHandler($severity, $message, $file, $line, $context){
	}
}
