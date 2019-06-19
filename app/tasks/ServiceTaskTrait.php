<?php

use Phalcon\Http\Client\Request as RequestClient;

use App\Models\Access;
use App\Models\Account;
use App\Models\Adgroup;
use App\Models\Aditem;
use App\Models\Adrecord;
use App\Models\Campaign;
use App\Models\Permission;


trait ServiceTaskTrait  {
	abstract static function service_name();

	protected $_client;

	protected static function BucketTx($arr, $fn, $bucketSize=100) {
		$keys = array_keys($arr);
		while(0<count($keys)) {
			$_kys = array_splice($keys, 0, $bucketSize);
			if(!$_kys || count($_kys)<=0) break;
			$_vals = array_map(function($ky) use($arr) {
				return $arr[$ky]; 
			}, $_kys);
			// run tx
			$fn($_vals, $_kys);
		}
	}

	protected function _config($key=null, $defaultValue=null) {
		$_path = 'services.'.self::service_name();
		if($key)
			$_path .= ".$key";
		return $this->config->path($_path, $defaultValue);
	}

	protected function client() {
		if(!$this->_client)
			$this->_client = RequestClient::getProvider();
		return $this->_client;
	}

	protected function retrieveAccounts() {
		$accesses = $this->access_in_live();
		self::BucketTx($accesses, function($accs))
	}

	protected function access_in_live() {
		$query = Access::find([
			'conditions' => 'service = ?0 AND 0<status AND deleted_at is NULL',
			'bind' => [ self::service_name() ]
		]);
		$rets = [];
		foreach($query as $acc)
			$rets[$acc->id] = $acc;
		return $rets;
	}
}