<?php

namespace base;

class Controller{
	
	private $_views = array();
	private $_res = null;
	private $_req = null;
	public $currentPath = null;
	
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
		if(!$isNew && !empty($this->_views[$tplName])) {
			return $this->_views[$tplName];
		} else {			
			if(empty($this->currentPath) || empty($this->className)) {
				$class_info = new \ReflectionClass($this);
				$this->currentPath = dirname($class_info->getFileName());
				$this->className = $class_info->getShortName();
			}
			$ds = DIRECTORY_SEPARATOR;
			$tplName = $this->currentPath.$ds.$this->className.'.Views'.$ds."{$tplName}.php";
			$this->_views[$tplName] = new View($tplName);
			return $this->_views[$tplName];
		}		
	}
}