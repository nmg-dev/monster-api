<?php 
require_once(APP_PATH.'/migrations/ModelDefinition.php');
/**
 * Class AdrecordsMigration_100
 */
class AdrecordsMigration_100 extends ModelDefinition
{
	public function morph()
    {
        $this->morphTable('adrecords', [
        	'columns' => [
                $this->_bigint('item_id', ['unsigned'=>true, 'notNull'=>true]),
                $this->_date('day_id', ['notNull'=>true]),

                $this->_bigint('impressions'),
                $this->_bigint('clicks'),
                $this->_bigint('conversions'),
                $this->_double('spendings'),
                $this->_text('stats'),
                $this->_text('errors'),

                $this->_timestamp('created_at'),
                $this->_timestamp('updated_at'),
        	],
        	'indexes' => [
                $this->_index('item_id,day_id'),
        	],
        	
        ]);
    }
}