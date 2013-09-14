<?php

namespace base;

class Db extends \PDO
{
	static $connections = array();
	public static function getConnection($name){
		if (empty(self::$connections[$name])) {
			$options = array(
			  \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,	
				\PDO::ATTR_AUTOCOMMIT => 0
			);
			self::$connections[$name] = new Db('mysql:host=localhost;dbname=testDb', "root", "", $options);			
		}
		return self::$connections[$name];
	}
}