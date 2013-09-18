<?php

namespace base;

class Application 
{		
	public function process(Request $req)
	{
		$httpMethod = $req->getHttpMethod();
		$controllerId = $req->getControllerId('users');
		$methodId = $req->getMethodId();				
		$controllerNs = "\\app\\controllers\\{$controllerId}";		
		$controller = new $controllerNs();
		$method = $httpMethod.$methodId;
		
		if(!method_exists($controller, $method)) {
			throw new \BadMethodCallException("public function {$method}() not found in class ".$controllerNs);				
		} else {
			$res = $controller->{$method}();
			if($res instanceOf Response) {
				if($controller->layout) {
					$layout = new View(APP_PATH.DS."layouts".DS.$controller->layout.".php");
					$layout->setVar('content', $res->getView());
					$res->setView($layout);
				}
			} else if ($res instanceOf View) {
				
			}
		}		
		return $res;
	}
}
