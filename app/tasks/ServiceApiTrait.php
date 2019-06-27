<?php

use Phalcon\Http\Client\Request as RequestClient;

trait ServiceApiTrait {
	use ServiceTaskTrait;
	abstract function updated_token($access);
	abstract function parse_api_response($resp, &$next, &$errors);
	abstract function request_api_accounts($access);
	abstract function retrieve_api_accounts_ids($account_data, &$rets);
	
	protected function api_url($path) {
		$url = $this->_config('api_host').'/'.$path;
		echo "\n".$url."\n";
		return $url;
	}

	protected function _request($method, $path, $access=null, $params=null) {
		$client = $this->client();
		// client setup
		if($access && $access->access_token)
			$client->header->set('Authorization', 'Bearer '.$access->access_token);
		$url = $this->api_url($path);
		$resp = $client->$method($url, $params);
		
		return $resp;
	}

	protected function _requests($access, $req_func_name, $retrieve_func_name) {
		$rets = [];
		$next_page = null;
		$params = null;
		$errors = null;
		do {
			$params = $next_page ? ['after' => $next_page] : null;
			$resp = $this->$req_func_name($access);
			$data = $this->parse_api_response($resp, $next_page, $errors);
			// on error break
			if($errors) {
				$access->errors = $errors;
				$access->status = -1;
				break;
			} 
			// no data
			else if(!$data) {
				$access->status = -2;
				break;
			}

			// merge account id list
			$this->$retrieve_func_name($data, $rets);
		} while($next_page!=null);
		return $rets;
	}

	protected function retrieve_account_ids($access) {
		$token = $this->updated_token($access);
		$rets = $this->_requests($access, 'request_api_accounts', 'retrieve_api_accounts_ids');
		// touch the access here
		$access->save();

		return $rets;
	}

	protected function client() {
		if(!$this->_client)
			$this->_client = RequestClient::getProvider();
		return $this->_client;
	}



	// retrieve & update associated adaccounts for the access
	public function accessAction($limits=10) {
		$_now = time();
		$_service_name = self::service_name();
		foreach($this->list_access() as $access) {
			// retrieve adaccounts
			$account_ids = [];
			$visited = time();
			// api retrieved adaccount uids
			$next_ids = $this->retrieve_account_ids($access);
			// database loaded adaccount uids
			$prev_ids = [];
			foreach($access->accounts as $ac)
				array_push($prev_ids, $ac->uid);
			$accounts = $this->search_accounts_by_ids($next_ids + $prev_ids);
			// add ad accounts
			$this->_transaction(function($tx) 
				use(&$accounts, $next_ids, $_service_name) {
				foreach($next_ids as $aid) {
					if(!array_key_exists($aid, $accounts)) {
						$accouts[$aid] = Account::NEW($_service_name, $aid);
					}
				}
			});

			// and the permissions
			$this->_transaction(function($tx) 
				use(&$accounts, &$access, $next_ids, $prev_ids, $_service_name) {
					array_map(function($aid) use(&$access, &$accounts, $_service_name) {
						Permission::DEL($access->id, $accounts[$aid]->id);
					}, array_diff($prev_ids, $next_ids));
					array_map(function($aid) use(&$access, &$accounts, $_service_name) {
						Permission::NEW($access->id, $accounts[$aid]->id);
					}, array_diff($next_ids, $prev_ids));
			});
		}
	}

}