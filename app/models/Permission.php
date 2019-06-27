<?php

namespace App\Models;

class Permission extends \Phalcon\Models\AbstractModel
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $access_id;

    /**
     *
     * @var integer
     */
    public $account_id;

    /**
     *
     * @var integer
     */
    public $can_manage;

    /**
     *
     * @var integer
     */
    public $has_own;

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

    public static function NEW($access_id, $account_id, $is_manager=null, $is_owner=null) {
        $permission = new Permission();
        echo $access_id.' '.$account_id."\n";
        $permission->assign([
            'access_id' => $access_id,
            'account_id' => $account_id,
            'can_manage' => $is_manager,
            'has_own' => $is_owner,
        ]);
        $permission->save();

        return $permission;
    }

    public static function DEL($access_id, $account_id) {
        $permission = self::findFirst([
            'access_id' => $access_id,
            'account_id' => $account_id
        ]);
        if($permssion)
            $permission->delete();
        return $permission;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("monsters");
        $this->setSource("permissions");
        $this->belongsTo('access_id', 'App\Models\Access', 'id', ['alias' => 'access']);
        $this->belongsTo('account_id', 'App\Models\Account', 'id', ['alias' => 'account']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'permissions';
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
