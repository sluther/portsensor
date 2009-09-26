<?php
/***********************************************************************
| PortSensor(tm) developed by WebGroup Media, LLC.
|-----------------------------------------------------------------------
| All source code & content (c) Copyright 2008, WebGroup Media LLC
|   unless specifically noted otherwise.
|
| This source code may be modified for your own use.
|
| By using this software, you acknowledge having read this license
| and agree to be bound thereby.
| ______________________________________________________________________
|	http://www.portsensor.com	  http://www.webgroupmedia.com/
***********************************************************************/
/*
 * IMPORTANT LICENSING NOTE from your friendly PortSensor developers
 * 
 * Hey there!  You!  With the text editor...
 * 
 * You're totally welcome to make changes to this code so it suits your 
 * needs better.  We just ask that you respect our licensing and buy 
 * a copy of the product if you find it useful.
 * 
 * We've never believed in encoding our source code out of paranoia over not 
 * getting paid.  We want you to have the full source code and be able to 
 * make the tweaks your organization requires to get more done -- despite 
 * having less of everything than you might need (time, people, money, 
 * energy).  We shouldn't be your bottleneck by hording code.
 * 
 * Quality software backed by a dedicated team takes money to develop.  We 
 * don't want to be out of the office bagging groceries when you call up 
 * needing a helping hand.  We'd rather spend our free time coding your 
 * feature requests than mowing the neighbors' lawns for rent money. 
 * 
 * - Jeff Standen, Mike Fogg, Brenan Cavish, Darren Sugita, Dan Hildebrandt
 * 		and Joe Geck.
 *   WEBGROUP MEDIA LLC. - Developers of PortSensor
 */

class DAO_Monitor extends DevblocksORMHelper {
	const ID = 'id';
	const NAME = 'name';
	const GUID = 'guid';
	const SECRET_KEY = 'secret_key';
	const LAST_UPDATED = 'last_updated';
	const MIA_SECS = 'mia_secs';

	static function create($fields) {
		$db = DevblocksPlatform::getDatabaseService();
		
		$id = $db->GenID('generic_seq');
		
		$sql = sprintf("INSERT INTO monitor (id) ".
			"VALUES (%d)",
			$id
		);
		$db->Execute($sql);
		
		self::update($id, $fields);
		
		return $id;
	}
	
	static function update($ids, $fields) {
		parent::_update($ids, 'monitor', $fields);
	}
	
	/**
	 * @param string $where
	 * @return Model_Monitor[]
	 */
	static function getWhere($where=null) {
		$db = DevblocksPlatform::getDatabaseService();
		
		$sql = "SELECT id, name, guid, secret_key, last_updated, mia_secs ".
			"FROM monitor ".
			(!empty($where) ? sprintf("WHERE %s ",$where) : "").
			"ORDER BY id asc";
		$rs = $db->Execute($sql);
		
		return self::_getObjectsFromResult($rs);
	}

	/**
	 * @param integer $id
	 * @return Model_Monitor	 */
	static function get($id) {
		$objects = self::getWhere(sprintf("%s = %d",
			self::ID,
			$id
		));
		
		if(isset($objects[$id]))
			return $objects[$id];
		
		return null;
	}
	
	/**
	 * @param string $guid
	 * @return Model_Monitor|null
	 */
	static function getByGUID($guid) {
		$objects = self::getWhere(sprintf("%s = '%s'",
			self::GUID,
			addslashes($guid)
		));
		
		if(!is_array($objects) || empty($objects)) {
			return null;
		}
			
		return array_shift($objects);
	}
	
	/**
	 * @param integer $id
	 * @return Model_Monitor|null
	 */
	static function getByID($id) {
		$objects = self::getWhere(sprintf("%s = %d",
			self::ID,
			intval($id)
		));
		
		if(!is_array($objects) || empty($objects)) {
			return null;
		}
			
		return array_shift($objects);
	}
	
