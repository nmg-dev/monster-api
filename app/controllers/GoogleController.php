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
    	$access = $params['id_token'];
    	$refresh = $params['login_hint'];
    	$expires = $params['expires_at'];

    	$resp = $this->_api('get', 'userinfo', );

    	return [

    	];
    }

    protected function authRedirectUrl() {
    	return $this->buildGetUrl('https://accounts.google.com/o/oauth2/v2/auth', [
    		'redirect_uri' => 
    		'access_type' => 'offline',
    		'include_granted_scopes' => 'true',

    	]);
    }

}

