<?php
namespace Phalcon\Models;

trait SoftDeletes {

    use Queriable;

	protected static function softDeleteColumn() { return 'deleted_at'; }

	public function delete() {
		$sd = self::softDeleteColumn();
		$this->$sd = date('c');
		return $this->update();
	}
}