	/**
	 * @param ADORecordSet $rs
	 * @return Model_Monitor[]
	 */
	static private function _getObjectsFromResult($rs) {
		$objects = array();
		
		while(!$rs->EOF) {
			$object = new Model_Monitor();
			$object->id = $rs->fields['id'];
			$object->name = $rs->fields['name'];
			$object->guid = $rs->fields['guid'];
			$object->secret_key = $rs->fields['secret_key'];
			$object->last_updated = intval($rs->fields['last_updated']);
			$object->mia_secs = intval($rs->fields['mia_secs']);
			$objects[$object->id] = $object;
			$rs->MoveNext();
		}
		
		return $objects;
	}
	
	static function delete($ids) {
		if(!is_array($ids)) $ids = array($ids);
		$db = DevblocksPlatform::getDatabaseService();
		
		$ids_list = implode(',', $ids);
		
		if(empty($ids_list))
			return;
		
		// Monitors
		$db->Execute(sprintf("DELETE FROM monitor WHERE id IN (%s)",$ids_list));
		
		// Cascade Sensors
		$sensors = DAO_Sensor::getWhere(sprintf("%s IN (%s)",
			DAO_Sensor::MONITOR_ID,
			$ids_list
		));
		if(!empty($sensors)) {
			DAO_Sensor::delete(array_keys($sensors));
		}
	}
};

class DAO_Device extends DevblocksORMHelper {
	const ID = 'id';
	const GUID = 'guid';
	const NAME = 'name';
	const LAST_UPDATED = 'last_updated';

	static function create($fields) {
		$db = DevblocksPlatform::getDatabaseService();
		
		$id = $db->GenID('generic_seq');
		
		$sql = sprintf("INSERT INTO device (id, name) ".
			"VALUES (%d, '')",
			$id
		);
		$db->Execute($sql);
		
		self::update($id, $fields);
		
		return $id;
	}
	
	static function update($ids, $fields) {
		parent::_update($ids, 'device', $fields);
	}
	
	/**
	 * @param string $where
	 * @return Model_Device[]
	 */
	static function getWhere($where=null) {
		$db = DevblocksPlatform::getDatabaseService();
		
		$sql = "SELECT id, guid, name, last_updated ".
			"FROM device ".
			(!empty($where) ? sprintf("WHERE %s ",$where) : "").
			"ORDER BY name asc";
		$rs = $db->Execute($sql);
		
		return self::_getObjectsFromResult($rs);
	}
	
	/**
	 * @param string $guid
	 * @return Model_Device|null
	 */
	static function getByGUID($guid) {
		$objects = self::getWhere(sprintf("%s = '%s'",
			self::GUID,
			addslashes($guid)
		));
		
		if(!is_array($objects) || empty($objects)) {
			return null;
		}
			
		return array_shift($objects);
	}
	
	/**
	 * @param integer $id
	 * @return Model_Device|null
	 */
	static function getByID($id) {
		$objects = self::getWhere(sprintf("%s = %d",
			self::ID,
			intval($id)
		));
		
		if(!is_array($objects) || empty($objects)) {
			return null;
		}
			
		return array_shift($objects);
	}
	
	/**
	 * @param ADORecordSet $rs
	 * @return Model_Device[]
	 */
	static private function _getObjectsFromResult($rs) {
		$objects = array();
		
		while(!$rs->EOF) {
			$object = new Model_Device();
			$object->id = intval($rs->fields['id']);
			$object->guid = $rs->fields['guid'];
			$object->name = $rs->fields['name'];
			$object->last_updated = intval($rs->fields['last_updated']);
			$objects[$object->id] = $object;
			$rs->MoveNext();
		}
		
		return $objects;
	}
	
