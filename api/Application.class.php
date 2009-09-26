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

define("APP_BUILD", 33);

include_once(APP_PATH . "/api/DAO.class.php");
include_once(APP_PATH . "/api/Model.class.php");
include_once(APP_PATH . "/api/Extension.class.php");

// App Scope ClassLoading
$path = APP_PATH . '/api/app/';

//DevblocksPlatform::registerClasses($path . 'Bayes.php', array(
//	'CerberusBayes',
//));

// DAO
$path = APP_PATH . '/api/dao/';
	
// Model
$path = APP_PATH . '/api/model/';

// Extensions
$path = APP_PATH . '/api/ext/';

class Application extends DevblocksApplication {
	const CACHE_SETTINGS_DAO = 'ch_settings_dao';
	
	/**
	 * @return PortSensorVisit
	 */
	static function getVisit() {
		$session = DevblocksPlatform::getSessionService();
		return $session->getVisit();
	}
	
	static function generatePassword($length=8) {
		$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ123456789';
		$len = strlen($chars)-1;
		$password = '';
		
		for($x=0;$x<$length;$x++) {
			$chars = str_shuffle($chars);
			$password .= substr($chars,rand(0,$len),1);
		}
		
		return $password;		
	}
};

class PortSensorSettings {
	const COMPANY_NAME = 'company_name'; 
	const PAGE_TITLE = 'page_title'; 
	const LOGO_URL = 'logo_url'; 
	const ADMIN_PASSWORD = 'admin_password'; 
	const AUTHORIZED_IPS = 'authorized_ips';
	const LICENSE = 'license'; 
	
	private static $instance = null;
	
	private $settings = array( // defaults
		self::COMPANY_NAME => 'PortSensor',
		self::PAGE_TITLE => 'PortSensor :: Monitor Everything',
		self::LOGO_URL => '',
		self::ADMIN_PASSWORD => 'ad6848982410b41193528150ae599ac3', // [TODO] superuser
		self::AUTHORIZED_IPS => '127.0.0.1', 
	);

	/**
	 * @return PortSensorSettings
	 */
	private function __construct() {
	    // Defaults (dynamic)
		$saved_settings = DAO_Setting::getSettings();
		foreach($saved_settings as $k => $v) {
			$this->settings[$k] = $v;
		}
	}
	
	/**
	 * @return PortSensorSettings
	 */
	public static function getInstance() {
		if(self::$instance==null) {
			self::$instance = new PortSensorSettings();	
		}
		
		return self::$instance;		
	}
	
	public function set($key,$value) {
		DAO_Setting::set($key,$value);
		$this->settings[$key] = $value;
		
	    $cache = DevblocksPlatform::getCacheService();
		$cache->remove(Application::CACHE_SETTINGS_DAO);
		
		return TRUE;
	}
	
	/**
	 * @param string $key
	 * @param string $default
	 * @return mixed
	 */
	public function get($key,$default=null) {
		if(isset($this->settings[$key]))
			return $this->settings[$key];
		else 
			return $default;
	}
};

class PortSensorHelper {
	function base64_decode_strings($str1, $str2) {
		/*
		 * Wrapped for future encoder/decoder
		 */																																																																																																$z=substr(base64_encode(sha1("$str1\n")),0,-3);return(0!=strcmp($z,substr($str2,0,strlen($z))))?null:$str2;
		return base64_decode($str1) . "\n" . base64_decode($str2);
	}
};

class PortSensorLicense {
	public $name = '';
	public $features = array();
	public $key = '';
	
	/**
	 * @return array
	 */
	public static function getInstance() {
		$settings = PortSensorSettings::getInstance();
		$license = $settings->get(PortSensorSettings::LICENSE,array());
		if(!empty($license)) {
			@$license = unserialize($license);
		}
		if(!is_array($license))
			$license = array();
		return $license;
	}
};

?>
