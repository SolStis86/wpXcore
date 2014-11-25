<?php

class WPX_DB extends WPX_Model {

	public $DB, $model;

	protected $table, $queryStore = false;

	public function __construct() {

		global $wpdb;
		
		$this->DB = $wpdb;
	
	}

	public function createTable($tableName, Closure $schema) {
		
		//$table = function() use ($schema) {
			$table = $schema(new WPX_DB_SCHEMA);
		//};
		$this->DB->query($table->buildSchema($tableName));
		//return $this->runQuery($table->buildSchema());
	}

	public function tableExists($tableName) {
		return ($this->DB->get_var("SHOW TABLES LIKE '$tableName'") != $tableName) ? false : true;
	}

	public function setTable($table) {
		$this->table = $table;
	}

	public function all() {
		return $this->DB->get_results( parent::get() );
	}

	public function get() {
		if(!$this->queryStore) {
			return $this->DB->get_results( parent::get() );
		} else {
			return $this->DB->get_results( $this->queryStore->get() );
		}
	}

	public function update($id, $params, $primary_col = 'id') {
		return $this->DB->query(parent::update($id, $params, $primary_col));
	}

	public function delete($id, $primary_col = 'id') {
		return $this->DB->query(parent::delete($id, $primary_col));
	}

	public function create($params) {
		return $this->DB->query(parent::create($params));
	}

	public function where($colName, $operator, $constraint) {
		if(!$this->queryStore) {
			$this->queryStore = parent::where($colName, $operator, $constraint);	
		} else {
			$this->queryStore->where($colName, $operator, $constraint);
		}
		
	}
	public function sortBy($col, $operator) {
		if(!$this->queryStore) {
			$this->queryStore = parent::sortBy($col, $operator);
		} else {
			$this->queryStore->sortBy($col, $operator);
		}
		
	}

}


class WPX_DB_SCHEMA {

	public $schema = [], $key = false;

	public function buildSchema($tableName) {
		$SQL = 'CREATE TABLE ' . $tableName . '(';

		$SQL .= implode(', ', $this->schema);

		if($this->key) {
			$SQL .= ', ' . $this->key;
		}
		return $SQL . ')';
		
	}

	public function increments($colName, $key = true) {
		$this->schema[$colName] = $colName . ' INT NOT NULL AUTO_INCREMENT';
		$this->key = $key ? 'PRIMARY KEY ( ' . $colName . ' )' : false;
		return $this;
	}
	public function text($colName) {
		$this->schema[$colName] = $colName . ' TEXT NOT NULL';
		return $this;
	}
	public function varchar($colName, $length = 255) {
		$this->schema[$colName] = $colName . ' VARCHAR(' . $length . ') NOT NULL';
		return $this;
	}

	public function integer($colName) {
		$this->schema[$colName] = $colName . ' INT';
		return $this;
	}

	public function date($colName) {
		$this->schema[$colName] = $colName . ' DATE';
		return $this;
	}

	public function dateTime($colName) {
		$this->schema[$colName] = $colName . ' DATETIME';	
		return $this;
	}

	public function float($colName, $length = 10, $decimals = 2) {
		$this->schema[$colName] = $colName . ' FLOAT(' . $length . ', ' . $decimals . ') NOT NULL';	
		return $this;
	}
	public function double($colName, $length = 16, $decimals = 4) {
		$this->schema[$colName] = $colName . ' DOUBLE(' . $length . ', ' . $decimals . ') NOT NULL';	
		return $this;
	}

	public function boolean($colName) {
		$this->schema[$colName] = $colName . ' TINYINT(1) NOT NULL';	
		return $this;
	}

	public function null() {
		end($this->schema);
		$col = key($this->schema);
		$last = array_pop($this->schema);
		$this->schema[$col] = str_replace('NOT NULL', 'NULL', $last);
		return $this;
	}


}

abstract class WPX_Model extends WPX_Container {

	protected $table;

	private $query = [], $params, $updateSQL = [], $sort = null;

	public function __construct() {
		$this->query['constraints'] = [];
		$this->query['selects'] = [];
	}

	public function where($colName, $operator, $constraint) {
			$this->query['constraints'][] = sprintf("$colName$operator'%s'", @mysql_real_escape_string($constraint));
			return $this;
	}



	public function get() {
		
		$SQL = "SELECT ";
		
		if(func_num_args() > 0) {
			foreach(func_get_args() as $col) {
				$this->query['selects'][] = $col;
			}
			$SQL .= implode(', ', $this->query['selects']);

		} else {
			$SQL .= "*";
		}
		return $SQL . ' FROM ' . $this->table . $this->processConstraints() . $this->sort;
	}

	public function delete($id, $primary_col = 'id') {
		
		return 'DELETE FROM ' . $this->table . $this->where($primary_col, '=', $id)->processConstraints();
	}

	public function create($params) {

		array_walk($params, [$this, 'processParamsCreate']);
		
		$cols = '(' . implode(", ", $this->params['cols']) . ')';
		$vals = 'VALUES (' . implode(", ", $this->params['vals']) . ')';
		
		return "INSERT INTO $this->table " . $cols . " " . $vals;

	}

	public function update($id, $params, $primary_col = 'id') {
		
		$SQL = "UPDATE $this->table SET "; 

		array_walk($params, [$this, 'processParamsUpdate']);

		return $SQL .= implode(',', $this->updateSQL) . $this->where($primary_col, '=', $id)->processConstraints();

	}

	public function count() {
		return "SELECT COUNT(*) FROM $this->table" . $this->processConstraints();
	}

	public function countWhere($colName, $operator, $constraint) {
		return $this->where($colName, $operator, $constraint)->count();
	}

	public function sortBy($col, $operator) {
		$this->sort = " ORDER BY $col $operator";
		return $this;
	}

	private function processParamsCreate($val, $key) {
		$this->params['cols'][] = $key;
		$this->params['vals'][] = sprintf("'%s'", @mysql_real_escape_string($val));
	}

	private function processParamsUpdate($val, $key) {
		$this->updateSQL[] = $key . "=" . sprintf("'%s'", @mysql_real_escape_string($val));
	}

	private function processConstraints() {
		
		if(sizeof($this->query['constraints']) > 0) {

			$constraint = array_pop($this->query['constraints']);
			$SQL = " WHERE $constraint";
			$SQL .= implode(" AND ", $this->query['constraints']);
		
			return $SQL; 
		}
		return;

	}






}

