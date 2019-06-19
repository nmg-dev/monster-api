<?php 
require_once(APP_PATH.'/migrations/ModelDefinition.php');

/**
 * Class CampaignsMigration_101
 */
class CampaignsMigration_101 extends ModelDefinition
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('campaigns', [
            'references' => [
                $this->_refer('fk_campaign_account', 'account_id', 'id', 'accounts'),
            ]
        ]);
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {

    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {

    }

}
