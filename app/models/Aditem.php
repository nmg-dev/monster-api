<?php

namespace App\Models;

class Aditem extends \Phalcon\Mvc\Model
{
    use \Phalcon\Models\SoftDeletes;

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $service;

    /**
     *
     * @var integer
     */
    public $account_id;

    /**
     *
     * @var integer
     */
    public $campaign_id;

    /**
     *
     * @var integer
     */
    public $group_id;

    /**
     *
     * @var string
     */
    public $uid;

    /**
     *
     * @var string
     */
    public $profile;

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
     * @var string
     */
    public $stats;

    /**
     *
     * @var string
     */
    public $period_from;

    /**
     *
     * @var string
     */
    public $period_till;

    /**
     *
     * @var string
     */
    public $errors;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     *
     * @var string
     */
    public $visited_at;

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
     *
     * @var string
     */
    public $deleted_at;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("monsters");
        $this->setSource("aditems");
        $this->hasMany('id', 'Models\Adrecord', 'item_id', ['alias' => 'records']);
        $this->belongsTo('account_id', 'Models\Account', 'id', ['alias' => 'account']);
        $this->belongsTo('group_id', 'Models\Adgroup', 'id', ['alias' => 'group']);
        $this->belongsTo('campaign_id', 'Models\Campaign', 'id', ['alias' => 'campaign']);
    }


    public function beforeSave() {
        $this->created_timestamp();
        $this->updated_timestamp();
        $this->timestrings(['visited_at', 'deleted_at']);
        
        $this->jsonite('profile');
        $this->jsonite('stats');
        $this->jsonite('errors');
    }

    public function afterFetch() {
        $this->jsonparse('profile');
        $this->jsonparse('stats');
        $this->jsonparse('errors');
        $this->timestamps(['visited_at', 'created_at','updated_at', 'deleted_at']);
    }


    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'aditems';
    }
}
