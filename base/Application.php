<?php

namespace base;

class Application 
{		
	public function process(Request $req)
	{
		$httpMethod = $req->getHttpMethod();
		$controllerId = $req->getControllerId('user');
		$methodId = $req->getMethodId();				
		$controllerNs = "\\app\\controllers\\{$controllerId}";				
		
		$controller = new $controllerNs();		
		$method = $httpMethod.$methodId;
		
		if(!method_exists($controller, $method)) {
			//static  function Error404(){
				//TODO Implement HTTP Error message
				header('HTTP/1.1 404');		
				header('Cache-Control: no-cache, must-revalidate');				
				throw new \BadMethodCallException("Page not found");
				die();			
		} else {
			$res = $controller->{$method}();	
		}
		return $res;
	}
	
	public static function autoload($className) {	
		$className = ltrim($className, '\\');
	  $fileName  = '';
	  $namespace = '';
	  if ($lastNsPos = strripos($className, '\\')) {
	      $namespace = substr($className, 0, $lastNsPos);
	      $className = substr($className, $lastNsPos + 1);
	      $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	  }
	  $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		if (is_readable($fileName)) {
			require_once $fileName;
		}  
	}
}

spl_autoload_register(array('base\Application', 'autoload'), true, true);