	static function delete($ids) {
		if(!is_array($ids)) $ids = array($ids);
		$db = DevblocksPlatform::getDatabaseService();
		
		$ids_list = implode(',', $ids);
		
		if(empty($ids_list))
			return;
		
		$db->Execute(sprintf("DELETE FROM device WHERE id IN (%s)",$ids_list));
		
		// Cascade Sensors
		$sensors = DAO_Sensor::getWhere(sprintf("%s IN (%s)",
			DAO_Sensor::DEVICE_ID,
			$ids_list
		));
		if(!empty($sensors)) {
			DAO_Sensor::delete(array_keys($sensors));
		}
	}

};

class DAO_Sensor extends DevblocksORMHelper {
	const ID = 'id';
	const DEVICE_ID = 'device_id';
	const MONITOR_ID = 'monitor_id';
	const NAME = 'name';
	const STATUS = 'status';
	const METRIC = 'metric';
	const OUTPUT = 'output';
	const LAST_UPDATED = 'last_updated';

	static function create($fields) {
		$db = DevblocksPlatform::getDatabaseService();
		
		$id = $db->GenID('generic_seq');
		
		$sql = sprintf("INSERT INTO sensor (id) ".
			"VALUES (%d)",
			$id
		);
		$db->Execute($sql);
		
		self::update($id, $fields);
		
		return $id;
	}
	
	static function update($ids, $fields) {
		parent::_update($ids, 'sensor', $fields);
	}
	
	/**
	 * @param string $where
	 * @return Model_Sensor[]
	 */
	static function getWhere($where=null) {
		$db = DevblocksPlatform::getDatabaseService();
		
		$sql = "SELECT id, device_id, monitor_id, name, status, metric, output, last_updated ".
			"FROM sensor ".
			(!empty($where) ? sprintf("WHERE %s ",$where) : "").
			"ORDER BY name asc";
		$rs = $db->Execute($sql) or die($sql);
		
		return self::_getObjectsFromResult($rs);
	}

	/**
	 * @param integer $id
	 * @return Model_Sensor	 */
	static function get($id) {
		$objects = self::getWhere(sprintf("%s = %d",
			self::ID,
			$id
		));
		
		if(isset($objects[$id]))
			return $objects[$id];
		
		return null;
	}
	
	/**
	 * @param ADORecordSet $rs
	 * @return Model_Sensor[]
	 */
	static private function _getObjectsFromResult($rs) {
		$objects = array();
		
		while(!$rs->EOF) {
			$object = new Model_Sensor();
			$object->id = $rs->fields['id'];
			$object->device_id = $rs->fields['device_id'];
			$object->monitor_id = $rs->fields['monitor_id'];
			$object->name = $rs->fields['name'];
			$object->status = $rs->fields['status'];
			$object->metric = $rs->fields['metric'];
			$object->output = $rs->fields['output'];
			$object->last_updated = $rs->fields['last_updated'];
			$objects[$object->id] = $object;
			$rs->MoveNext();
		}
		
		return $objects;
	}
	
	static function delete($ids) {
		if(!is_array($ids)) $ids = array($ids);
		$db = DevblocksPlatform::getDatabaseService();
		
		$ids_list = implode(',', $ids);
		
		$db->Execute(sprintf("DELETE FROM sensor WHERE id IN (%s)", $ids_list));
		
		DAO_SensorEvent::deleteBySensorIds($ids_list);
	}
	
};

class DAO_SensorEvent extends DevblocksORMHelper {
	const SENSOR_ID = 'sensor_id';
	const DEVICE_ID = 'device_id';
	const STATUS = 'status';
	const METRIC = 'metric';
	const LOG_DATE = 'log_date';

	static function log($sensor_id, $device_id, $status, $metric) {
		$db = DevblocksPlatform::getDatabaseService();
		
		$sql = sprintf("INSERT INTO sensor_event (sensor_id, device_id, status, metric, log_date) ".
			"VALUES (%d, %d, %d, %s, %d)",
			$sensor_id,
			$device_id,
			$status,
			$db->qstr($metric),
			time()
		);
		$db->Execute($sql);
		
		return true;
	}
	
