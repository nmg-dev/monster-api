<?php

class FacebookTask extends \Phalcon\Cli\Task
{
	use ServiceTaskTrait;

	static function service_name() { return 'facebook'; }

	public function mainAction() {
		foreach($this->loadAccess() as $key=>$val) {
			printf("\n %d: [%d] %s - %s", $key, $val->id, $val->uid, $val->access_token);
		}
	}
}