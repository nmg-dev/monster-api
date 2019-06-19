<?php 
require_once(APP_PATH.'/migrations/ModelDefinition.php');

/**
 * Class AccessMigration_100
 */
class AccessMigration_100 extends ModelDefinition
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('access', [
            'columns' => [
                $this->_integer('id', ['autoIncrement'=>true, 'unsigned'=>true, 'notNull'=>true]),
                $this->_char('service', ['size'=>12, 'notNull'=>true]),
                $this->_varchar('uid', ['size'=>400, 'notNull'=>true]),
                $this->_varchar('access_token', ['size'=>400]),
                $this->_varchar('refresh_token',['size'=>400]),
                $this->_integer('status', ['size'=>1, 'notNull'=>true, 'default'=>0]),
                $this->_text('info'),
                $this->_text('errors'),
                $this->_timestamp('expires_at'),
                $this->_timestamp('created_at'),
                $this->_timestamp('updated_at'),
                $this->_timestamp('deleted_at'),
            ],
            'indexes' => [
                $this->_index('id'),
                $this->_index('service,uid', 'access_uniqueness', true),
            ],
        ]);
    }
}
