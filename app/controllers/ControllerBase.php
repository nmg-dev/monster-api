<?php


use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
	public function onConstruct() {
		// $this->req = new \Phalcon\Http\Request;
		// $this->resp = new \Phalcon\Http\Response;
		// $this->conf = $this->getDI()->getConfig();
	}
}
