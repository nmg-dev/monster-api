<?php 
require_once(APP_PATH.'/migrations/ModelDefinition.php');

/**
 * Class AdgroupsMigration_101
 */
class AdgroupsMigration_101 extends ModelDefinition
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('adgroups', [
            'references' => [
                $this->_refer('fk_adgroup_campaign', 'campaign_id', 'id', 'campaigns'),
                $this->_refer('fk_adgroup_account', 'account_id', 'id', 'accounts')
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
