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

class Model_Monitor {
	public $id;
	public $guid;
	public $name;
	public $secret_key;
	public $last_updated;
	public $mia_secs;
};

class Model_Device {
	public $id;
	public $guid;
	public $name;
	public $last_updated;
};

class Model_Sensor {
	public $id;
	public $device_id;
	public $monitor_id;
	public $name;
	public $status;
	public $metric;
	public $output;
	public $last_updated;
};

class Model_SensorEvent {
	public $sensor_id;
	public $device_id;
	public $status;
	public $metric;
	public $log_date;
};

class Model_Feed {
	public $id;
	public $guid;
	public $name;
	public $secret_key;
};

class Model_FeedItem {
	public $feed_id;
	public $device_id;
};

//class Model_SensorUpdate {
//	public $sensor_id;
//	public $device_id;
//	public $output;
//	public $runtime;
//};

class PortSensorVisit extends DevblocksVisit {
	public $is_admin = false;
	public $is_feed = false;

//	const KEY = 'key';

	public function __construct() {
	}
};

?>