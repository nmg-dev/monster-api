<?php

use App\Models\Account;
use App\Models\Access;
use App\Models\Permission;

class FacebookTask extends \Phalcon\Cli\Task
{
	// use ServiceTaskTrait;
	use ServiceApiTrait;

	const API_KEY_ERROR = 'error';
	const API_KEY_PAGE = 'paging';
	const API_KEY_PAGE_NEXT = 'next';
	const API_KEY_PAGE_CURSOR = 'cursors';
	const API_KEY_PAGE_CURSOR_NEXT = 'after';
	const API_KEY_DATA = 'data';


	// @override('ServiceTaskTrait')
	protected static function service_name() { return 'facebook'; }

	// @override('ServiceApiTrait')
	function parse_api_response($resp, &$next=null, &$errors=null) {
		$content = json_decode($resp->body, true);
		if(array_key_exists(self::API_KEY_ERROR, $content)) {
			// if($errors!=null)
			$errors = $content[self::API_KEY_ERROR];
			return null;
		}
		if(array_key_exists(self::API_KEY_PAGE, $content)
		&& array_key_exists(self::API_KEY_PAGE_NEXT, $content[self::API_KEY_PAGE])) {
			$next = $content[self::API_KEY_PAGE][self::API_KEY_PAGE_CURSOR][self::API_KEY_PAGE_CURSOR_NEXT];
		} 
		if(array_key_exists(self::API_KEY_DATA, $content)) {
			return $content[self::API_KEY_DATA];
		} else {
			return null;
		}
	}

	function _request_apis($suffix, $fields, $params, $nextpage=null) {
		if($this->_access && $this->_account) {
			$endpoint = sprintf('act_%s/%s', $this->_account->uid, $suffix);
			$parameters = $params + [
				'access_token' => $this->_access->access_token,
				'fields' => implode(',', $fields)
			];
			if($nextpage)
				$parameters[self::API_KEY_PAGE_CURSOR_NEXT] = $nextpage;
			return $this->_request('get', $endpoint, $this->_access, $parameters);
		}
		return null;
	}
	
	// @override('ServiceApiTrait')
	function request_api_accounts($nextpage=null) {
		$params = [
			'access_token'=>$this->_access->access_token,
			'fields' => 'id,name,account_id,account_status,currency,amount_spent,owner,balance,timezone_name',
		];
		if($nextpage)
			$params[self::API_KEY_PAGE_CURSOR_NEXT] = $nextpage;
		return $this->_request('get', 'me/adaccounts', $this->_access, $params);
	}


	function request_api_campaigns($nextpage=null) {
		return $this->_request_apis('campaigns', 
			['id','name','effective_status','objective','spend_cap','daily_budget'],
			['date_preset'=>'last_7d'], $nextpage);
	}

	function request_api_groups($nextpage=null) {
		return $this->_request_apis('adsets',
			['id','name','status','start_time','end_time','lifetime_budget','daily_budget','lifetime_spend_cap','bid_amount','bid_strategy','targeting', 'account_id', 'campaign_id'],
			['date_preset'=>'last_7d'], $nextpage);
	}

	function request_api_items($nextpage=null) {
		return $this->_request_apis('ads',
			['id','name','bid_amount','creative{id,name,body,object_url,link_url,object_type,status,thumbnail_url}','status', 'account_id', 'campaign_id', 'adset_id'],
			['date_preset'=>'last_7d'], $nextpage);
	}

	function request_api_insights($nextpage=null) {
		return $this->_request_apis('insights',
			['ad_id','impressions','reach','clicks','unique_clicks','actions','unique_actions','conversions','spend','relevance_score'],
			['date_preset'=>'last_7d', 'level'=>'ad', 'time_increment'=>1], $nextpage
		);
	}




	// @override('ServiceApiTrait')
	function retrieve_api_accounts($data) {
		$rets = [];
		foreach($data as $idx=>$adata) {
			$uid = $adata['account_id'];
			$rets[$uid] = [
				'uid' => $uid,
				'profile' => $adata,
				'owner' => $adata['owner'],
				'status' => $adata['account_status'],
			];
		}
		return $rets;
	}

