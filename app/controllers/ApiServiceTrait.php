<?php 

use Phalcon\Http\Client\Request as RequestClient;
use App\Models\Access;

trait ApiServiceTrait {
	/* 
	 * const SERVICE_NAME 
	 * const API_HOST
	 */

	// protected $__token = null;

	

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
	protected function config_host() { return $this->_config_('api_host');	}
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
		// $this->before_req($client, $params);
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
				'service = ?0 AND uid = ?1 AND deleted_at IS NULL',
				'bind' => [ self::SERVICE_NAME, $access_data['uid'] ]
			]);

			// var_dump($access);
			if(!$access) $access = new Access;
			$access->assign($access_data);

			$access->save();

			return json_encode($access->toArray(), JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
		} catch(\Exception $ex) {
			$this->response->setStatusCode(500, 'Access not assigned');
			$this->response->setContent(
				"\n".$ex->getMessage().
				"\n".$ex->getTraceAsString());
			$this->response->send();
		}
	}

	// protected function _oauthCodeParameterName() { return 'code' }
	
}