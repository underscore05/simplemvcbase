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
}
