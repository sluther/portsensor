<?php
$db = DevblocksPlatform::getDatabaseService();
$datadict = NewDataDictionary($db); /* @var $datadict ADODB_DataDict */ // ,'mysql' 

$tables = array();
$indexes = array();

// ***** Application

$tables['monitor'] = "
	id I4 DEFAULT 0 NOTNULL PRIMARY,
	name C(64) DEFAULT '' NOTNULL,
	guid C(32) DEFAULT '' NOTNULL,
	last_updated I4
";

$indexes['monitor'] = array(
    'guid' => 'guid',
);

$tables['device'] = "
	id I4 DEFAULT 0 NOTNULL PRIMARY,
	name C(64) DEFAULT '' NOTNULL,
	guid C(32) DEFAULT '' NOTNULL,
	last_updated I4
";

$indexes['device'] = array(
    'guid' => 'guid',
);

$tables['sensor'] = "
	id I4 DEFAULT 0 NOTNULL PRIMARY,
	device_id I4 DEFAULT 0 NOTNULL,
	name C(64) DEFAULT '' NOTNULL,
	status I1 DEFAULT 0 NOTNULL,
	output C(255) DEFAULT '' NOTNULL,
	last_updated I4
";

$indexes['sensor'] = array(
    'device_id' => 'device_id',
);

$tables['sensor_event'] = "
	sensor_id I4 DEFAULT 0 NOTNULL,
	device_id I4 DEFAULT 0 NOTNULL,
	status I1 DEFAULT 0 NOTNULL,
	metric C(255) DEFAULT '' NOTNULL,
	log_date I4 DEFAULT 0 NOTNULL
";

$indexes['sensor_event'] = array(
    'sensor_id' => 'sensor_id',
    'device_id' => 'device_id',
    'status' => 'status',
    'log_date' => 'log_date',
);

$tables['feed'] = "
	id I4 DEFAULT 0 NOTNULL PRIMARY,
	guid C(32) DEFAULT '' NOTNULL,
	name C(64) DEFAULT '' NOTNULL
";

$indexes['feed'] = array(
    'guid' => 'guid',
);

$tables['feed_item'] = "
	feed_id I4 DEFAULT 0 NOTNULL PRIMARY,
	device_id I4 DEFAULT 0 NOTNULL PRIMARY
";

$tables['setting'] = "
	setting C(64) DEFAULT '' NOTNULL PRIMARY,
	value XL
";

//// `webapi_key` ========================
//if(!isset($tables['webapi_key'])) {
//	$flds ="
//		id I4 DEFAULT 0 NOTNULL PRIMARY,
//		nickname C(64) DEFAULT '' NOTNULL,
//		access_key C(32) DEFAULT '' NOTNULL,
//		secret_key C(40) DEFAULT '' NOTNULL,
//		rights XL
//	";
//	$sql = $datadict->CreateTableSQL('webapi_key', $flds);
//	$datadict->ExecuteSQLArray($sql);
//}
//
//$columns = $datadict->MetaColumns('webapi_key');
//$indexes = $datadict->MetaIndexes('webapi_key',false);
//
//if(!isset($indexes['access_key'])) {
//	$sql = $datadict->CreateIndexSQL('access_key','webapi_key','access_key');
//	$datadict->ExecuteSQLArray($sql);
//}

// [TODO] This could be part of the patcher
$currentTables = $db->MetaTables('TABLE', false);

if(is_array($tables))
foreach($tables as $table => $flds) {
	if(false === array_search($table,$currentTables)) {
		$sql = $datadict->CreateTableSQL($table,$flds);
		// [TODO] Need verify step
		// [TODO] Buffer up success and fail messages?  Patcher!
		if(!$datadict->ExecuteSQLArray($sql,false)) {
			echo '[' . $table . '] ' . $db->ErrorMsg();
			exit;
			return FALSE;
		}

		// Add indexes for this table if we have them
		if(is_array($indexes) && isset($indexes[$table]))
		foreach($indexes[$table] as $idxname => $idxflds) {
			$sqlarray = $datadict->CreateIndexSQL($idxname, $table, $idxflds);
			if(!$datadict->ExecuteSQLArray($sqlarray,false)) {
				echo '[' . $table . '] ' . $db->ErrorMsg();
				exit;
				return FALSE;
			}
		}
		
	}
}

