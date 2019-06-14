<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class RecordsMigration_100
 */
class RecordsMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('records', [
                'columns' => [
                    new Column(
                        'item_id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'size' => 1,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'day_id',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'item_id'
                        ]
                    ),
                    new Column(
                        'impressions',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 1,
                            'after' => 'day_id'
                        ]
                    ),
                    new Column(
                        'clicks',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 1,
                            'after' => 'impressions'
                        ]
                    ),
                    new Column(
                        'actions',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 1,
                            'after' => 'clicks'
                        ]
                    ),
                    new Column(
                        'costs',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => 'actions'
                        ]
                    ),
                    new Column(
                        '_stats',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => 'costs'
                        ]
                    ),
                    new Column(
                        'created_at',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => '_stats'
                        ]
                    ),
                    new Column(
                        'updated_at',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => 'created_at'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('sqlite_autoindex_records_1', ['item_id', 'day_id'], 'PRIMARY')
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
