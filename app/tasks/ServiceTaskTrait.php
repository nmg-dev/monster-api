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

	// cursors
	protected $_access;
	protected $_account;

	// temporary containers
	protected $_campaigns = [];
	protected $_adgroups = [];
	protected $_aditems = [];

	// load service related configure
	protected function _config($key=null, $defaultValue=null) {
		$_path = 'services.'.self::service_name();
		if($key)
			$_path .= ".$key";
		return $this->config->path($_path, $defaultValue);
	}

	// find key from array or default value
	protected function _avalue($key, $arr, $defaultValue=null) {
		return array_key_exists($key, $arr) ? $arr[$key] : $defaultValue;
	}

	// list login access (SELECT)
	protected function list_access($limits=20) {
		return Access::find([
			sprintf("service = '%s'", self::service_name()),
			sprintf("0 < status"),
			sprintf("deleted_at < 0"),
			// sprintf("updated_at < TIMESTAMPADD(HOUR, -8, NOW())")
			'order' => 'updated_at desc',
			'limit' => $limits
		]);
	}

	// general inserts transaction (INSERT)
	protected function _updates(&$container, $dataset, $buildFn) {
		$updates = [];

		foreach($dataset as $key=>$data) {
			$entity = $this->$buildFn($key, $data);
			if($entity)
				array_push($updates, $entity);
		}

		$this->_transaction(function($tx) use(&$container, $updates) {
			foreach($updates as $entity) {
				$entity->save();
				$container[strval($entity->uid)] = $entity;
			}
		});
		return $container;
	}
 
 	// account insert builder combination
 	protected function _update_accounts_build($uid, $data) {
 		
 		$service = self::service_name();
 		return Account::GetOne([
 			'service' => self::service_name(),
 			'uid' => $uid,
 		], [
 			'profile' => $this->_avalue('profile', $data),
 			'status' => $this->_avalue('status', $data),
 			'errors' => $this->_avalue('errors', $data),
 		]);
 	}
	protected function update_accounts(&$accounts, $aids) {
		return $this->_updates($accounts, $aids, '_update_accounts_build');
	}
	
	// permission insert build
	protected function insert_permissions(&$accounts, $access, $aids) {
		$access_id = $access->id;
		$inserts = array_map(function($aid) use(&$accounts, $access_id) {
			$account = $accounts[$aid];
			$owner = $this->_avalue('owner', $aids[$aid], false);
			$manager = $this->_avalue('manager', $aids[$aid], $owner);
			return Permission::NEW($access_id, $accounts[$aid]->id, $manager, $owner);
		}, $aids);

		$this->_transaction(function($tx) use(&$inserts) {
			foreach($inserts as $perm) $perm->save();
		});
	}

	// campaign insert builder combination
	protected function _update_campaigns_build($uid, $data) {
		return Campaign::GetOne([
			'service' => $this->_account->service,
			'account_id' => intval($this->_account->id),
			'uid' => strval($uid)
		], [
			'profile' => $this->_avalue('profile', $data),
			'status' => $this->_avalue('status', $data, false),
			'errors' => $this->_avalue('errors', $data),
		]);
	}
	protected function update_campaigns(&$campaigns, $dataset) {
		return $this->_updates($campaigns, $dataset, '_update_campaigns_build');
	}

	// adgroup insert builder combination
	protected function _update_adgroups_build($uid, $data) {
		// $campaign = $this->_campaigns[$data['campaign_id']];
		return Adgroup::GetOne([
			'service' => $this->_account->service,
			'account_id' => intval($this->_account->id),
			'campaign_id' => intval($data['campaign_id']),
			'uid' => strval($uid),
		], [
			'profile' => $this->_avalue('profile', $data),
			'status' => $this->_avalue('status', $data, false),
			'period_from' => $this->_avalue('period_from', $data),
			'period_till' => $this->_avalue('period_till', $data),
			'errors' => $this->_avalue('errors', $data),
		]);
	}
	protected function update_adgroups(&$adgroups, $group_data) {
		return $this->_updates($adgroups, $group_data, '_update_adgroups_build');
	}

	// aditem insert builder combination
	protected function _update_aditems_build($uid, $data) {
		// $group = $this->_adgroups[$data['group_id']];
		// $campaign = $this->_campaigns[$data['campaign_id']];
		return AdItem::GetOne([
			'service' => $this->_account->service,
			'account_id' => intval($this->_account->id),
			'campaign_id' => intval($data['campaign_id']),
			'group_id' => intval($data['group_id']),
			'uid' => strval($uid),
		], [
			'impressions' => $this->_avalue('impressions', $data),
			'clicks' => $this->_avalue('clicks', $data),
			'conversions' => $this->_avalue('conversions', $data),
			'spendings' => $this->_avalue('spendings', $data),
			'stats' => $this->_avalue('stats', $data),
			'profile' => $this->_avalue('profile', $data),
			'period_from' => $this->_avalue('period_from', $data),
			'period_till' => $this->_avalue('period_till', $data),
			'status' => $this->_avalue('status', $data, false),
			'errors' => $this->_avalue('errors', $data),
		]);
	}
	protected function update_aditems(&$aditems, $item_data) {
		return $this->_updates($aditems, $item_data, '_update_aditems_build');
	}

	// adrecords inserts
	protected function update_adrecords_flush(&$stacks) {
		$this->_transaction(function($tx) use(&$stacks) {
			foreach($stacks as $st) $st->save();
		});
		// clear current
		$stacks = [];
	}
	protected function update_adrecords($records, $flush_threshold=200) {
		$stacks = [];
		foreach($records as $data) {
			$record = Adrecord::GetOne([
				'item_id'=> intval($data['item_id']), 
				'day_id' => strtotime($data['day_id'])
			], [
				'impressions' => $this->_avalue('impressions', $data),
				'clicks' => $this->_avalue('clicks', $data),
				'conversions' => $this->_avalue('conversions', $data),
				'spendings' => $this->_avalue('spendings', $data),
				'stats' => $this->_avalue('stats', $data),
				'errors' => null,
			]);
			array_push($stacks, $record);

			if($flush_threshold < count($stacks))
				$this->update_adrecords_flush($stacks);
		}
		$this->update_adrecords_flush($stacks);
	}

	protected function delete_permissions(&$accounts, $access, $aids) {
		$access_id = $access->id;
		$deletes = $this->search_accounts_by_ids($aids);
		$this->_transaction(function($tx) use($deletes) {
			foreach($deletes as $entity) $entity->delete();
		});
	}

	



	protected function list_accounts($limits=20) {
		return Account::find([
			sprintf("service = '%s'", self::service_name()),
			sprintf("0 < status"),
			sprintf("delete_at < 0"),
			// sprintf("visited_at < TIMESTAMPADD(HOUR, -8, NOW())")
			'order' => 'visited_at desc',
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