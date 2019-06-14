<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class SqliteSequenceMigration_100
 */
class SqliteSequenceMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('sqlite_sequence', [
                'columns' => [
                    new Column(
                        'name',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'seq',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 1,
                            'after' => 'name'
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
