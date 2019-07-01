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

    public static function NEW($service, $uid, $profile=null, $status=1) {
        $account = new Account();
        $account->assign([
            'service' => $service,
            'uid' => $uid,
            'profile' => $profile,
            'status'=>$status,
        ]);
        // $account->save();
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
        $this->hasMany('id', 'App\Models\Adgroup', 'account_id', ['alias' => 'groups']);
        $this->hasMany('id', 'App\Models\Aditem', 'account_id', ['alias' => 'items']);
        $this->hasMany('id', 'App\Models\Campaign', 'account_id', ['alias' => 'campaigns']);
        $this->hasMany('id', 'App\Models\Permission', 'account_id', ['alias' => 'permissions']);

        $this->hasManyToMany('id', 
            'App\Models\Permission', 'account_id', 'access_id', 
            'App\Models\Access', 'id', ['alias'=>'accesses']);
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

    public function theMostAccess() {
        $most = null;
        foreach($this->permissions as $p) {
            // skip unavailables
            $acc = $p->access;
            if($acc->status<=0 || $acc->deleted_at || $acc->errors) continue;
            if(!$most)
                $most = $p;
            else if(!$most->has_own && $p->has_own)
                return $acc;
            else if(!$most->can_manage && $p->can_manage)
                $most = $p;
        }
        return $most->access;
    }

    public function listCampaigns() {
        printf("account_id = %d\n", $this->id);
        $query = Campaign::find([
            sprintf("service = '%s'", $this->service),
            sprintf("account_id = %d", $this->id)
        ]);
        $rets = [];
        foreach($query as $cmp)
            $rets[$cmp->uid] = $cmp;
        
        return $rets;
    }

    public function listGroups() {
        $query = Adgroup::find([
            sprintf("service = '%s'", $this->service),
            sprintf("account_id = %d", $this->id),
        ]);
        $rets = [];
        foreach($query as $grp)
            $rets[$grp->uid] = $grp;
        
        return $rets;
    }

    public function listItems() {
        $query = Aditem::find([
            sprintf("service = '%s'", $this->service),
            sprintf("account_id = %d", $this->id),
        ]);
        $rets = [];
        foreach($query as $item)
            $rets[$item->uid] = $item;
        return $rets;
    }
}
