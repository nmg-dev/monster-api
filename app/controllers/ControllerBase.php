<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
	protected $req;
	protected $resp;
	protected $conf;

	public function onConstruct() {
		// $this->req = new \Phalcon\Http\Request;
		// $this->resp = new \Phalcon\Http\Response;
		// $this->conf = $this->getDI()->getConfig();
	}
}
