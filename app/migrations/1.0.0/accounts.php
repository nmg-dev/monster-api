<?php 
require_once(APP_PATH.'/migrations/ModelDefinition.php');

/**
 * Class AccountsMigration_100
 */
class AccountsMigration_100 extends ModelDefinition
{
	public function morph()
    {
        $this->morphTable('accounts', [
        	'columns' => [
        		$this->_integer('id', ['autoIncrement'=>true, 'unsigned'=>true, 'notNull'=>true]),
        		$this->_char('service', ['size'=>12, 'notNull'=>true]),
                $this->_varchar('uid', ['size'=>400, 'notNull'=>true]),
                $this->_text('profile'),
                $this->_text('errors'),
                $this->_integer('status', ['size'=>1, 'default'=>0]),
                $this->_timestamp('visited_at'),
                $this->_timestamp('created_at'),
                $this->_timestamp('updated_at'),
                $this->_timestamp('deleted_at'),
        	],
        	'indexes' => [
        		$this->_index('id'),
        		$this->_index('service,uid', 'account_uniqueness', true),
        	],
        ]);
    }
}
