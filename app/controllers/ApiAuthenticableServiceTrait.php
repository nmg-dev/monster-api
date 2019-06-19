<?php

trait ApiAuthenticableServiceTrait {
	use ApiServiceTrait;

	protected function buildGetUrl($href, $params) {
		$query = '';
		foreach($params as $pk=>$pv) {
			$query .= 0<strlen($query) ? '&' : '?';
			$query .= "$pk=$pv";
		}
		return $href.$query;
	}

	protected function authRedirectUrl() { return ''; }
	protected function authRetrieveToken($code) { return null; }

	public function authAction() {
		if($this->request->has('code')) {
			$adata = $this->authRetrieveToken($this->request->get('code'));
			$this->view->access = $adata;
		} else {
			$aurl = $this->authRedirectUrl();
			$this->response->redirect($aurl);
			$this->view->disable();
		}
		$this->response->send();
	}
}