// Patches

$db->Execute("DELETE FROM sensor WHERE monitor_id = 0");

// Setting
$columns = $datadict->MetaColumns('setting');

if(255 == $columns['VALUE']->max_length) {
	$datadict->ExecuteSQLArray($datadict->RenameColumnSQL('setting', 'value', 'value_old',"value_old C(255) DEFAULT '' NOTNULL"));
	$datadict->ExecuteSQLArray($datadict->AddColumnSQL('setting', "value B"));
	
	$sql = "SELECT setting, value_old FROM setting ";
	$rs = $db->Execute($sql);
	
	if($rs)
	while(!$rs->EOF) {
		@$db->UpdateBlob(
			'setting',
			'value',
			$rs->fields['value_old'],
			sprintf("setting = %s",
				$db->qstr($rs->fields['setting'])
			)
		);
		$rs->MoveNext();
	}
	
	if($rs)
		$datadict->ExecuteSQLArray($datadict->DropColumnSQL('setting', 'value_old'));
}

// Feeds

$columns = $datadict->MetaColumns('feed');
$indexes = $datadict->MetaIndexes('feed',false);

if(!isset($columns['SECRET_KEY'])) {
    $sql = $datadict->AddColumnSQL('feed', "secret_key C(64) DEFAULT '' NOTNULL");
    $datadict->ExecuteSQLArray($sql);
}

// Devices

$columns = $datadict->MetaColumns('device');
$indexes = $datadict->MetaIndexes('device',false);

if(!isset($columns['SECRET_KEY'])) {
    $sql = $datadict->AddColumnSQL('device', "secret_key C(64) DEFAULT '' NOTNULL");
    $datadict->ExecuteSQLArray($sql);
}

// Sensors

$columns = $datadict->MetaColumns('sensor');
$indexes = $datadict->MetaIndexes('sensor',false);

if(!isset($columns['METRIC'])) {
    $sql = $datadict->AddColumnSQL('sensor', "metric C(255) DEFAULT '' NOTNULL");
    $datadict->ExecuteSQLArray($sql);
}

if(!isset($indexes['metric'])) {
    $sql = $datadict->CreateIndexSQL('metric','sensor','metric');
    $datadict->ExecuteSQLArray($sql);
}

if(isset($columns['EXPECTED_POST_INTERVAL'])) {
	$sql = $datadict->DropColumnSQL('sensor','expected_post_interval');
    $datadict->ExecuteSQLArray($sql);
}

if(!isset($columns['MONITOR_ID'])) {
    $sql = $datadict->AddColumnSQL('sensor', "monitor_id I4 DEFAULT 0 NOTNULL");
    $datadict->ExecuteSQLArray($sql);
}

// Monitors

$columns = $datadict->MetaColumns('monitor');
$indexes = $datadict->MetaIndexes('monitor',false);

if(!isset($columns['ID'])) {
    $sql = $datadict->AddColumnSQL('monitor', "id I4 DEFAULT 0 NOTNULL PRIMARY");
    $datadict->ExecuteSQLArray($sql);
}

if(!isset($columns['NAME'])) {
    $sql = $datadict->AddColumnSQL('monitor', "name C(64) DEFAULT '' NOTNULL");
    $datadict->ExecuteSQLArray($sql);
}

if(!isset($columns['SECRET_KEY'])) {
    $sql = $datadict->AddColumnSQL('monitor', "secret_key C(64) DEFAULT '' NOTNULL");
    $datadict->ExecuteSQLArray($sql);
}

if(!isset($columns['LAST_UPDATED'])) {
    $sql = $datadict->AddColumnSQL('monitor', "last_updated I4 DEFAULT 0 NOTNULL");
    $datadict->ExecuteSQLArray($sql);
}

if(!isset($columns['MIA_SECS'])) {
    $sql = $datadict->AddColumnSQL('monitor', "mia_secs I4 DEFAULT 900 NOTNULL");
    $datadict->ExecuteSQLArray($sql);
}

return TRUE;
?>