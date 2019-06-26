<?php

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

	protected function parse_api_response($resp, &$next, &$errors) {
		$content = json_decode($resp->body, true);
		if(array_key_exists(API_KEY_ERROR, $content)) {
			$errors = $content[API_KEY_ERROR];
			return null;
		}
		if(array_key_exists(API_KEY_PAGE, $content)
		&& array_key_exists(API_KEY_PAGE_NEXT, $content[API_KEY_PAGE])) {
			$next = $content[API_KEY_PAGE][API_KEY_PAGE_CURSOR][API_KEY_PAGE_CURSOR_NEXT];
		} else {
			$next = null;
		}
		if(array_key_exists(API_KEY_DATA, $content)) {
			return $content[API_KEY_DATA];
		} else {
			return null;
		}
	}

	protected function updated_token($access) {
		// TODO
		return $access->access_token;
	}

	protected function retrieve_account_ids($access) {
		$account_ids = [];
		$token = $this->updated_token($access);
		$next_page = null;
		$params = null;
		$errors = null;
		do {
			$params = $next_page ? ['after' => $next_page] : null;
			$resp = $this->_request('get', 'me/adaccounts', $token);
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
			$account_ids += array_map(function($account_data) {
				return $account_data['account_id'];
			}, $data);
		} while($next_page!=null);

		return $account_ids;
	}



	// retrieve & update associated adaccounts for the access
	public function accountAction($limits=10) {
		// $_now = time();

		foreach($this->list_access as $access) {
			// retrieve adaccounts
			$account_ids = [];
			$visited = time();
			// api retrieved adaccount uids
			$next_ids = $this->retrieve_account_ids($access);
			// database loaded adaccount uids
			$prev_ids = array_map(function($acc) { return $acc->uid; },
				$access->accounts);
			$accounts = $this->search_accounts_by_ids($next_ids + $prev_ids);
			// $permissions = $access->permissions;

			foreach($next_ids+$prev_ids as $aid) {
				// $account_id should be string (for to use on permission transaction)
				$aid = strval($aid);
				// adaccount in database and sustain - 
				if(array_key_exists($aid, $accounts) && in_array($aid, $next_ids)) {
					if(!in_array($aid, $prev_ids)) {
						// add permission only
						array_push($permissions, [
							'access_id' => $access->id,
							'account_id' => $aid,
						]);
					}
					// else, has database and it matches with current query result. // (continue)
				} 
				// adaccount not in database
				else if(array_key_exists($aid, $accounts)) {
					array_push($accounts, ['uid'=>$aid]);
					array_push($permissions, ['access_id'=>$access->id, 'account_id'=>$aid]);
				}
				// adaccount not presented at query result
				else if(in_array($aid, $next_ids)) {
					array_push($permissions, ['access_id'])
				}
			}
		}
	}

	public function insightAction($limits=20) {
		// list accounts order by visited_at timestamp

		// retrieve & update adaccount info
		// campaign - adset - ad - insight

	}
}