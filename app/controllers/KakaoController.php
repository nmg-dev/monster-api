<?php


class KakaoController extends ControllerBase
{
	use ApiServiceTrait;
    const SERVICE_NAME = 'kakao';
    const API_HOST = 'https://kapi.kakao.com/v1';
    protected $access_token;

    protected function before_req(&$client, &$params) {
    	if($this->access_token) 
    		$client->header->set('Authorization', 'Bearer '.$this->access_token);
    	$client->header->set('Content-type', 'application/x-www-form-urlencoded;charset=utf-8');
    }

    protected function after_api(&$response) {
    	return json_decode($response->body, true);
    }

    public function indexAction()
    {

    }

    protected function access_parse_info($params) {
    	$expires = time() + intval($params['expires_in']);

    	$this->access_token = $params['access_token'];
    	$resp = $this->_api('get', 'user/me');

    	return [
    		'uid' => strval($resp['id']),
    		'access_token' => $params['access_token'],
    		'refresh_token' => $params['refresh_token'],
    		'status' => 1,
    		'expires_at' => $expires
    	];
    } 

}

