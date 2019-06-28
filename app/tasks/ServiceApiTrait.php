<?php

use Phalcon\Http\Client\Request as RequestClient;

use App\Models\Access;
use App\Models\Account;
use App\Models\Adgroup;
use App\Models\Aditem;
use App\Models\Adrecord;
use App\Models\Campaign;
use App\Models\Permission;

trait ServiceApiTrait {
	use ServiceTaskTrait;
	// cursors
	

	abstract function updated_token($access);
	abstract function parse_api_response($resp, &$next=null, &$errors=null);


	// accounts & permissions from access
	abstract function request_api_accounts();
	abstract function retrieve_api_accounts($data);

	// campaign, adgroup, ads from account
	abstract function request_api_campaigns();
	abstract function retrieve_api_campaigns($data);

	abstract function request_api_groups();
	abstract function retrieve_api_groups($data);

	abstract function request_api_items();
	abstract function retrieve_api_items();

	// ad insigts
	abstract function request_api_insights($access);
	abstract function retrieve_api_insights($data);
	
	protected function api_url($path) {
		$url = $this->_config('api_host').'/'.$path;
		echo "\n".$url."\n";
		return $url;
	}

	protected function client() {
		if(!$this->_client)
			$this->_client = RequestClient::getProvider();
		return $this->_client;
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

	protected function _requests($req_func_name, $retrieve_func_name) {
		$rets = [];
		$next_page = null;
		$latest_cursor = null;
		$params = null;
		$errors = null;
		do {

			$params = $next_page ? ['after' => $next_page] : null;
			$resp = $this->$req_func_name();
			$data = $this->parse_api_response($resp, $next_page, $errors);
			// on error break
			if($errors) {
				$this->_access->errors = $errors;
				$this->_access->status = -1;
				break;
			} 
			// no data break
			else if(!$data) {
				$this->_access->status = -2;
				break;
			}

			// merge account id list
			$rets += $this->$retrieve_func_name($data);
			// rest a little to prevent token error
			if($next_page && $next_page != $latest_cursor) {
				sleep(1);
				$latest_cursor = $next_page;
			} else  {
				break;
			}
		} while($next_page!=null);
		return $rets;
	}

	protected function load_accounts() {
		$token = $this->updated_token($this->_access);
		$rets = $this->_requests('request_api_accounts', 'retrieve_api_accounts');
		// touch the access here
		$this->_access->save();

		return $rets;
	}


	// retrieve & update associated adaccounts for the access
	public function accessAction($limits=10) {
		$_service_name = self::service_name();
		foreach($this->list_access($limits) as $access) {
			$this->_access = $access;
			
			// api retrieved adaccounts
			$nexts = $this->load_accounts();
			// database loaded adaccounts
			$prevs = $access->listAccounts();

			// insert adaccount into database
			$aids = array_unique(array_keys($nexts) + array_keys($prevs));
			$accounts = $this->search_accounts_by_ids($aids);

			// add ad accounts
			$this->update_accounts($accounts, $nexts);

			// invalid permissions (prev - current)
			$aids_to_del = array_diff(array_keys($prevs), array_keys($nexts));
			$this->delete_permissions($accounts, $access, $aids_to_del);

			$aids_to_put = array_diff(array_keys($nexts), array_keys($prevs));
			$this->insert_permissions($accounts, $access, $aids_to_put);
		}
	}

	public function insightAction($limits=20) {
		foreach($this->list_accounts($limits) as $account) {
			// initiate status
			$this->_account = $account;
			$this->_access = $account->theMostAccess();

			// process campaigns
			$cdata = $this->_requests('request_api_campaigns','retrieve_api_campaigns');
			$campaigns = $account->listCampaigns();

			$this->update_campaigns($campaigns, $cdata);
			$this->_campaigns = $campaigns;

			// next to adgroups
			$gdata = $this->_requests('request_api_groups','retrieve_api_groups');
			$adgroups = $account->listGroups();

			$this->update_adgroups($adgroups, $gdata);
			$this->_adgroups = $adgroups;

			// ad items
			$adata = $this->_requests('request_api_items','retrieve_api_items');
			$items = $account->listItems();
			$this->update_aditems($items, $adata);
			$this->_aditems = $items;

			// records, finally
			$rdata = $this->_requests('request_api_insights','retrieve_api_insights');
			$this->update_adrecords($rdata);

			printf("[%s] %s SAVINGS : \n", $account->service, $account->uid);
			printf("Campaigns (%d) - %s\n", count($campaigns), implode("\n", array_keys($campaigns)));
			printf("Adgroups (%d) - %s\n", count($adgroups), implode("\n", array_keys($adgroups)));
			printf("Records (%d)\n\n", count($rdata));
		}
	}
}