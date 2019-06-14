<?php

trait SoftDeletable {
	public function on_initialize() {
		$this->addBehavior(new \Phalcon\Mvc\Model\Behavior\SoftDelete([
			'field' => 'deleted_at',
			'value' => Date::now(),
		]));
	}
}