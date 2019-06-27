<?php

use Phalcon\Mvc\Model\Transaction\Failed as TxFailed;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;

use App\Models\Access;
use App\Models\Account;
use App\Models\Adgroup;
use App\Models\Aditem;
use App\Models\Adrecord;
use App\Models\Campaign;
use App\Models\Permission;


trait ServiceTaskTrait  {
	abstract static function service_name();
	

	protected $_client;
	protected $_tx_manager;

	// load service related configure
	protected function _config($key=null, $defaultValue=null) {
		$_path = 'services.'.self::service_name();
		if($key)
			$_path .= ".$key";
		return $this->config->path($_path, $defaultValue);
	}

	
	protected function list_access($limits=20) {
		return Access::find([
			sprintf("service = '%s'", self::service_name()),
			sprintf("0 < status"),
			sprintf("deleted_at < 0"),
			'order' => 'updated_at desc',
			'limit' => $limits
		]);
	}

	protected function search_permissions_by_access($access) {
		return Permission::find([
			sprintf("access_id = %d", $access->id),
		]);
	}

	protected function search_accounts_by_ids($account_ids) {
		$_accounts = Account::find([
			sprintf("service = '%s'", self::service_name()),
			sprintf("uid IN (%s)", implode(',', array_map(function($account_id) {
				return "'$account_id'";
			}, $account_ids)))
		]);
		$rets = [];
		foreach($_accounts as $ac) {
			$rets[$ac->uid] = $ac;
		}
		return $rets;
	}

	protected function _txBlock() {
		if(!$this->_tx_manager)
			$this->_tx_manager = new TxManager();
		return $this->_tx_manager->get();
	}

	protected function _transaction($txFn) {
		$tx = $this->_txBlock();
		try {
			$txFn($tx);
			$tx->commit();
		} catch(TxFailed $txErr) {
			fwrite(STDERR, $txErr->getMessage() . PHP_EOL);
    		fwrite(STDERR, $txErr->getTraceAsString() . PHP_EOL);
			$tx->rollback();
		}
	}




}