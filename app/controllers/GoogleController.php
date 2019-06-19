<?php


class GoogleController extends ControllerBase
{
	use ApiAuthenticableServiceTrait;
    const SERVICE_NAME = 'google';
    const API_HOST = 'https://www.googleapis.com';

    public function indexAction()
    {

    }

    protected function access_parse_info($params) {
    	var_dump($params);
    	// $access = $params['id_token'];
    	// $refresh = $params['login_hint'];
    	// $expires = $params['expires_at'];    	

    	// $resp = $this->_api('get', 'userinfo');

    	// return [

    	// ];
    }

    protected function authRedirectUrl() {
    	return $this->buildGetUrl('https://accounts.google.com/o/oauth2/v2/auth', [
    		'redirect_uri' => urlencode($this->config->path('application.host').'/google/auth'),
    		'scope' => $this->config->path('services.google.scopes'),
    		'access_type' => 'offline',
    		'response_type' => 'code',
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

    	die(json_encode($params));
    	
    	$resp = $this->_req('post', $url, $params);

    	return $resp->body;
    }

}

