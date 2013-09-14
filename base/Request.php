<?php
/**
 * Request
 *
 * @package base
 * @author Richard Neil Roque
 **/

namespace base;

class Request
{	
	protected static $instance = null;
	
	private $_options = array();	
	private $_vars = array();	
	private $_getVars = array();
	private $_postVars = array();
	
	public $method = 'get';
	public $controllerVar = 'c';
	public $methodVar = 'm';

	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new self();
	    }
	    return self::$instance;
	}
	
	private function __construct($options = array())
	{		
		$this->_readRawParams();
	}
			
	private function _readRawParams()
	{
		$this->_vars = $_REQUEST;
		$this->_postVars = $_POST;
		$this->_getVars = $_GET;
		$this->_method = strtolower($_SERVER['REQUEST_METHOD']);	
	}
	
	public function getHttpMethod()
	{
		return $this->_method;
	}
	
	public function getControllerId($def)
	{
		$id = null;
		if(!empty($this->_vars[$this->controllerVar])) {
			$id = ucfirst($this->_vars[$this->controllerVar]);
		} else {
			$id = ucfirst($def);
		}
		return $id;
	}
	
	public function getMethodId($def = 'index')
	{
		$id = null;		
		if(!empty($this->_vars[$this->methodVar])) {
			$id = ucfirst($this->_vars[$this->methodVar]);
		} elseif($def) {			
			$id = ucfirst($def);
		}
		return $id;
	}
	
	public function getInt($varId, $def = 0, $isPost = false)
	{
		$var = ($isPost==true) ? '_postVars' : '_getVars';
		$val = intVal($def);
		if(!empty($this->{$var}[$varId])){
			$val = intVal($this->{$var}[$varId]);			
		}
		return $val;
	}
	
	public function getArray($varId, $isPost = false)
	{
		$var = ($isPost==true) ? '_postVars' : '_getVars';
		$val = array();
		if(!empty($this->{$var}[$varId])){
			$val = (array)$this->{$var}[$varId];
		}
		return $val;		
	}
}