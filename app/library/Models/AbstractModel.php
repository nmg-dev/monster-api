<?php 
namespace Phalcon\Models;

abstract class AbstractModel extends \Phalcon\Mvc\Model {
	
	protected function jsonite($column) {
		if($this->$column) {
			$this->$column = json_encode($this->$column, JSON_FORCE_OBJECT |  JSON_INVALID_UTF8_SUBSTITUTE);
		}
	}

	protected function jsonparse($column) {
		if($this->$column) {
			$this->$column = json_decode($this->$column, true);
		}
	}

	protected function timestamps($times=['created_at','updated_at']) {
		foreach($times as $ts) {
			if(property_exists($this, $ts) && $this->$ts) {
				$this->$ts = strtotime($this->$ts);
			} else {
				$this->$ts = null;
			}
		}
	}

	protected function timestrings($times) {
		foreach($times as $ts) {
			if(property_exists($this, $ts) && $this->$ts) {
				$this->$ts = date(self::TIMESTAMP_FORMAT, intval($this->$ts));
			} else {
				$this->$ts = null;
			}
		}
	}

	const TIMESTAMP_FORMAT = 'Y-m-d H:i:s.B';

	protected function created_timestamp($column='created_at') {
		// die($column.' '.gettype($this->$column).' '.$this->$column);
		if(!$this->$column)
			$this->$column = date(self::TIMESTAMP_FORMAT);
		else
			$this->$column = date(self::TIMESTAMP_FORMAT, intval($this->$column));
	}

	protected function updated_timestamp($column='updated_at') {
		$this->$column = date(self::TIMESTAMP_FORMAT);
	}

	protected function _listValues($property_name, $key='uid') {
		$rets = [];
		// echo $this->$property_name."\n";
		foreach($this->$property_name as $k=>$v) {
			$rets[$v->$key] = $v;
		}
		return $rets;
	}
	
}

