<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;


abstract class ModelDefinition extends Migration {

	/* hide abstraction */
	public function up() { }
	public function down() { }

	protected function _column(String $name, int $type, Array $options) {
		return new Column($name, array_merge($options, [ 'type' => $type]));
	}

	/* column interfaces */
	protected function _boolean(String $name, Array $options=[]) {
		return $this->_column($name, Column::TYPE_BOOLEAN, $options);
	}
	protected function _integer(String $name, Array $options=[]) {
		return $this->_column($name, Column::TYPE_INTEGER, $options);
	}
	protected function _bigint(String $name, Array $options=[]) {
		return $this->_column($name, Column::TYPE_BIGINTEGER, $options);
	}
	protected function _float(String $name, Array $options=[]) {
		return $this->_column($name, Column::TYPE_FLOAT, $options);
	}
	protected function _double(String $name, Array $options=[]) {
		return $this->_column($name, Column::TYPE_DOUBLE, $options);
	}
	protected function _char(String $name, Array $options=[]) {
		return $this->_column($name, Column::TYPE_CHAR, $options);
	}
	protected function _varchar(String $name, Array $options=[]) {
		return $this->_column($name, Column::TYPE_VARCHAR, $options);
	}
	protected function _text(String $name, Array $options=[]) {
		return $this->_column($name, Column::TYPE_TEXT, $options);
	}
	protected function _json(String $name, Array $options=[]) {
		return $this->_column($name, Column::TYPE_JSON, $options);
	}
	protected function _timestamp(String $name, Array $options=[]) {
		return $this->_column($name, Column::TYPE_TIMESTAMP, $options);
	}
	protected function _date(String $name, Array $options=[]) {
		return $this->_column($name, Column::TYPE_DATE, $options);
	}

	protected function __strmap(String $values, $delimiter=',') : Array {
		if($values && 0<strlen($values)) {
			return array_map(function($el) { return trim($el); },
				array_filter(
					explode($delimiter, $values), 
					function($el) { return $el && 0<=strlen(trim($el)); }
				)
			);
		} else {
			return [];
		}
	}

	protected function _index(String $column_names, String $name=null, $is_unique=false) {
		if(!$name)
			$name = 'PRIMARY';
		return new Index($name, 
			$this->__strmap($column_names), 
			$is_unique ? 'UNIQUE' : null);
	}

	protected function _refer(String $name, String $referee, String $referrer_column, String $referrer_table=null, String $referrer_schema=null) {
		$opts = [
			'columns' => $this->__strmap($referee),
			'referencedColumns' => $this->__strmap($referrer_column),
		];
		if($referrer_table)
			$opts['referencedTable'] = $referrer_table;
		if($referrer_schema)
			$opts['referencedSchema'] = $referrer_schema;

		return new Reference($name, $opts);
	}
}