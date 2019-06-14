<?php

class FacebookController extends ControllerBase
{
	use ApiServiceTrait;
	protected static function api_endpoint($endpoint) { 
		return "https://graph.facebook.com/v3.3/$endpoint"; 
	}
	protected function oauth_redirect_uri() {
		$redirect = $this->config->path('application.host').$this->request->getURI();
		$client_id = $this->config->path('services.facebook.client_id');
		return "https://www.facebook.com/dialog/oauth"
			."?client_id=$client_id"
			."&redirect_uri={$redirect}";
	}

	protected function oauth_access_token($code) {
		return $this->_api('get', 'oauth/access_token', [
			'code' => $code,
			'redirect_uri' => urlencode($this->config->path('application.host').'/facebook/auth'),
			'client_id' => $this->config->path('services.facebook.client_id'),
			'client_secret' => $this->config->path('services.facebook.client_secret'),
		]);
	}
	protected function oauth_refresh_token($access) {
		// TODO: later
	}

	protected function  access_parse_info() {
		// $data = $this->request->
		return [];
	}
}