	function retrieve_api_campaigns($data) {
		$rets = [];
		$account = $this->_account;
		foreach($data as $idx=>$cmp) {
			$uid = $cmp['id'];
			$rets[$uid] = [
				'uid' => $uid,
				'account_id' => $account->id,
				'profile' => [
					'name' => $this->_avalue('name', $cmp),
					'objective' => $this->_avalue('objective', $cmp),
					'cost_cap' => $this->_avalue('spend_cap', $cmp),
					'budget_daily' => $this->_avalue('daily_budget', $cmp),
				],
				'status' => $cmp['effective_status'] == 'ACTIVE' ? 1 : 0,
			];
		}

		return $rets;
	}

	function retrieve_api_groups($data) {
		$rets = [];
		$account = $this->_account;
		foreach($data as $idx=>$grp) {
			$uid = $grp['id'];
			$rets[$uid] = [
				'uid' => $uid,
				'account_id' => $account->id,
				'campaign_id' => intval($this->_campaigns[$grp['campaign_id']]->id),
				'period_from' => strtotime($grp['start_time']),
				'period_till' => strtotime($grp['end_time']),
				'profile' => [
					'budget_total' => $this->_avalue('lifetime_budget', $grp),
					'budget_daily' => $this->_avalue('daily_budget', $grp),
					'bid_strategy' => $this->_avalue('bid_strategy', $grp),
					'targeting' => $this->_avalue('targeting', $grp),
				],
				'status' => $grp['status'] == 'ACTIVE' ? 1 : 0,
				'deleted_at' => null,
			];
		}
		return $rets;
	}

	function retrieve_api_items($data) {
		$rets = [];
		$account = $this->_account;
		foreach($data as $idx=>$item) {
			$uid = $item['id'];
			$rets[$uid] = [
				'uid' => $uid,
				'account_id' => $account->id,
				'campaign_id' => intval($this->_campaigns[$item['campaign_id']]->id),
				'group_id' => intval($this->_adgroups[$item['adset_id']]->id),
				'profile' => [
					'name' => $this->_avalue('name', $item),
					'bid_amount' => $this->_avalue('bid_amount', $item),
					'creatives' => $this->_avalue('creative', $item),
				],
				'status' => $item['status'] == 'ACTIVE' ? 1 : 0,
			];
		}
		return $rets;
	}

	function retrieve_api_insights($data) {
		$rets = [];
		$account = $this->_account;
		$service = $account->service;
		$diction = (function($agg, $act) {
			$agg[$act['action_type']] = intval($act['value']);
			return $agg;
		});
		$summation = (function($total,$act) {
			$total += intval($act['value']);
			return $total;
		});
		foreach($data as $rec) {
			$aditem = $this->_aditems[$rec['ad_id']];
			
			$impressions = intval($rec['impressions']);
			$clicks = intval($rec['clicks']);
			$cost = intval($rec['spend']);
			$conversions = array_reduce($rec['conversions'], $summation, 0);

			array_push($rets, [
				'item_id' => $aditem->id,
				'day_id' => $rec['date_start'],
				'impressions' => $impressions,
				'clicks' => $clicks,
				'conversions' => $conversions,
				'spendings' => $cost,
				'stats' => [
					'impressions' => $impressions,
					'reach' => intval($rec['reach']),
					'clicks' => $clicks,
					'unique_clicks' => intval($rec['unique_clicks']),
					'spend' => intval($rec['spend']),
					'relevance_score' => intval($rec['relevance_score']),
					'actions' => array_reduce($rec['actions'], $diction, []),
					'conversions' => array_reduce($rec['conversions'], $diction, []),
					'unique_actions' => array_reduce($rec['unique_actions'], $diction, []),
					'relevance_score' => floatval($rec['relevance_score']),
				],
				'errors' => null,
			]);
		}
		return $rets;
	}

	// @override('ServiceApiTrait')
	protected function updated_token($access) {
		// TODO
		return $access->access_token;
	}
}