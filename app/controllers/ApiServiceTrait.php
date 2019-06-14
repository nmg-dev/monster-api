<?php 

use Phalcon\Http\Client\Request as RequestClient;

use Models\Access;

trait ApiServiceTrait {
	/* 
	 * const SERVICE_NAME 
	 * const API_HOST
	 */

	protected abstract static function api_endpoint($endpoint);

	/* login function */
	protected abstract function oauth_redirect_uri();
	protected abstract function oauth_access_token($code);
	protected abstract function oauth_refresh_token($access);

	protected abstract function access_parse_info();

	protected function _api($method, $endpoint, $params) {
		$client = RequestClient::getProvider();
		$client->setBaseUri($this->api_endpoint($endpoint));

		var_dump([
			'url' => $this->api_endpoint($endpoint),
			'params' => $params
		]);

		$response = $client->$method($endpoint, $params);

		switch($response->header->get('Content-Type')) {
			case 'application/json':
				return json_decode($response->body);
			case 'text/xml':
				return simplexml_load_string($response->body);
			default:
				return $response->body;
		}
	}

	public function accessAction() {
		// available iff post method
		if($this->request->isPost()) {
			$access = new Access;
			$access->save($this->access_parse_info());
			// 
			$this->response->send();
		}
	}

	public function authAction($code=null) {
		if($this->request->hasQuery('token')) {
			die('fault action');
		}
		if($this->request->hasQuery('code')) {
			$code = $this->request->get('code');
			// send oauth access
			$resp = $this->oauth_access_token($code);
			// redirect to ok
			var_dump($resp);
			$this->response->setContent($resp->toString());
		} else {
			$redirect = $this->oauth_redirect_uri();
			// redirect into login screen
			$this->response->redirect($redirect);
		}
		// finally send
		$this->response->send();
	}
}