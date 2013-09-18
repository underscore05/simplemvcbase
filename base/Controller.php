<?php

namespace base;

class Controller{
	
	protected $_views = array();
	private $_res = null;
	private $_req = null;
	
	public $layout = "main";		
	
	protected function getRequest()
	{
		return Request::getInstance();
	}
	
	protected function getResponse()
	{
		if(empty($this->_res)){
			$this->_res = new Response();
		} 
		return $this->_res;
	}
	
	protected function getView($tplName, $isNew = false)
	{	
		if(empty($this->currentPath) || empty($this->className)) {
			$class_info = new \ReflectionClass($this);
			$this->currentPath = dirname($class_info->getFileName());
			$this->className = $class_info->getShortName();
		}	
		$tplName = APP_PATH.DS."views".DS.$this->className.DS."{$tplName}.php";
				
		if(!$isNew && $this->_views[$tplName] instanceOf View) {
			return $this->_views[$tplName];
		} else {			
			$this->_views[$tplName] = new View($tplName);
			return $this->_views[$tplName];
		}		
	}
	
	public function getIndex() {
		return "This getIndex function should be overriden by child class";
	}
}