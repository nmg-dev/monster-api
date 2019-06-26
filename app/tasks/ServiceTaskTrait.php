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
	abstract protected function parse_api_response($resp, &$next, &$errors);

	protected $_client;

	// load service related configure
	protected function _config($key=null, $defaultValue=null) {
		$_path = 'services.'.self::service_name();
		if($key)
			$_path .= ".$key";
		return $this->config->path($_path, $defaultValue);
	}

	
	protected function list_access($limits) {
		return Access::find([
			sprintf("service = '%s'", self::service_name()),
			sprintf("0 < status"),
			sprintf("deleted_at < 0"),
			'order' => 'visited_at desc',
			'limit' => $limits
		]);
	}

	protected function search_permissions_by_access($access) {
		return Permission::find([
			sprintf("access_id = %d", $access->id),
		]);
	}

	protected function search_accounts_by_ids($account_ids) {
		$_accounts = Account::find([
			sprintf("service = '%s'", self::service_name()),
			sprintf("uid IN (%s)", implode(',', array_map(function($account_id) {
				return "'$account_id'";
			}, $account_ids)))
		]);
		return array_reduce($account_ids,
			function($agg, $aid) use(&$_accounts) {
				$agg[$aid] = array_key_exists($aid, $_accounts) ? $_accounts[$aid] : null;
				return $agg;
			}, 
		[]);
	}


}