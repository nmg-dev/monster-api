<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class AccessMigration_100
 */
class AccessMigration_100 extends Migration
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
                    new Column(
                        'id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'autoIncrement' => true,
                            'size' => 1,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'service',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'id'
                        ]
                    ),
                    new Column(
                        'uid',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'service'
                        ]
                    ),
                    new Column(
                        'token',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => 'uid'
                        ]
                    ),
                    new Column(
                        'refresh',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => 'token'
                        ]
                    ),
                    new Column(
                        'status',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "1",
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'refresh'
                        ]
                    ),
                    new Column(
                        '_errors',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => 'status'
                        ]
                    ),
                    new Column(
                        'created_at',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => '_errors'
                        ]
                    ),
                    new Column(
                        'updated_at',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => 'created_at'
                        ]
                    ),
                    new Column(
                        'expires_at',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => 'updated_at'
                        ]
                    ),
                    new Column(
                        'deleted_at',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => 'expires_at'
                        ]
                    )
                ],
            ]
        );
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
