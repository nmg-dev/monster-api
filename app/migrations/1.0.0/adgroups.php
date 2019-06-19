<?php 
require_once(APP_PATH.'/migrations/ModelDefinition.php');

/**
 * Class AdgroupsMigration_100
 */
class AdgroupsMigration_100 extends ModelDefinition
{
	public function morph()
    {
        $this->morphTable('adgroups', [
        	'columns' => [
        		$this->_bigint('id', ['autoIncrement'=>true, 'unsigned'=>true, 'notNull'=>true]),
        		$this->_char('service', ['size'=>12, 'notNull'=>true]),
                $this->_integer('account_id', ['unsigned'=>true, 'notNull'=>true]),
                $this->_integer('campaign_id', ['unsigned'=>true, 'notNull'=>true]),
                $this->_varchar('uid', ['size'=>400, 'notNull'=>true]),
                $this->_text('profile'),
                $this->_date('period_from'),
                $this->_date('period_till'),
                $this->_text('errors'),
                $this->_integer('status', ['size'=>1, 'default'=>0]),
                $this->_timestamp('visited_at'),
				$this->_timestamp('created_at'),
				$this->_timestamp('updated_at'),
				$this->_timestamp('deleted_at'),
        	],
        	'indexes' => [
        		$this->_index('id'),
                $this->_index('service,uid', 'adgroup_uniqueness', 'UNIQUE'),
                $this->_index('period_from', 'adgroup_period_from_index'),
                $this->_index('period_till', 'adgroup_period_till_index'),
        	],

        ]);
    }
}