<?php

namespace base;

use interfaces\IModel;

class ModelManager
{
	private $_db;
	private $_metas = array();
	
	public function __construct()
	{
		$this->_db = Db::getConnection("main");
	}
	
	public function beginTransaction()
	{
		$this->_db->beginTransaction();
	}
	
	public function commit()
	{
		$this->_db->commit();
	}
	
	private function getMeta(IModel $model)
	{
		$tbl = $model->getTableName();
		if(in_array($tbl, array_keys($this->_metas))){
			$meta = $this->_metas[$tbl];			
		} else {
			$db = Db::getConnection("main");
			$stmt = $this->_db->query("SHOW COLUMNS FROM {$tbl}");
			$stmt->setFetchMode(\PDO::FETCH_ASSOC);
			$cols = array();
			foreach($stmt->fetchAll() as $r) {
				$cols[$r['Field']] = $r;				
			}
			$meta = array();
			$meta['columns'] = $cols;
			$this->_metas[$tbl] = $meta;
		}
		
		$cols = $meta['columns'];
		foreach(array_keys($cols) as $c) {
			if(!property_exists($model, $c)) {
				$model->{$c} = ($cols[$c]['Null']=='NO')?'':null;				
			}			
		}		
		return $meta;
	}
	
	public function save(IModel $model)
	{	
		$meta = $this->getMeta($model);
		$tbl = $model->getTableName();
		$pk = $model->getPrimaryKey();
		
		if(!$model->{$pk}){
			$vals = array();
			$cols = array();			
			foreach(array_keys($meta['columns']) as $c) {
				if($c!=$pk) {
					$vals[] = ":".$c;
					$cols[] = $c;
				}					
			}
			$beforeInsertEvents = $model->getEvents('beforeInsert');
			if($beforeInsertEvents) {
				$withError = false;
				foreach($beforeInsertEvents as $handler) {					
					if(!$model->$handler()) {
						$withError = true;
					}
				}				
				if($withError){
					return array('status'=>'failed', 'errors'=>$model->getErrors());
				}
			}
			
			// TODO Add checking if model is clean before performing an update. Create a separate array for old values
			
			$values = array();
			foreach($cols as $c) {
				$values[":".$c] = $model->{$c};
			}					
			
			$_cols = implode(", ", $cols);
			$_vals = implode(", ", $vals);						
			$sql = "INSERT INTO {$tbl} ({$_cols}) VALUES({$_vals})";
			$stmt = $this->_db->prepare($sql);						
			$stmt->execute($values);			
			$model->id = $this->_db->lastInsertId();
			
			$afterInsertEvents = $model->getEvents('afterInsert');
			if($afterInsertEvents) {
				$withError = false;
				foreach($afterInsertEvents as $handler) {
					if(!$model->$handler()) {
						$withError = true;
					}
				}
				if($withError){
					return array('status'=>'failed', 'errors'=>$model->getErrors());
				}
			}
		} else {
				
		}
		
		$this->_isClean = true;
	}			
}

