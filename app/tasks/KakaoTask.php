<?php

class KakaoTask extends \Phalcon\Cli\Task
{
	use ServiceApiTrait;

	protected static function service_name() { return 'kakao'; }

	static $CONVERSION_KEY_ORDERED = [
		// purchase (direct)
		'conv_px_purchase_dir',
		// purchase - sdk auto postback (indirect)
		'conv_sdk_purchase_indir',
		// in app purchase - postback
		'conv_sdk_iap_indir',
		// purchase - postback
		'conv_px_purchase_indir',

		// app install
		'conv_sdk_app_install_indir', 

		// link as a friend
		'conv_kf_pf_add', 

		// member registration
		'conv_sdk_cmpt_reg_dir',
		'conv_px_cmpt_reg_indir',
	];

	public static function conversions($metrics) {
		foreach(self::$CONVERSION_KEY_ORDERED as $ck) {
			if(array_key_exists($ck, $metrics))
				return $metrics[$ck];
		}
		return null;
	}

	function parse_api_response($resp, &$next=null, &$errors=null) {
		$_resp = json_decode($resp->body, true);
		$next = null;
		$errors = null;
		return $_resp['content'];
	}

	function request_api_accounts($nextpage=null) {
		$accounts = $this->_request('get', 'adAccounts', $this->_access);
		foreach($accounts as $idx=>$acc) {
			$accounts[$idx] = $this->_request('get', 'adAccount', $this->_access, [
				'adAccountId' => $acc['id']
			]);
		}
		return $accounts;
	}

	function request_api_campaigns($nextpage=null) {
		$campaign_data = $this->_request('get', 'campaigns', $this->_access, [
			'adAccountId' => $this->_account->uid
		]);
		$campaign_data['account_id'] = $this->_account->id;
	}

	function request_api_groups($nextpage=null) {
		$groups = [];
		foreach($this->_campaigns as $campaign) {
			$grp = $this->_request('get', 'adGroups', $this->_access, [
				'campaignId' => $campaign->uid,
			]);
			$grp['account_id'] = $campaign->account_id;
			$grp['campaign_id'] = $campaign->id;
			array_push($groups, $grp);
		}
		return $groups;
	}

	function request_api_items($nextpage=null) {
		$ads = null;
		foreach($this->_adgroups as $group) {
			$ads = $this->_request('get', 'creatives', $this->_access, [
				'adGroupId'=>$group->uid,
			]);
			foreach($ads as $idx=>$ad) {
				$adata = $this->_request('get', 'creative', $this->_account, [
					'creativeId' => $ad['id']
				]);
				$adata['account_id'] = $group->account_id;
				$adata['campaign_id'] = $group->campaign_id;
				$adata['group_id'] = $group->id;
				$ads[$idx] = array_merge($ad, $adata);
			}
		}
		return $ads;
	}

	function request_api_insights($nextpage=null) {
		$insights = [];
		foreach($this->_aditems as $ad) {
			$report = $this->_request('get', 'creative/report', $this->_access, [
				'creativeId' => $ad->uid,
				'datePreset' => 'YESTERDAY',
				'dimension' => 'DEVICE_TYPE',
				'metricGroup'=>'BASIC,ADDITION,MESSAGE,VIDEO,ADVIEW,CHAT_LANDING,PLUS_FRIEND,PIXEL_CONVERSION,SDK_CONVERSION'
			]);
			$report['item_id'] = $ad->id;
			if(!array_key_exists($ad->id, $insights))
				$insights[$ad->id] = [];
			$day = $report['start'];
			$insights[$ad->id][$day] = $report;
		}
		return $insights;
	}


	function retrieve_api_accounts($data) {
		foreach($data as $idx=>$d) {
			$data[$idx] = [
				'uid' => $d['id'],
				'profile' => [
					'name' => $d['name'],
					'type' => $d['type'],
					'business' => $d['ownerCompany'],
					'advertiser' => $d['advertiser'],
				],
				'owner' => $this->_account->uid,
				'status' => !$d['isAdminStop'] && !$d['isOutOfBalance'] && $d['config'] == 'ON' ? 1 : 0,
				'deleted_at' => $d['config'] == 'DEL' ? time() : null,
			];
		}
		return $data;
	}

	function _retrieve_api_(&$data, $keyMapFn) {
		foreach($data as $idx => $d) {
			$data[$idx] = $keyMapFn($d, $idx, $data);
		}
		return $data;
	}

	function retrieve_api_campaigns($data) {
		return $this->_retrieve_api_($data, function($d, $idx, $data) {
			return [
				'uid' => $d['id'],
				'account_id' => $d['account_id'],
				'profile' => [
					'name' => $d['name'],
					'adtype' => $d['adPurposeType'],
					'budget_daily' => $d['dailyBudgetAmount'],
				],
				'status' => !$d['isDailyBudgetAmountOver'] && $d['config'] == 'ON' ? 1 : 0,
				'deleted_at' => $d['config'] == 'DEL' ? time() : null,
			];
		});
	}

	

	function retrieve_api_groups($data) {
		return $this->_retrieve_api_($data, function($d, $idx, $data) {
			return [
				'uid' => $d['id'],
				'account_id' => $d['account_id'],
				'campaign_id' => $d['campaign_id'],
				'profile' => [
					'name' => $d['name'],
					'pricing'=> $d['pricingType'],
					'pacing' => $d['pacing'],
					'bid_type' => $d['bidStrategy'],
					'budget_daily' => $d['dailyBudgetAmount'],
					'cost_cap' => $d['bidAmount'],
				],
				'status' => $d['isValidPeriod'] && $d['config'] == 'ON' ? 1 : 0,
				'deleted_at' => $d['config'] == 'DEL' ? time() : null,
			];
		});
	}

	function retrieve_api_items($data) {
		return $this->_retrieve_api_($data, function($d, $idx, $data) {
			return [
				'uid' => $d['id'],
				'account_id' => $d['account_id'],
				'campaign_id' => $d['campaign_id'],
				'group_id' => $d['group_id'],
				'profile' => [
					'name' => $d['name'],
					'type' => $d['type'],
					'format' => $d['format'],
					'bid_amount' => $d['bidAmount'],
					'review' => $d['reviewStatus'],
				],
				'status' => $d['config'] == 'ON' ? 1 : 0,
				'deleted_at' => $d['config'] == 'DEL' ? time() : null,
			];
		});
	}

	function retrieve_api_insights($data) {
		return $this->_retrieve_api_($data, function($d, $idx, $data) {
			$m = $d['metrics'];
			return [
				'item_id' => $d['item_id'],
				'day_id' => $d['start'],
				'impressions' => $m['imp'],
				'clicks' => $m['click'],
				'conversions' => KakaoTask::conversions($m),
				'spendings' => $m['cost'],
				'stats' => $m,

			];
		});
		return $data;
	}

	protected function updated_token($access) {
		return $access->access_token;
	}


}