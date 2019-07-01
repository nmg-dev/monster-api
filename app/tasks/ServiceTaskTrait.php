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
			$entity = $this->$buildFn($container, $key, $data);
			if($entity)
				array_push($updates, $entity);
		}

		$this->_transaction(function($tx) use(&$container, $updates) {
			foreach($updates as $idx=>$entity) {
				$entity->save();
				$container[strval($entity->uid)] = $entity;
			}
		});
		return $container;
	}
 
 	// account insert builder combination
 	protected function _update_accounts_build(&$container, $uid, $data) {
 		if(array_key_exists($uid, $container)) {
 			$entity = $container[$uid];
 			
 		}
 		else {
 			$entity = new Account();
 			$entity->assign([
	 			'service' => self::service_name(),
	 			'uid' => $uid,
 			]);
 		}
 		$entity->assign([
 			'profile' => $this->_avalue('profile', $data),
 			'status' => $this->_avalue('status', $data),
 			'errors' => $this->_avalue('errors', $data),
 		]);
 		return $entity;
 	}
	protected function update_accounts(&$accounts, $aids) {
		return $this->_updates($accounts, $aids, '_update_accounts_build');
	}
	
	// permission insert build
	protected function insert_permissions(&$accounts, $access, $aids) {
		$access_id = $access->id;
		$inserts = array_map(function($aid) use(&$accounts, $access_id, $aids) {
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
	protected function _update_campaigns_build(&$container, $uid, $data) {
		if(array_key_exists($uid, $container)) {
			$entity = $container[$uid];
			
		} else {
			$entity = new Campaign();
			$entity->assign([
				'service' => $this->_account->service,
				'account_id' => intval($this->_account->id),
				'uid' => strval($uid),
			]);
		}
		$entity->assign([
			'profile' => $this->_avalue('profile', $data),
			'status' => $this->_avalue('status', $data, false),
			'errors' => $this->_avalue('errors', $data),
		]);
		return $entity;
	}
	protected function update_campaigns(&$campaigns, $dataset) {
		return $this->_updates($campaigns, $dataset, '_update_campaigns_build');
	}

	// adgroup insert builder combination
	protected function _update_adgroups_build(&$container, $uid, $data) {
		if(array_key_exists($uid, $container)) {
			$entity = $container[$uid];
			
		} else {
			$entity = new Adgroup();
			$entity->assign([
				'service' => $this->_account->service,
				'account_id' => intval($this->_account->id),
				'campaign_id' => $data['campaign_id'],
				'uid' => strval($uid),
			]);
		}
		$entity->assign([
			'profile' => $this->_avalue('profile', $data),
			'status' => $this->_avalue('status', $data, false),
			'period_from' => $this->_avalue('period_from', $data),
			'period_till' => $this->_avalue('period_till', $data),
			'errors' => $this->_avalue('errors', $data),
		]);
		return $entity;
	}
	protected function update_adgroups(&$adgroups, $group_data) {
		return $this->_updates($adgroups, $group_data, '_update_adgroups_build');
	}

	// aditem insert builder combination
	protected function _update_aditems_build(&$container, $uid, $data) {
		if(array_key_exists($uid, $container)) {
			$entity = $container[$uid];
			
		} else {
			$entity = new Aditem();
			$entity->assign([
				'service' => $this->_account->service,
				'account_id' => intval($this->_account->id),
				'campaign_id' => $data['campaign_id'],
				'group_id' => $data['group_id'],
				'uid' => strval($uid),
			]);
		}
		$entity->assign([
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

		return $entity;
	}
	protected function update_aditems(&$aditems, $item_data) {
		return $this->_updates($aditems, $item_data, '_update_aditems_build');
	}

	// adrecords inserts
	protected function update_adrecords_flush(&$stacks) {
		$this->_transaction(function($tx) use(&$stacks) {
			foreach($stacks as $st) 
				$st->save();
		});
		// clear current
		$stacks = [];
	}
	protected function update_adrecords(&$records, $rdata, $flush_threshold=200) {
		$stacks = [];
		foreach($rdata as $item_id=>$recs) {
			foreach($recs as $day_id=>$data) {
				// find
				if(array_key_exists($item_id, $records)
				&& array_key_exists($day_id, $records[$item_id])
				&& $records[$item_id][$day_id]) {
					$record = $records[$item_id][$day_id];
				} else {
					$record = new Adrecord();
					$record->assign([
						'item_id'=> intval($item_id), 
						'day_id' => $day_id,
					]);
				}
				$record->assign([
					'impressions' => $this->_avalue('impressions', $data),
					'clicks' => $this->_avalue('clicks', $data),
					'conversions' => $this->_avalue('conversions', $data),
					'spendings' => $this->_avalue('spendings', $data),
					'stats' => $this->_avalue('stats', $data),
					'errors' => null,
				]);
				array_push($stacks, $record);
			}

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

	protected function list_adrecords($date_format='Y-m-d', $days=-10) {
		$aditem_ids = array_map(function($item) { 
			return sprintf("%d", intval($item->id));
		}, $this->_aditems);
		if(count($aditem_ids)<=0)
			return [];

		$_records = Adrecord::find([
			sprintf('item_id IN (%s)', implode(',', $aditem_ids)),
			sprintf('day_id >= TIMESTAMPADD(DAY, %d, TODAY())', $days),
		]);
		$rets = [];
		foreach($_records as $rec) {
			if(!array_key_exists($rec->item_id, $rets))
				$rets[$rec->item_id] = [];
			$day_id = date($date_format, strtotime($rec->day_id));
			$rets[$rec->item_id][$day_id] = $rec;
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
		// try {
			$txFn($tx);
			$tx->commit();
		// } catch(TxFailed $txErr) {
			// fwrite(STDERR, $txErr->getMessage() . PHP_EOL);
   //  		fwrite(STDERR, $txErr->getTraceAsString() . PHP_EOL);
			// $tx->rollback();
		// }
	}




}