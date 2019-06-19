<?php 
require_once(APP_PATH.'/migrations/ModelDefinition.php');

/**
 * Class PermissionsMigration_101
 */
class PermissionsMigration_101 extends ModelDefinition
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('permissions', [
            'references' => [
                $this->_refer('fk_permission_access', 'access_id', 'id', 'access'),
                $this->_refer('fk_permission_account', 'account_id', 'id', 'accounts')
            ],
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
