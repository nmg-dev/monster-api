<?php

namespace App\Models;

class Adrecord extends \Phalcon\Mvc\Model
{

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
        $this->jsonite('stats');
        $this->jsonite('errors');
    }

    public function afterFetch() {
        $this->jsonparse('stats');
        $this->jsonparse('errors');
        $this->timestamps();
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

}
