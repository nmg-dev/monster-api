<?php 
require_once(APP_PATH.'/migrations/ModelDefinition.php');

/**
 * Class AdrecordsMigration_101
 */
class AdrecordsMigration_101 extends ModelDefinition
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('adrecords', [
            'references' => [
                $this->_refer('fk_adrecord_item', 'item_id', 'id', 'aditems')
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
