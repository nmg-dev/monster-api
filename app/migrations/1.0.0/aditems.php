<?php 
require_once(APP_PATH.'/migrations/ModelDefinition.php');

/**
 * Class AditemsMigration_100
 */
class AditemsMigration_100 extends ModelDefinition
{
	public function morph()
    {
        $this->morphTable('aditems', [
        	'columns' => [
                $this->_bigint('id', ['autoIncrement'=>true, 'unsigned'=>true, 'notNull'=>true]),
                $this->_char('service', ['size'=>12, 'notNull'=>true]),
                $this->_integer('account_id', ['unsigned'=>true, 'notNull'=>true]),
                $this->_integer('campaign_id', ['unsigned'=>true, 'notNull'=>true]),
                $this->_bigint('group_id', ['unsigned'=>true, 'notNull'=>true]),
                $this->_varchar('uid', ['size'=>400, 'notNull'=>true]),
                $this->_text('profile'),
                
                $this->_bigint('impressions'),
                $this->_bigint('clicks'),
                $this->_bigint('conversions'),
                $this->_double('spendings'),
                $this->_text('stats'),

                // $this->_date('period_from'),
                // $this->_date('period_till'),
                $this->_text('errors'),
                $this->_integer('status', ['size'=>1, 'default'=>0]),

                $this->_timestamp('visited_at'),
                $this->_timestamp('created_at'),
                $this->_timestamp('updated_at'),
                $this->_timestamp('deleted_at'),
        	],
        	'indexes' => [
                $this->_index('id'),
                $this->_index('service,uid', 'aditem_uniqueness', 'UNIQUE'),
                // $this->_index('period_from', 'aditem_period_from_index'),
                // $this->_index('period_till', 'aditem_period_till_index'),
        	],
        ]);
    }
}