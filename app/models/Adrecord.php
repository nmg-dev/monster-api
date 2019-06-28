<?php

namespace App\Models;

class Adrecord extends \Phalcon\Models\AbstractModel
{
    use \Phalcon\Models\Queriable;
    public static $FIELDS_TO_PUT = [
        'impressions',
        'clicks',
        'conversions',
        'spendings',
        'stats',
        'errors',
    ];

    /**
     *
     * @var integer
     */
    public $item_id;

    /**
     *
     * @var string
     */
    public $day_id;

    /**
     *
     * @var integer
     */
    public $impressions;

    /**
     *
     * @var integer
     */
    public $clicks;

    /**
     *
     * @var integer
     */
    public $conversions;

    /**
     *
     * @var string
     */
    public $spendings;

    /**
     *
     * @var json
     */
    public $stats;

    /**
     *
     * @var json
     */
    public $errors;

    /**
     *
     * @var string
     */
    public $created_at;

    /**
     *
     * @var string
     */
    public $updated_at;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("monsters");
        $this->setSource("adrecords");
        $this->belongsTo('item_id', 'Models\Aditem', 'id', ['alias' => 'item']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'adrecords';
    }


    public function beforeSave() {
        $this->created_timestamp();
        $this->updated_timestamp();
        $this->day_id = date('Y-m-d', strtotime($this->day_id));
        $this->jsonite('stats');
        $this->jsonite('errors');

    }

    public function afterFetch() {
        $this->jsonparse('stats');
        $this->jsonparse('errors');
        $this->day_id = date('Y-m-d', strtotime($this->day_id));
    }


    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Permissions[]|Permissions|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Permissions|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function GET($item_id, $day_id) {
        $rec = parent::findFirst([
            sprintf("item_id = %d", intval($item_id)),
            sprintf("day_id = DATE('%s')", $day_id),
        ]);
        if(!$rec) {
            $rec = new Adrecord();
            $rec->assign([
                'item_id' => $item_id,
                'day_id' => $day_id,
            ]);
        }
        return $rec;
    }

}
