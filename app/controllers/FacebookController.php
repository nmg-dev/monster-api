<?php

class FacebookController extends ControllerBase
{
	use ApiServiceTrait;
	const SERVICE_NAME = 'facebook';
	const API_HOST = 'https://graph.facebook.com/v3.3';

	protected function after_api(&$response) {
		return json_decode($response->body, true);
	}

	protected function access_parse_info($params) {
		// var_dump($params);
		$resp = $this->_api('get', 'oauth/access_token', [
			'grant_type' => 'fb_exchange_token',
    		'client_id'  => $this->config_client_id(),
    		'client_secret' => $this->config_client_secret(),
    		'fb_exchange_token' => $params['accessToken']
		]);

		return [
			'uid' => $params['userID'],
			'access_token' => $resp['access_token'],
			'errors' => null,
			'expires_at' => strtotime('+2 months'),
			'status' => 1,
		];
	}
}

