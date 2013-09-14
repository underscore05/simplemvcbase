<?php

namespace base;

class Response {
	
	private $_view = null;
	private $_vars = array();
	
	public function getVar($varId)
	{
		return $this->_vars[$varId];
	}
	
	public function setVar($varId, $val)
	{
		$this->_vars[$varId] = $val;
	}
	
	public function getView()
	{
		return $this->_view;
	}
	
	public function setView(View $view)
	{
		$this->_view = $view;
	}
	
	public function __toString()
	{
		if($this->_view){
			return $this->_view->fetch();		
		} else {
			return "I came from Response Object. My Format is dependent on Response type";
		}
		
	}
}