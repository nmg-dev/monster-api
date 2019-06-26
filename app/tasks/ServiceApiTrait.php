<?php

use Phalcon\Http\Client\Request as RequestClient;

trait ServiceApiTrait {
	use ServiceTaskTrait;
	// protected abstract function nextPage($parsedResp);
	
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

	protected function client() {
		if(!$this->_client)
			$this->_client = RequestClient::getProvider();
		return $this->_client;
	}

}