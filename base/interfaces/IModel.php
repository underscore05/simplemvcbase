<?php

namespace base\interfaces;
/**
 * Interface for classes that wants to be persistable
 *
 * @package default
 * @author Richard Neil Roque
 **/


interface IModel
{
	public function getTableName();	
	public function getPrimaryKey();	
	public function getFields();
	
	public function getEvents($eventName);
	public function addEvent($eventName, $handlerName);
	
	public function getErrors();
} // END interface IModel

