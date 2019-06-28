<?php 
require_once(APP_PATH.'/migrations/ModelDefinition.php');

/**
 * Class CampaignsMigration_100
 */
class CampaignsMigration_100 extends ModelDefinition
{
	public function morph()
    {
        $this->morphTable('campaigns', [
        	'columns' => [
        		$this->_integer('id', ['autoIncrement'=>true, 'unsigned'=>true, 'notNull'=>true]),
        		$this->_char('service', ['size'=>12, 'notNull'=>true]),
                $this->_integer('account_id', ['unsigned'=>true, 'notNull'=>true]),
                $this->_varchar('uid', ['size'=>400, 'notNull'=>true]),
                $this->_text('profile'),
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
                $this->_index('service,uid', 'campaign_uniqueness', 'UNIQUE'),
                // $this->_index('period_from', 'campaign_period_from_index'),
                // $this->_index('period_till', 'campaign_period_till_index'),
        	],
            
        ]);
    }
}
