<?php

namespace base;

interface IModel {
	public function find($pk);		
	public function getTableName();
	public function save();
}

abstract class Model implements IModel {
	
	
	private $_db;	
	private $_tbl;
	private $_fields;
	private $_fieldMetas;
	private $_pk = 'id';		
	private $_isClean = false;
	
	public function __construct()
	{
		$this->_db = Db::getConnection("main");
		$this->_tbl = $this->getTableName();		
		$this->initFields();	
	}
	
	private function initFields()
	{
		if(empty($this->_fields))
		{
			$showColsStmt = $this->_db->query("SHOW COLUMNS FROM {$this->_tbl}");
			$showColsStmt->setFetchMode(\PDO::FETCH_ASSOC);
			static::$_stmts['showCols'] = $showColsStmt;
			
			foreach(static::$_stmts['showCols']->fetchAll() as $r){								
				$this->_fields[] = $r['Field'];
				$this->_fieldMetas[$r['Field']] = $r;
			}			
			
			foreach($this->_fields as $f) {
				if(empty($this->{$f}))
					$this->{$f} = "";
			}
		}		
		return $this->_fields;		
	}
	
	public function updateFields($data) {
		//This will remove invalid table fields from the data and pre-fill Object properties
		foreach($data as $field=>$value) {
			if(!in_array($field, $this->_fields)) {
				unset($data[$field]);
			} else {
				if($this->{$field}!=$value){
					$this->{$field} = $value;
					$this->_isClean = false;
				}				
			}
		}
	}
	
	public function find($key, $pk='id', $type="int") 
	{				
		if(!in_array($pk, $this->_fields)) {			
			echo "<pre>"; print_r (debug_print_backtrace()); echo "</pre>";
			die('Invalid Primary key');
		}
		$withStmt = !empty(static::$_stmts['find']);
		$isNewPk = 	($pk!=$this->_pk);
		if(!$withStmt || $isNewPk){
			$this->callCount++;		
			$stmt = $this->_db->prepare("SELECT * FROM {$this->_tbl} WHERE {$pk}=:val");
			$stmt->setFetchMode(\PDO::FETCH_CLASS, get_class($this));
			$stmt->bindParam(':val', $this->{$pk});	
			$stmt->execute();
			$this->_pk = $pk;
			static::$_stmts['find'] = $stmt;		
		} else {
			$stmt = static::$_stmts['find'];
		}
		$this->{$pk} = $key;
		$stmt->execute();				
		return $stmt->fetch();
	}		
	
	public function save()
	{				
		if(!$this->id){
			$withStmt = !empty(static::$_stmts['save_new']);
			//$stmt = null;
			if(!$withStmt) {
				$values = array();
				$fields = array();
				foreach($this->_fields as $f) {
					if($f!='id'){
						$values[] = ":{$f}";
						$fields[] = "{$f}";
					}					
				}
				$values = implode(",", $values);
				$fields = implode(",", $fields);
				
				$sql = "INSERT INTO {$this->_tbl} ({$fields}) VALUES({$values})";			
				$stmt = $this->_db->prepare($sql);			
				foreach($this->_fields as $f){
					if($f!='id'){
						$stmt->bindParam(":{$f}", $this->{$f});
					}
				}							
				static::$_stmts['save_new'] = $stmt;				
			} else {
				$stmt = static::$_stmts['save_new'];
			}			
			$stmt->execute();			
			$this->id = $this->_db->lastInsertId();
		} else {
			$withStmt = !empty(static::$_stmts['save_update']);
			if(!$withStmt) {							
				$fields_values = array();
				foreach($this->_fields as $f) {
					if($f!='id'){
						$fields_values[] = "{$f}=:{$f}";
					}
				}
				$fields_values = implode(",", $fields_values);
				$sql = "UPDATE {$this->_tbl} SET {$fields_values} WHERE id=:id";	
				//echo "<pre>"; print_r ($sql); die("</pre>");		
				$stmt = $this->_db->prepare($sql);
				foreach($this->_fields as $f){
					$stmt->bindParam(":{$f}", $this->{$f});
				}				
				$stmt->execute();
				static::$_stmts['save_update'] = $stmt;		
			} else {
				$stmt = static::$_stmts['save_new'];				
			}
			$stmt->execute();			
		}
						
		$this->_isClean = true;
	}
	
	public function validate() {
		return true;
	}		
}