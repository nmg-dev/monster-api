<?php 

use Phalcon\Http\Client\Request as RequestClient;

use App\Models\Access;
use App\Models\Account;
use App\Models\Campaign;
use App\Models\Adgroup;
use App\Models\Aditem;
use App\Models\Adrecord;

trait ApiServiceTrait {
	/* 
	 * const SERVICE_NAME 
	 * const API_HOST
	 */

	protected $__token = null;
	protected $__token_type = 'Bearer';

	protected function setClientAuth($token, $token_type='Bearer') {
		$this->__token = $token;
		$this->__token_type = $token_type;
	}

	protected static function api_endpoint($endpoint) {
		return self::API_HOST.'/'.$endpoint;
	}

	/* login function */
	// protected abstract function oauth_redirect_uri();
	// protected abstract function oauth_access_token($code);
	// protected abstract function oauth_refresh_token($access);

	protected abstract function access_parse_info($params);

	protected function _config_($key) {
		$service = self::SERVICE_NAME;
		return $this->config->path("services.$service.$key");
	}
	protected function config_host() { return $this->_config_('api_host', self::API_HOST);	}
	protected function config_client_id() { return $this->_config_('client_id'); }
	protected function config_client_secret() { return $this->_config_('client_secret'); }

	protected function before_req(&$client, &$params) {
	}
	protected function after_api(&$response) {
		return $response->body;
	} 

	protected function _req($method, $url, $params=[]) {
		$client = RequestClient::getProvider();
		if($this->__token)
			$client->header->set('Authorization', 
				sprintf('%s %s', $this->__token_type, $this->__token));
		$client->setBaseUri($url);
		return $client->$method($url, $params);
	}

	protected function _api($method, $endpoint, $params=[]) {
		$response = $this->_req($method, 
			$this->api_endpoint($endpoint), 
			$params);
		return $this->after_api($response);
	}

	public function accessAction() {
		// $access = new Access;
		try {
			$params = $this->request->getJsonRawBody(true);
			$access_data = $this->access_parse_info($params);
			// override initiali
			$access_data['service'] = self::SERVICE_NAME;
			$access_data['info'] = $params;

			$access = Access::findFirst([
				'service = ?0 AND uid = ?1',
				'bind' => [ self::SERVICE_NAME, $access_data['uid'] ]
			]);

			// var_dump($access);
			if(!$access) $access = new Access;
			$access->assign($access_data);

			$access->save();

			return json_encode($access->toArray(), 
				JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
		} catch(\Exception $ex) {
			$this->response->setStatusCode(500, 'Access not assigned');
			$this->response->setContent(
				"\n".$ex->getMessage().
				"\n".$ex->getTraceAsString());
			$this->response->send();
		}
	}

	protected function buildQueryParams() {
		$params = $this->request->getJsonRawBody(true);
		if(array_key_exists('service', $params))
			unset($params['service']);
		$options = [ sprintf('service = ?0')];
		$binds = [ self::SERVICE_NAME ];
		foreach($params as $k=>$v) {
			array_push($options, sprintf('%s = ?%d', $k, count($binds)));
			array_push($binds, $v);
		}
		$options['bind'] = $binds;
		return $options;
	}

	protected function _queryValues(&$entity, $cls, $params, $key) {
		$query = $cls::find($params);
		foreach($query as $val) {
			$entity[$key][strval($val->id)] = $val->toArray();
		}
	}

	protected function _buildAccountInfo($account, $withDetails=false) {
		$entity = $account->toArray();
		$query_params = [
			sprintf('service = ?0 AND account_id = ?1 AND deleted_at IS NULL'),
			'bind' => [self::SERVICE_NAME, intval($account->id)]
		];

		if($withDetails) {
			$this->_queryValues($entity, Campaign::class, $query_params, 'campaigns');
			$this->_queryValues($entity, Adgroup::class, $query_params, 'adgroups');
			$this->_queryValues($entity, Aditem::class, $query_params, 'ads');

			// ad insights
			if(!empty($entity['ads'])) {
				$item_ids = array_keys($entity['ads']);
				$query = Adrecord::find([
					sprintf('item_id in (%s) AND SUBDATE(NOW(), 3) <= day_id',
						implode(',', $item_ids)),
					'order' => 'item_id, day_id'
				]);
				foreach($query as $rec) {
					$item_id = strval($rec->item_id);
					$day_id = strval($rec->day_id);
					$insights = $rec->toArray();
					$entity['ads'][$item_id] = array_merge(
						$entity['ads'][$item_id], $insights);
					// $entity['ads'][$item_id]['insight'] = $insights;
				}

			}
		}
		return $entity;
	}

	public function accountAction($account_id=null) {
		$ret = [];
		if($account_id) {
			$account = Account::findFirst([
				'service = ?0 AND id = ?1',
				'bind' => [self::SERVICE_NAME, intval($account_id)]
			]);
			$ret = $this->_buildAccountInfo($account, true);
		} else {
			$params = $this->buildQueryParams();
			$query = Account::find($params);
			// $values = [];
			foreach($query as $account) {
				$ret[strval($account->id)] = $this->_buildAccountInfo($account);
			}
		}
		return json_encode($ret, 
			JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
	}

	// public function insightAction($account_id=null) {
	// 	$params = $this->buildQueryParams();
	// 	$query = Aditem::find($params);
	// }

	// protected function _oauthCodeParameterName() { return 'code' }
	
}