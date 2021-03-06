<?php

namespace base;

class View 
{
		
	private $_vars = array();
	
	public function __construct($tpl, Controller $context=null)
	{		
		if(!file_exists($tpl)) {
			throw new \Exception("Cannot find file {$tpl}");
		}
		$this->_tpl = $tpl;
	}
	
	public function getVar($varId)
	{
		return $this->_vars[$varId];
	}

	public function setVar($varId, $val)
	{
		$this->_vars[$varId] = $val;
	}
	
	public function setVars($vars){		
		if(!is_array($vars)){    	
			throw new \InvalidArgumentException();    	
		}
		$this->_vars = array_merge($this->_vars, $vars);
	}

	public function fetch()
	{
		foreach($this->_vars as $key => $var) {
			if($var instanceOf View) {
				$this->_vars[$key] = $var->fetch();
			}
		}		
		extract($this->_vars);        
		ob_start();
		require $this->_tpl;
		$str = ob_get_clean();	
		return $str;
	}
	
	public function __toString() 
	{
		return $this->fetch();
	}
}