	static function update($ids, $fields) {
		parent::_update($ids, 'sensor_event', $fields);
	}
	
	/**
	 * @param string $where
	 * @return Model_SensorEvent[]
	 */
	static function getWhere($where=null) {
		$db = DevblocksPlatform::getDatabaseService();
		
		$sql = "SELECT se.sensor_id as sensor_id, ".
			"se.device_id as device_id, ".
			"se.status as status, ".
			"se.metric as metric, ".
			"se.log_date, ".
			"d.name AS device_name, ".
			"s.name AS sensor_name ".
			"FROM sensor_event se ".
			"INNER JOIN sensor s ON (se.sensor_id=s.id) ".
			"INNER JOIN device d ON (se.device_id=d.id) ".
			(!empty($where) ? sprintf("WHERE %s ",$where) : "").
			"ORDER BY log_date desc";
		$rs = $db->Execute($sql);
		
		return self::_getObjectsFromResult($rs);
	}

	/**
	 * @param ADORecordSet $rs
	 * @return Model_SensorEvent[]
	 */
	static private function _getObjectsFromResult($rs) {
		$objects = array();
		
		while(!$rs->EOF) {
			$object = new Model_SensorEvent();
			$object->sensor_name = $rs->fields['sensor_name'];
			$object->sensor_id = $rs->fields['sensor_id'];
			$object->device_name = $rs->fields['device_name'];
			$object->device_id = $rs->fields['device_id'];
			$object->status = $rs->fields['status'];
			$object->metric = $rs->fields['metric'];
			$object->log_date = $rs->fields['log_date'];
			$objects[] = $object;
			$rs->MoveNext();
		}
		
		return $objects;
	}
	
	static function getCount() {
		$db = DevblocksPlatform::getDatabaseService();
		return $db->GetOne("SELECT count(log_date) FROM sensor_event");
	}
	
	static function deleteAll() {
		$db = DevblocksPlatform::getDatabaseService();
		$db->Execute("DELETE FROM sensor_event");
		return true;
	}
	
	static function deleteBySensorIds($ids) {
		if(!is_array($ids)) $ids = array($ids);
		$db = DevblocksPlatform::getDatabaseService();
		
		$ids_list = implode(',', $ids);
		
		$db->Execute(sprintf("DELETE FROM sensor_event WHERE sensor_id IN (%s)", $ids_list));
		
		return true;
	}
};

class DAO_Feed extends DevblocksORMHelper {
	const ID = 'id';
	const GUID = 'guid';
	const NAME = 'name';
	const SECRET_KEY = 'secret_key';

	static function create($fields) {
		$db = DevblocksPlatform::getDatabaseService();
		
		$id = $db->GenID('generic_seq');
		
		$sql = sprintf("INSERT INTO feed (id) ".
			"VALUES (%d)",
			$id
		);
		$db->Execute($sql);
		
		self::update($id, $fields);
		
		return $id;
	}
	
	static function update($ids, $fields) {
		parent::_update($ids, 'feed', $fields);
	}
	
	/**
	 * @param string $where
	 * @return Model_Feed[]
	 */
	static function getWhere($where=null) {
		$db = DevblocksPlatform::getDatabaseService();
		
		$sql = "SELECT id, guid, name, secret_key ".
			"FROM feed ".
			(!empty($where) ? sprintf("WHERE %s ",$where) : " ").
			"ORDER BY id asc";
		$rs = $db->Execute($sql);
		
		return self::_getObjectsFromResult($rs);
	}

	/**
	 * @param integer $id
	 * @return Model_Feed	 
	 */
	static function get($id) {
		$objects = self::getWhere(sprintf("%s = %d",
			self::ID,
			$id
		));
		
		if(isset($objects[$id]))
			return $objects[$id];
		
		return null;
	}
	
