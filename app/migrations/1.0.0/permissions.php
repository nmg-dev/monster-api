<?php 
require_once(APP_PATH.'/migrations/ModelDefinition.php');

/**
 * Class PermissionMigration_100
 */
class PermissionsMigration_100 extends ModelDefinition
{
	public function morph()
    {
        $this->morphTable('permissions', [
        	'columns' => [
        		$this->_integer('id', ['autoIncrement'=>true, 'unsigned'=>true, 'notNull'=>true]),
                $this->_integer('access_id', ['unsigned'=>true, 'notNull'=>true]),
                $this->_integer('account_id', ['unsigned'=>true, 'notNull'=>true]),
                $this->_boolean('can_manage', ['default'=>0]),
                $this->_boolean('has_own', ['default'=>0]),
                $this->_timestamp('created_at'),
                $this->_timestamp('updated_at'),
        	],
        	'indexes' => [
        		$this->_index('id'),
                $this->_index('access_id,account_id', 'permission_uniqueness', 'UNIQUE'),
        	]
        ]);
    }
}
