<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class ItemsMigration_100
 */
class ItemsMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('items', [
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
                        'account_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'service'
                        ]
                    ),
                    new Column(
                        'campaign_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'account_id'
                        ]
                    ),
                    new Column(
                        'adgroup_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'campaign_id'
                        ]
                    ),
                    new Column(
                        'item_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'adgroup_id'
                        ]
                    ),
                    new Column(
                        '_access',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => 'item_id'
                        ]
                    ),
                    new Column(
                        '_account',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => '_access'
                        ]
                    ),
                    new Column(
                        '_campaign',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => '_account'
                        ]
                    ),
                    new Column(
                        '_adgroup',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => '_campaign'
                        ]
                    ),
                    new Column(
                        '_iteminfo',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => '_adgroup'
                        ]
                    ),
                    new Column(
                        '_errors',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => '_iteminfo'
                        ]
                    ),
                    new Column(
                        'status',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "1",
                            'notNull' => true,
                            'size' => 1,
                            'after' => '_errors'
                        ]
                    ),
                    new Column(
                        'created_at',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => 'status'
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
                        'visited_at',
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
                            'after' => 'visited_at'
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
