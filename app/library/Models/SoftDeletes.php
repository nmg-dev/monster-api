<?php
namespace Phalcon\Models;

trait SoftDeletes {

	protected static function softDeleteColumn() { return 'deleted_at'; }

	public function delete() {
		$sd = self::softDeleteColumn();
		$this->$sd = date('c');
		return $this->update();
	}

	/**
     * Allows to query a set of records that match the specified conditions.
     * Do not explicitly finds deleted
     * 
     * @param mixed $parameters
     * @return Model[]|Model|\Phalcon\Mvc\Model\ResultSetInterface
     */
	public static function find($parameters = null) {
		
		return parent::find($parameters);
	}

	/**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Model|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
	
}