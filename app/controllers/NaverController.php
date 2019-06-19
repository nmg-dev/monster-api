<?php


class NaverController extends ControllerBase
{
	use ApiAuthenticableServiceTrait;
    const SERVICE_NAME = 'naver';
    const API_HOST = 'https://openapi.naver.com/v1';

    protected $access_token = null;

    protected function before_req(&$client, &$params) {
        if($this->access_token)
            $client->header->set('Authorization', 'Bearer '.$this->access_token);
    }

    protected function after_api(&$response) {
        return json_decode($response->body, true);
    }

	protected function access_parse_info($params) {
        // check api user info
        // die $this->_api()
        $expires = time() + intval($params['expires_in']);
        $this->setClientAuth($params['access_token']);
        // $this->access_token = $params['access_token'];
        $resp = $this->_api('get','nid/me');      

        return [
            'uid'=> strval($resp['response']['id']),
            'access_token' => $params['access_token'],
            'refresh_token' => $params['refresh_token'],
            'status' => 1,
            'expires_at' => $expires,
        ];
	}

    public function indexAction() {

    }

    protected function authRedirectUrl() {
        return $this->buildGetUrl('https://nid.naver.com/oauth2.0/authorize', [
            'response_type'=>'code',
            'state'=>'',
            'client_id'=>$this->config_client_id(),
            'redirect_uri' => $this->config->path('application.host').'/naver/auth'
        ]);
    }
    protected function authRetrieveToken($code) {
        $url = 'https://nid.naver.com/oauth2.0/token';
        $resp = $this->_req('get', $url, [
			'client_id' => $this->config_client_id(),
			'client_secret' => $this->config_client_secret(),
			'code' => $this->request->get('code'),
			'state' => '',
			'grant_type' => 'authorization_code'
        ]);

        return $resp->body;
    }
}

