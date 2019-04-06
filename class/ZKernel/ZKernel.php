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
