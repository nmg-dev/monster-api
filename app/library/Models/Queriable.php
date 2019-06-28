<?php
namespace Phalcon\Models;

trait Queriable {

	public static function find($parameters = null) {
		return parent::find($parameters);
	}

	public static function findFirst($parameters = null) {
		return parent::findFirst($parameters);
	}

	public static function GetOne($keys, $values) {
		// find the one
		$entity = self::findFirst($keys);
		// if any, create it.
		if(!$entity) {
			$entity = new self();
			$entity->assign($keys);
		}

		// now put values on it
		$entity->assign($values);
		return $entity;
	}

}