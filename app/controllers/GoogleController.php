<?php


class GoogleController extends ControllerBase
{
	use ApiAuthenticableServiceTrait;
    const SERVICE_NAME = 'google';
    const API_HOST = 'https://www.googleapis.com';

    public function indexAction()
    {

    }

    protected function userinfo_validation($uinfo) {
    	// option 1. free pass on every validation email
    	return $uinfo['verified_email'];
    	// option 2. restrict to certain domains
    	// return in_array($uinfo['hd'], ['nextmediagroup', 'fsn.com', 'cauly.com']);
    }

    protected function after_api(&$response) {
        return json_decode($response->body, true);
    }

    protected function access_parse_info($params) {
    	$expires = time() + intval($params['expires_in']);
    	$this->setClientAuth($params['access_token']);
    	$uinfo = $this->_api('get', 'oauth2/v2/userinfo');
    	
    	return [
    		'uid' => $uinfo['id'],
    		'access_token' => $params['access_token'],
    		'refresh_token' => $params['refresh_token'],
    		'status' => $this->userinfo_validation($uinfo) ? 1 : 0,
    		'expires_at' => $expires,
    	];
    }

    protected function authRedirectUrl() {
    	return $this->buildGetUrl('https://accounts.google.com/o/oauth2/v2/auth', [
    		'redirect_uri' => urlencode($this->config->path('application.host').'/google/auth'),
    		'scope' => $this->config->path('services.google.scopes'),
    		'access_type' => 'offline',
    		'response_type' => 'code',
    		'prompt'=>'consent',
    		'include_granted_scopes' => 'true',
    		'client_id' => $this->config_client_id(),
    	]);
    }

    protected function authRetrieveToken($code) {
    	$url = 'https://www.googleapis.com/oauth2/v4/token';
    	$params = [
    		'code' => $code,
    		'client_id' => $this->config_client_id(),
    		'client_secret' => $this->config_client_secret(),
    		'redirect_uri' => $this->config->path('application.host').'/google/auth',
    		'grant_type' => 'authorization_code',
    	];
    	
    	$resp = $this->_req('post', $url, $params);
    	return $resp->body;
    }

}

