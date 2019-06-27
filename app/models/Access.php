<?php
namespace App\Models;

class Access extends \Phalcon\Models\AbstractModel
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
    public $access_token;

    /**
     *
     * @var string
     */
    public $refresh_token;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     *
     * @var string
     */
    public $info;

    /**
     *
     * @var string
     */
    public $errors;

    /**
     *
     * @var string
     */
    public $expires_at;

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

    public static function NEW($service, $uid, $token, $rtoken=null, $info=null, $expires=null) {
        $access = new Access();
        $access->assign([
            'service' => $service,
            'uid' => $uid,
            'access_token' => $token,
            'refresh_token' => $rtoken,
            'info' => $info,
            'expires_at' => $expires,
            'status' => 1
        ]);
        $access->save();
        return $access;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("monsters");
        $this->setSource("access");
        // $this->hasMany('id', 'Model\Permissions', 'access_id', ['alias' => 'Permissions']);
        $this->hasManyToMany('id', 
            'App\Models\Permission', 'access_id', 'account_id', 
            'App\Models\Account', 'id', ['alias'=>'accounts']);
    }

    public function beforeSave() {
        $this->created_timestamp();
        $this->updated_timestamp();
        $this->timestrings(['expires_at', 'deleted_at']);
        $this->jsonite('info');
    }

    public function afterFetch() {
        $this->jsonparse('info');
        $this->timestamps(['expires_at', 'created_at','updated_at', 'deleted_at']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'access';
    }
}
