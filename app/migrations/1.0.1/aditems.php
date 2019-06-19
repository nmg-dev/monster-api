<?php 
require_once(APP_PATH.'/migrations/ModelDefinition.php');

/**
 * Class AditemsMigration_101
 */
class AditemsMigration_101 extends ModelDefinition
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('aditems', [
            'references' => [
                $this->_refer('fk_aditem_adgroup', 'group_id', 'id', 'adgroups'),
                $this->_refer('fk_aditem_campaign', 'campaign_id', 'id', 'campaigns'),
                $this->_refer('fk_aditem_account', 'account_id', 'id', 'accounts'),
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
