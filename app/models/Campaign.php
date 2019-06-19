<?php

namespace App\Models;

class Campaign extends \Phalcon\Mvc\Model
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
        $this->setSource("campaigns");
        $this->hasMany('id', 'Models\Adgroup', 'campaign_id', ['alias' => 'groups']);
        $this->hasMany('id', 'Models\Aditem', 'campaign_id', ['alias' => 'items']);
        $this->belongsTo('account_id', 'Models\Account', 'id', ['alias' => 'account']);
    }


    public function beforeSave() {
        $this->created_timestamp();
        $this->updated_timestamp();
        $this->timestrings(['visited_at', 'deleted_at']);
        
        $this->jsonite('profile');
        $this->jsonite('errors');
    }

    public function afterFetch() {
        $this->jsonparse('profile');
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
        return 'campaigns';
    }

}