	/**
	 * @param string $guid
	 * @return Model_Feed|null
	 */
	static function getByGUID($guid) {
		$objects = self::getWhere(sprintf("%s = '%s'",
			self::GUID,
			addslashes($guid)
		));
		
		if(!is_array($objects) || empty($objects)) {
			return null;
		}
			
		return array_shift($objects);
	}
	
	/**
	 * @param ADORecordSet $rs
	 * @return Model_Feed[]
	 */
	static private function _getObjectsFromResult($rs) {
		$objects = array();
		
		while(!$rs->EOF) {
			$object = new Model_Feed();
			$object->id = $rs->fields['id'];
			$object->guid = $rs->fields['guid'];
			$object->name = $rs->fields['name'];
			$object->secret_key = $rs->fields['secret_key'];
			$objects[$object->id] = $object;
			$rs->MoveNext();
		}
		
		return $objects;
	}
	
	static function delete($ids) {
		if(!is_array($ids)) $ids = array($ids);
		$db = DevblocksPlatform::getDatabaseService();
		
		$ids_list = implode(',', $ids);
		
		if(empty($ids_list))
			return;
		
		$db->Execute(sprintf("DELETE FROM feed WHERE id IN (%s)",$ids_list));
		$db->Execute(sprintf("DELETE FROM feed_item WHERE feed_id IN (%s)",$ids_list));
	}

};

class DAO_FeedItem {
	static function setFeedDevices($feed_id, $device_ids) {
		$db = DevblocksPlatform::getDatabaseService();

		if(empty($feed_id) || !is_array($device_ids))
			return;
		
		$db->Execute(sprintf("DELETE FROM feed_item WHERE feed_id = %d", $feed_id));
		
		foreach($device_ids as $device_id) {
			$db->Execute(sprintf("INSERT INTO feed_item (feed_id, device_id) VALUES (%d,%d)",
				$feed_id,
				$device_id
			));
		}
	}
	
	static function getFeedDevices($feed_id) {
		$db = DevblocksPlatform::getDatabaseService();
		
		$rs = $db->Execute(sprintf("SELECT feed_id, device_id FROM feed_item WHERE feed_id=%d",$feed_id));
		
		$device_ids = array();
		
		while(!$rs->EOF) {
			$device_ids[] = intval($rs->fields['device_id']);
			$rs->MoveNext();
		}
		
		if(empty($device_ids))
			return array();
		
		return DAO_Device::getWhere(sprintf("%s IN (%s)",
			DAO_Device::ID,
			implode(',', $device_ids)
		));
	}

};

class DAO_Setting extends DevblocksORMHelper {
	static function set($key, $value) {
		$db = DevblocksPlatform::getDatabaseService();
		$db->Replace('setting',array('setting'=>$db->qstr($key),'value'=>$db->qstr($value)),array('setting'),false);
	}
	
	static function get($key) {
		$db = DevblocksPlatform::getDatabaseService();
		$sql = sprintf("SELECT value FROM setting WHERE setting = %s",
			$db->qstr($key)
		);
		$value = $db->GetOne($sql) or die(__CLASS__ . ':' . $db->ErrorMsg()); /* @var $rs ADORecordSet */
		
		return $value;
	}
	
	// [TODO] Cache as static/singleton or load up in a page scope object?
	static function getSettings() {
	    $cache = DevblocksPlatform::getCacheService();
	    if(false === ($settings = $cache->load(Application::CACHE_SETTINGS_DAO))) {
			$db = DevblocksPlatform::getDatabaseService();
			$settings = array();
			
			$sql = sprintf("SELECT setting,value FROM setting");
			$rs = $db->Execute($sql) or die(__CLASS__ . ':' . $db->ErrorMsg()); /* @var $rs ADORecordSet */
			
			while(!$rs->EOF) {
				$settings[$rs->Fields('setting')] = $rs->Fields('value');
				$rs->MoveNext();
			}
			
			$cache->save($settings, Application::CACHE_SETTINGS_DAO);
	    }
		
		return $settings;
	}
};

?>