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

	protected static function service_name() { return 'facebook'; }

	function parse_api_response($resp, &$next, &$errors) {
		$content = json_decode($resp->body, true);
		if(array_key_exists(self::API_KEY_ERROR, $content)) {
			$errors = $content[self::API_KEY_ERROR];
			return null;
		}
		if(array_key_exists(self::API_KEY_PAGE, $content)
		&& array_key_exists(self::API_KEY_PAGE_NEXT, $content[self::API_KEY_PAGE])) {
			$next = $content[self::API_KEY_PAGE][self::API_KEY_PAGE_CURSOR][self::API_KEY_PAGE_CURSOR_NEXT];
		} else {
			$next = null;
		}
		if(array_key_exists(self::API_KEY_DATA, $content)) {
			return $content[self::API_KEY_DATA];
		} else {
			return null;
		}
	}

	function request_api_accounts($access) {
		return $this->_request('get', 'me/adaccounts', $access, ['access_token'=>$access->access_token]);
	}

	function retrieve_api_accounts_ids($data,  &$rets) {
		$rets += array_map(function($account_data) {
			return $account_data['account_id'];
		}, $data);
		return $rets;
	}

	protected function updated_token($access) {
		// TODO
		return $access->access_token;
	}



	



	

	public function insightAction($limits=20) {
		// list accounts order by visited_at timestamp

		// retrieve & update adaccount info
		// campaign - adset - ad - insight

	}
}