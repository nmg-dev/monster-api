<?php

namespace App\Models;

class Account extends \Phalcon\Models\AbstractModel
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

    public static function NEW($service, $uid) {
        $account = new Account();
        $account->assign([
            'service' => $service,
            'uid' => $uid,
            'status'=>1,
        ]);
        $account->save();
        // var_dump($account);
        return $account;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("monsters");
        $this->setSource("accounts");
        $this->hasMany('id', 'Models\Adgroup', 'account_id', ['alias' => 'groups']);
        $this->hasMany('id', 'Models\Aditem', 'account_id', ['alias' => 'items']);
        $this->hasMany('id', 'Models\Campaign', 'account_id', ['alias' => 'campaigns']);
        // $this->hasMany('id', 'Model\Permissions', 'account_id', ['alias' => 'Permissions']);

        $this->hasManyToMany('id', 
            'Model\Permissions', 'account_id', 'access_id', 
            'Model\Access', 'id', ['alias'=>'accesses']);
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
        return 'accounts';
    }

}
