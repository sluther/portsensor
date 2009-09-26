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

class PsCorePlugin extends DevblocksPlugin {
	function load(DevblocksPluginManifest $manifest) {
	}
};

class PsUpdateController extends DevblocksControllerExtension {
	function __construct($manifest) {
		parent::__construct($manifest);
		$router = DevblocksPlatform::getRoutingService();
		$router->addRoute('update','core.controller.update');
	}
	
	/*
	 * Request Overload
	 */
	function handleRequest(DevblocksHttpRequest $request) {
	    @set_time_limit(0); // no timelimit (when possible)
	    DevblocksPlatform::clearCache();
	    
	    $stack = $request->path;
	    array_shift($stack); // update

	    switch(array_shift($stack)) {
	    	case 'locked':
	    		if(!DevblocksPlatform::versionConsistencyCheck()) {
	    			$url = DevblocksPlatform::getUrlService();
	    			echo "<h1>PortSensor Portal</h1>";
	    			echo "The application is currently waiting for an administrator to finish upgrading. ".
	    				"Please wait a few minutes and then ". 
		    			sprintf("<a href='%s'>try again</a>.<br><br>",
							$url->write('c=update&a=locked')
		    			);
	    			echo sprintf("If you're an admin you may <a href='%s'>finish the upgrade</a>.",
	    				$url->write('c=update')
	    			);
	    		} else {
	    			DevblocksPlatform::redirect(new DevblocksHttpResponse(array('home')));
	    		}
	    		break;
	    		
	    	default:
			    $path = DEVBLOCKS_PATH . 'tmp' . DIRECTORY_SEPARATOR;
				$file = $path . 'psupdate_lock';	    		
				
				if(!file_exists($file) || @filectime($file)+600 < time()) { // 10 min lock
					touch($file);

//				    $settings = CerberusSettings::getInstance();
//				    $authorized_ips_str = $settings->get(CerberusSettings::AUTHORIZED_IPS);
//				    $authorized_ips = CerberusApplication::parseCrlfString($authorized_ips_str);
//				    
//			   	    $authorized_ip_defaults = CerberusApplication::parseCsvString(AUTHORIZED_IPS_DEFAULTS);
//				    $authorized_ips = array_merge($authorized_ips, $authorized_ip_defaults);
//				    
//				    $pass = false;
//					foreach ($authorized_ips as $ip)
//					{
//						if(substr($ip,0,strlen($ip)) == substr($_SERVER['REMOTE_ADDR'],0,strlen($ip)))
//					 	{ $pass=true; break; }
//					}
					$pass = true;
				    if(!$pass) {
					    echo 'Your IP address ('.$_SERVER['REMOTE_ADDR'].') is not authorized to update this helpdesk.';
					    return;
				    }

				    //echo "Running plugin patches...<br>";
				    if(DevblocksPlatform::runPluginPatches()) {
						@unlink($file);
				    	DevblocksPlatform::redirect(new DevblocksHttpResponse(array('home')));
				    } else {
						@unlink($file);
				    	echo "Failure!"; // [TODO] Needs elaboration
				    } 
				    break;
				}
				else {
					echo "Another administrator is currently running update.  Please wait...";
				}
	    }
	    
		exit;
	}
};

class PsPageController extends DevblocksControllerExtension {
    const ID = 'core.controller.page';
    
	function __construct($manifest) {
		parent::__construct($manifest);

		/*
		 * [JAS]: Read in the page extensions from the entire system and register 
		 * the URI shortcuts from their manifests with the router.
		 */
        $router = DevblocksPlatform::getRoutingService();
        $pages = DevblocksPlatform::getExtensions('app.page', false);
        
        foreach($pages as $manifest) { /* @var $manifest DevblocksExtensionManifest */
            $uri = $manifest->params['uri'];
            if(empty($uri)) continue;
            $router->addRoute($uri, self::ID);
        }
	}

	/**
	 * Enter description here...
	 *
	 * @param string $uri
	 * @return string $id
	 */
	private function _getPageIdByUri($uri) {
        $pages = DevblocksPlatform::getExtensions('app.page', false);
        foreach($pages as $manifest) { /* @var $manifest DevblocksExtensionManifest */
            if(0 == strcasecmp($uri,$manifest->params['uri'])) {
                return $manifest->id;
            }
        }
        return NULL;
	}
	
	public function handleRequest(DevblocksHttpRequest $request) {
	    $path = $request->path;
		$controller = array_shift($path);

        $pages = DevblocksPlatform::getExtensions('app.page', true);

        $page_id = $this->_getPageIdByUri($controller);
        @$page = $pages[$page_id];

        if(empty($page)) {
	        switch($controller) {
	        	default:
	        		return; // default page
	        		break;
	        }
        }

	    @$action = array_shift($path) . 'Action';

	    switch($action) {
	        case NULL:
	            // [TODO] Index/page render
	            break;
	            
	        default:
			    // Default action, call arg as a method suffixed with Action
				if(method_exists($page,$action)) {
					call_user_func(array(&$page, $action)); // [TODO] Pass HttpRequest as arg?
				}
	            break;
	    }
	}
	
	public function writeResponse(DevblocksHttpResponse $response) {
	    $path = $response->path;
		// [JAS]: Ajax? // [TODO] Explore outputting whitespace here for Safari
//	    if(empty($path))
//			return;

		$tpl = DevblocksPlatform::getTemplateService();
		$session = DevblocksPlatform::getSessionService();
		$translate = DevblocksPlatform::getTranslationService();
		$settings = PortSensorSettings::getInstance();
		$visit = $session->getVisit();
		
		$controller = array_shift($path);
		$pages = DevblocksPlatform::getExtensions('app.page', true);

		// Default page [TODO] This is supposed to come from framework.config.php
		if(empty($controller)) 
			$controller = 'home';

	    // [JAS]: Require us to always be logged in for pages
		if(empty($visit))
			$controller = 'login';

	    $page_id = $this->_getPageIdByUri($controller); /* @var $page PageExtension */
	    @$page = $pages[$page_id];
        
        if(empty($page)) return; // 404

		// [JAS]: Listeners (Step-by-step guided tour, etc.)
	    $listenerManifests = DevblocksPlatform::getExtensions('devblocks.listener.http');
	    foreach($listenerManifests as $listenerManifest) { /* @var $listenerManifest DevblocksExtensionManifest */
	         $inst = $listenerManifest->createInstance(); /* @var $inst DevblocksHttpRequestListenerExtension */
	         $inst->run($response, $tpl);
	    }
		
        // [JAS]: Variables provided to all page templates
		$tpl->assign('settings', $settings);
		$tpl->assign('session', $_SESSION);
		$tpl->assign('translate', $translate);
		$tpl->assign('visit', $visit);
		
		$tpl->assign('pages',$pages);		
		$tpl->assign('page',$page);

		$license = PortSensorLicense::getInstance();
		$tpl->assign('license', $license);
		
		$tpl->assign('response_uri', implode('/', $response->path));
		
		$tpl_path = dirname(__FILE__) . '/templates/';
		$tpl->assign('tpl_path', $tpl_path);

		// Timings
		$tpl->assign('render_time', (microtime(true) - DevblocksPlatform::getStartTime()));
		if(function_exists('memory_get_usage') && function_exists('memory_get_peak_usage')) {
			$tpl->assign('render_memory', memory_get_usage() - DevblocksPlatform::getStartMemory());
			$tpl->assign('render_peak_memory', memory_get_peak_usage() - DevblocksPlatform::getStartPeakMemory());
		}
		
		$tpl->display($tpl_path.'border.php');
	}
};

class PsPostController extends DevblocksControllerExtension {
	protected $_format = 'xml';
	protected $_payload = ''; 
	
	function __construct($manifest) {
		parent::__construct($manifest);
		$router = DevblocksPlatform::getRoutingService();
		$router->addRoute('post','core.controller.post');
	}
	
	function handleRequest(DevblocksHttpRequest $request) {
		$stack = $request->path;
		$db = DevblocksPlatform::getDatabaseService();
		
		// **** BEGIN AUTH
		@$verb = $_SERVER['REQUEST_METHOD'];
		@$header_date = $_SERVER['HTTP_DATE'];
		@$header_signature = $_SERVER['HTTP_PORTSENSOR_AUTH'];
		@$this->_payload = $this->_getRawPost();
		@list($auth_access_key,$auth_signature) = explode(":", $header_signature, 2);
		
		if(null == ($monitor = DAO_Monitor::getByGUID($auth_access_key))) {
			$this->_error(sprintf("Access denied! (Unknown monitor: %s)", $auth_access_key));
		}
		
		DAO_Monitor::update($monitor->id,array(
			DAO_Monitor::LAST_UPDATED => time()
		));

		@$auth_secret_key = $monitor->secret_key;

		$string_to_sign = "$verb\n$header_date\n$this->_payload\n$auth_secret_key\n";
		$compare_hash = base64_encode(sha1($string_to_sign, true));

		if(0 != strcmp($auth_signature,$compare_hash)) {
			$this->_error("Access denied! (Invalid signature)");
		}
			
			// Check that this IP is allowed to perform the VERB
//			if(!$stored_keychain->isValidIp($_SERVER['REMOTE_ADDR'])) {
//				$this->_error(sprintf("Access denied! (IP %s not authorized)",$_SERVER['REMOTE_ADDR']));				
//			}

		// **** END AUTH
		
		// Figure out our format by looking at the last path argument
		@list($command,$format) = explode('.', array_pop($stack));
		array_push($stack, $command);
		$this->_format = $format;
		
		$method = strtolower($verb) .'Action';
		
		if(method_exists($this,$method)) {
			call_user_func(array(&$this,$method),$stack,$monitor);
		}
		
		//************
		
	}
	
	private function postAction($stack, Model_Monitor $monitor) {
		switch(array_shift($stack)) {
			case 'post':
				$this->_postPostAction($stack, $monitor);
				break;
		}
	}
	
	private function _postPostAction($stack, Model_Monitor $monitor) {
		$xmlstr = $this->getPayload();
		$xml_in = simplexml_load_string($xmlstr);
		
		$devices = array();
		
		// Monitor
		if(empty($monitor)) {
			// Unknown monitor
			$this->_error(sprintf("Access denied! (Unknown monitor)"));
			return;
		}
		
		DAO_Monitor::update($monitor->id,array(
			DAO_Monitor::LAST_UPDATED => time()
		));

		// Load up all the sensors this monitor has provided in the past
		$monitor_stale_sensors = DAO_Sensor::getWhere(sprintf("%s = %d",
			DAO_Sensor::MONITOR_ID,
			$monitor->id
		));
		
		foreach($xml_in->xpath('/sensors/sensor') as $eSensor) {
			$sensor = new stdClass();
			$sensor->name=(string)$eSensor->name;
			$sensor->device=(string)$eSensor->device;
			$sensor->status = (integer)$eSensor->status;
			$sensor->metric=(string)$eSensor->metric;
			$sensor->output=(string)$eSensor->output;
			$sensor->runtime=(string)$eSensor->runtime;
			
			if(null == ($device = DAO_Device::getByGUID($sensor->device)))
				continue; // invalid device
			
			// New sensor instance
			if(null == ($lookup = DAO_Sensor::getWhere(
				sprintf("%s = '%s' AND %s = %d",
					DAO_Sensor::NAME,
					addslashes($sensor->name),
					DAO_Sensor::DEVICE_ID,
					$device->id
				)))) {
				
				$fields = array(
					DAO_Sensor::NAME => $sensor->name,
					DAO_Sensor::DEVICE_ID => $device->id,
					DAO_Sensor::STATUS => $sensor->status,
					DAO_Sensor::METRIC => $sensor->metric,
					DAO_Sensor::OUTPUT => $sensor->output,
					DAO_Sensor::LAST_UPDATED => $sensor->runtime,
					DAO_Sensor::MONITOR_ID => $monitor->id,
				);
				$sensor_id = DAO_Sensor::create($fields);
				
				DAO_SensorEvent::log($sensor_id, $device->id, $sensor->status, $sensor->metric);
					
			} else { // Updating an existing sensor
				$parent_sensor =  array_shift($lookup);
				
				// This updated sensor is not stale
				unset($monitor_stale_sensors[$parent_sensor->id]);
				
				$fields = array(
					DAO_Sensor::STATUS => $sensor->status,
					DAO_Sensor::METRIC => $sensor->metric,
					DAO_Sensor::OUTPUT => $sensor->output,
					DAO_Sensor::LAST_UPDATED => $sensor->runtime,
					DAO_Sensor::MONITOR_ID => $monitor->id,
				);
				DAO_Sensor::update($parent_sensor->id, $fields);
				
				if($parent_sensor->status != $sensor->status) {
					DAO_SensorEvent::log($parent_sensor->id, $device->id, $sensor->status, $sensor->metric);
				}
			}
		}
		
		// Purge any sensors no longer provided by this monitor
		// [TODO] We need to consider if any monitors won't always send a full update
		if(!empty($monitor_stale_sensors))
			DAO_Sensor::delete(array_keys($monitor_stale_sensors));
		
		$xml_out = new SimpleXMLElement("<success></success>");
		$this->_render($xml_out->asXML());
	}
	
	protected function _render($xml) {
		if('json' == $this->_format) {
			header("Content-type: text/javascript;");
			echo Zend_Json::fromXml($xml, true);
		} else {
			header("Content-type: text/xml;");
			echo $xml;
		}
		exit;
	}
	
	protected function _error($message) {
		$out_xml = new SimpleXMLElement('<error></error>');
		$out_xml->addChild('message', $message);
		$this->_render($out_xml->asXML());
	}
	
	function writeResponse(DevblocksHttpResponse $response) {
	}
	
	protected function getPayload() {
		return $this->_payload;
	}
	
	private function _getRawPost() {
		$contents = "";
		
		$putdata = fopen( "php://input" , "rb" ); 
		while(!feof( $putdata )) 
			$contents .= fread($putdata, 4096); 
		fclose($putdata);

		return $contents;
	}
};

class PsFeedController extends DevblocksControllerExtension {
	protected $_format = 'xml';
	protected $_payload = ''; 
	
	function __construct($manifest) {
		parent::__construct($manifest);
		$router = DevblocksPlatform::getRoutingService();
		$router->addRoute('feed','core.controller.feed');
	}
	
	function handleRequest(DevblocksHttpRequest $request) {
		$stack = $request->path;
		$db = DevblocksPlatform::getDatabaseService();
		
		array_shift($stack); // feed
		@$feed_guid = array_shift($stack);
		
		// **** BEGIN AUTH
		@$verb = $_SERVER['REQUEST_METHOD'];
		@$header_date = $_SERVER['HTTP_DATE'];
		@$header_signature = $_SERVER['HTTP_PORTSENSOR_AUTH'];
		@$this->_payload = $this->_getRawPost();
		@list($auth_access_key,$auth_signature) = explode(":", $header_signature, 2);
		
		if(null == ($feed = DAO_Feed::getByGUID($feed_guid))) {
			$this->_error(sprintf("Access denied! (Unknown feed: %s)", $feed_guid));
		}
		
		@$auth_secret_key = $feed->secret_key;
		
		// If no feed pass don't require auth header
		if(!empty($auth_secret_key)) {
			$string_to_sign = "$verb\n$header_date\n$this->_payload\n$auth_secret_key\n";
			$compare_hash = base64_encode(sha1($string_to_sign, true));
	
			if(0 != strcmp($auth_signature,$compare_hash)) {
				$this->_error("Access denied! (Invalid signature)");
			}
	
				// Check that this IP is allowed to perform the VERB
	//			if(!$stored_keychain->isValidIp($_SERVER['REMOTE_ADDR'])) {
	//				$this->_error(sprintf("Access denied! (IP %s not authorized)",$_SERVER['REMOTE_ADDR']));				
	//			}
		}

		// **** END AUTH
		
		// Figure out our format by looking at the last path argument
		@list($command,$format) = explode('.', array_pop($stack));
		array_push($stack, $command);
		$this->_format = $format;
		
		$method = strtolower($verb) .'Action';
		
		if(method_exists($this,$method)) {
			call_user_func(array(&$this,$method),$stack,$feed);
		}
		
		//************
	}
	
	protected function getAction($stack, Model_Feed $feed) {
		$this->_getFeedAction($feed);
	}
	
	private function _getFeedAction(Model_Feed $feed) {
		header("Content-Type: text/xml");
		$xml = new SimpleXMLElement("<virtual_groups></virtual_groups>");

		$feed_devices = DAO_FeedItem::getFeedDevices($feed->id);
		
		$eGroup =& $xml->addChild("group");
		$eGroup->addAttribute("id", "100");
		$eGroup->addChild("name", $feed->name);
		
		$eServers =& $eGroup->addChild("devices");

//		$devices = DAO_Device::getWhere();
		$monitors = DAO_Monitor::getWhere();
		
		foreach($feed_devices as $device) {
			$eServer =& $eServers->addChild("device");
			$eServer->addAttribute("id", $device->id);
			$eServer->addChild("name", $device->name);

			$eSensors =& $eServer->addChild("sensors");
			
			// [TODO] This should be more efficient (not 'n' queries)
			$sensors = DAO_Sensor::getWhere(sprintf("%s = %d",
				DAO_Sensor::DEVICE_ID,
				$device->id
			));
			
			foreach($sensors as $sensor) {
				if(null == (@$monitor = $monitors[$sensor->monitor_id])) {
					continue;
				}
				
				$eSensor =& $eSensors->addChild("sensor");
				$eSensor->addAttribute("id", $sensor->id);
				$eSensor->addChild("name",$sensor->name);
				
				if($monitor->mia_secs && (time() > $sensor->last_updated + $monitor->mia_secs)) {
					$eSensor->addChild("status",2);
					$eSensor->addChild("metric","M.I.A.");
					$eSensor->addChild("output","M.I.A.");
				}
				else {
					$eSensor->addChild("status",$sensor->status);
					$eSensor->addChild("metric",$sensor->metric);
					$eSensor->addChild("output",$sensor->output);
				}
				
				$eSensor->addChild("last_updated",$sensor->last_updated);
			}
		}
		
		echo $xml->asXML();
	}

	protected function _render($xml) {
		if('json' == $this->_format) {
			header("Content-type: text/javascript;");
			echo Zend_Json::fromXml($xml, true);
		} else {
			header("Content-type: text/xml;");
			echo $xml;
		}
		exit;
	}
	
	protected function _error($message) {
		$out_xml = new SimpleXMLElement('<error></error>');
		$out_xml->addChild('message', $message);
		$this->_render($out_xml->asXML());
	}
	
	function writeResponse(DevblocksHttpResponse $response) {
	}
	
	protected function getPayload() {
		return $this->_payload;
	}
	
	private function _getRawPost() {
		$contents = "";
		
		$putdata = fopen( "php://input" , "rb" ); 
		while(!feof( $putdata )) 
			$contents .= fread($putdata, 4096); 
		fclose($putdata);

		return $contents;
	}
	
};

class PsTranslations extends DevblocksTranslationsExtension {
	function __construct($manifest) {
		parent::__construct($manifest);	
	}
	
	function getTmxFile() {
		return dirname(__FILE__) . '/strings.xml';
	}
};

class PsConfigPage extends PageExtension {
	function __construct($manifest) {
		parent::__construct($manifest);

//		$path = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
//		
//		DevblocksPlatform::registerClasses($path. 'api/DAO.php', array(
//		    'DAO_Faq'
//		));
	}

	function isVisible() {
		// check login
		$visit = Application::getVisit();
		
		if(!empty($visit) && $visit->is_admin) {
			return true;
		} else {
			return false;
		}
	}
	
	function render() {
		$tpl = DevblocksPlatform::getTemplateService();
		$tpl->cache_lifetime = "0";
		$tpl->assign('path', dirname(__FILE__) . '/templates/');
		
		$session = DevblocksPlatform::getSessionService();
		$response = DevblocksPlatform::getHttpResponse();
		$license = PortSensorLicense::getInstance(); // Be nice!

		$stack = $response->path;

		@array_shift($stack); // config
		
		switch(array_shift($stack)) {
			case 'devices':
				$devices = DAO_Device::getWhere();
				$tpl->assign('devices', $devices);
				
				@$id = array_shift($stack);
				$tpl->assign('license', $license);
				
				if(0 < strlen($id)) {
					if(isset($devices[$id]))
						$tpl->assign('device', $devices[$id]);
						
					$tpl->display('file:' . dirname(__FILE__) . '/templates/config/devices/edit.tpl.php');
				} else {
					$tpl->display('file:' . dirname(__FILE__) . '/templates/config/devices/index.tpl.php');
				}
				
				break;

			case 'monitors':
				$monitors = DAO_Monitor::getWhere();
				$tpl->assign('monitors', $monitors);

				@$id = array_shift($stack);
				
				if(0 < strlen($id)) {
					if(isset($monitors[$id]))
						$tpl->assign('monitor', $monitors[$id]);
						
					$gen_secret_key = Application::generatePassword(20);
					$tpl->assign('gen_secret_key', $gen_secret_key);
						
					$tpl->display('file:' . dirname(__FILE__) . '/templates/config/monitors/edit.tpl.php');
				} else {
					$tpl->display('file:' . dirname(__FILE__) . '/templates/config/monitors/index.tpl.php');
				}
				
				break;

			case 'plugins':
				$tpl->display('file:' . dirname(__FILE__) . '/templates/config/plugins/index.tpl.php');
				break;
				
			case 'license':
				$tpl->assign('license', $license);
				$tpl->display('file:' . dirname(__FILE__) . '/templates/config/license/index.tpl.php');
				break;
				
			case 'feeds':
				$feeds = DAO_Feed::getWhere();
				$tpl->assign('feeds', $feeds);

				$devices = DAO_Device::getWhere();
				$tpl->assign('devices', $devices);
				
				@$id = array_shift($stack);
				
				if(0 < strlen($id)) {
					if(isset($feeds[$id]))
						$tpl->assign('feed', $feeds[$id]);
						
					$feed_devices = DAO_FeedItem::getFeedDevices($id);
					$tpl->assign('feed_devices', $feed_devices);
						
					$tpl->display('file:' . dirname(__FILE__) . '/templates/config/feeds/edit.tpl.php');
				} else {
					$tpl->display('file:' . dirname(__FILE__) . '/templates/config/feeds/index.tpl.php');
				}
				
				break;
				
			case 'general':
			default:
				$event_count = DAO_SensorEvent::getCount();
				$tpl->assign('event_count', $event_count);
				
				$tpl->display('file:' . dirname(__FILE__) . '/templates/config/general/index.tpl.php');
				break;
		}
	}
	
	function clearEventsAction() {
		DAO_SensorEvent::deleteAll();
		DevblocksPlatform::redirect(new DevblocksHttpResponse(array('config','general')));
	}
	
	function saveLicenseAction() {
		@$key = DevblocksPlatform::importGPC($_POST['key'],'string','');
		
		if(empty($key)) {
			DevblocksPlatform::setHttpResponse(new DevblocksHttpResponse(array('config','license','empty')));
			return;
		}
		
		// Clean off the wrapper
		@$lines = explode("\r\n", trim($key));
		$company = '';
		$features = array();
		$key = '';
		$valid=0;
		if(is_array($lines))
		foreach($lines as $line) {
			if(0==strcmp(substr($line,0,3),'---')) {
				$valid++;continue;
			}
			if(preg_match("/^(.*?)\: (.*?)$/",$line,$matches)) {
				if(0==strcmp($matches[1],"Company"))
					$company = $matches[2];
				if(0==strcmp($matches[1],"Feature"))
					$features[$matches[2]] = true;
			} else {
				$key .= trim($line);
			}
		}
		
		if(2!=$valid || 0!=$key%4) {
			DevblocksPlatform::setHttpResponse(new DevblocksHttpResponse(array('config','license','invalid')));
			return;
		}
		
		// Save for reuse in form in case we need to redraw on error
		$settings = PortSensorSettings::getInstance();
//		$settings->set('company', trim($company));
		
		ksort($features);
		
		/*
		 * [IMPORTANT -- Yes, this is simply a line in the sand.]
		 * You're welcome to modify the code to meet your needs, but please respect 
		 * our licensing.  Buy a legitimate copy to help support the project!
		 * http://www.portsensor.com/
		 */
		$license = PortSensorLicense::getInstance();
		// $license['name'] = CerberusHelper::strip_magic_quotes($company,'string');
		$license['name'] = $company;
		$license['features'] = $features;
		$license['key'] = PortSensorHelper::base64_decode_strings($company, $key);
		
		$settings->set(PortSensorSettings::LICENSE, serialize($license));		
		
		DevblocksPlatform::redirect(new DevblocksHttpResponse(array('config','license')));
	}
	
	function saveGeneralAction() {
		@$company_name = DevblocksPlatform::importGPC($_POST['company_name'],'string','');
		@$logo_url = DevblocksPlatform::importGPC($_POST['logo_url'],'string','');
		@$password = DevblocksPlatform::importGPC($_POST['password'],'string','');
		@$password2 = DevblocksPlatform::importGPC($_POST['password2'],'string','');

		$settings = PortSensorSettings::getInstance();
		
		$settings->set(PortSensorSettings::COMPANY_NAME, $company_name);
		$settings->set(PortSensorSettings::LOGO_URL, $logo_url);
		
		// [TODO] Check if passwords don't match (report error on DIV)
		if(!empty($password)) {
			$settings->set(PortSensorSettings::ADMIN_PASSWORD, md5(md5($password)));
		}
		
		DevblocksPlatform::redirect(new DevblocksHttpResponse(array('config','general')));
	}
	
	function saveDeviceAction() {
		@$id = DevblocksPlatform::importGPC($_REQUEST['id'],'integer','');
		@$device_name = DevblocksPlatform::importGPC($_REQUEST['device_name'],'string','');
		@$device_guid = DevblocksPlatform::importGPC($_REQUEST['device_guid'],'string','');
		@$delete = DevblocksPlatform::importGPC($_REQUEST['do_delete'],'integer',0);
		
		$device_guid = str_replace(' ','_',strtolower($device_guid));
		
		// Required args
		if(empty($device_name))
			$device_name = "(no device name)";
		
		if(!empty($id)) { // update
			if($delete) {
				DAO_Device::delete($id);
				
			} else {
				DAO_Device::update($id, array(
					DAO_Device::NAME => $device_name,
					DAO_Device::GUID => $device_guid,
				));
			}

		} else { // insert
			$device_id = DAO_Device::create(array(
				DAO_Device::NAME => $device_name,
				DAO_Device::GUID => $device_guid,
				DAO_Device::LAST_UPDATED => time(),
			));
		}
		
		DevblocksPlatform::redirect(new DevblocksHttpResponse(array('config','devices')));
	}
	
	function saveMonitorAction() {
		@$id = DevblocksPlatform::importGPC($_REQUEST['id'],'integer','');
		@$monitor_name = DevblocksPlatform::importGPC($_REQUEST['monitor_name'],'string','');
		@$monitor_guid = DevblocksPlatform::importGPC($_REQUEST['monitor_guid'],'string','');
		@$monitor_secret_key = DevblocksPlatform::importGPC($_REQUEST['monitor_secret_key'],'string','');
		@$monitor_mia_secs = DevblocksPlatform::importGPC($_REQUEST['monitor_mia'],'integer',0);
		@$delete = DevblocksPlatform::importGPC($_REQUEST['do_delete'],'integer',0);
		
		$monitor_guid = str_replace(' ','_',strtolower($monitor_guid));
		
		// Required args
		if(empty($monitor_name))
			$monitor_name = '(no monitor name)';

		if(empty($monitor_secret_key))
			$monitor_secret_key = Application::generatePassword(20);
			
		if(!empty($id)) { // update
			if($delete) {
				DAO_Monitor::delete($id);
				
			} else {
				DAO_Monitor::update($id, array(
					DAO_Monitor::NAME => $monitor_name,
					DAO_Monitor::GUID => $monitor_guid,
					DAO_Monitor::SECRET_KEY => $monitor_secret_key,
					DAO_Monitor::MIA_SECS => $monitor_mia_secs,
				));
			}

		} else { // insert
			$monitor_id = DAO_Monitor::create(array(
				DAO_Monitor::NAME => $monitor_name,
				DAO_Monitor::GUID => $monitor_guid,
				DAO_Monitor::SECRET_KEY => $monitor_secret_key,
				DAO_Monitor::MIA_SECS => $monitor_mia_secs,
				DAO_Monitor::LAST_UPDATED => time(),
			));
		}
		
		DevblocksPlatform::redirect(new DevblocksHttpResponse(array('config','monitors')));
	}
	
	function saveFeedAction() {
		@$id = DevblocksPlatform::importGPC($_REQUEST['id'],'integer','');
		@$feed_name = DevblocksPlatform::importGPC($_REQUEST['feed_name'],'string','');
		@$feed_guid = DevblocksPlatform::importGPC($_REQUEST['feed_guid'],'string','');
		@$feed_secret_key = DevblocksPlatform::importGPC($_REQUEST['feed_secret_key'],'string','');
		@$devices = DevblocksPlatform::importGPC($_REQUEST['devices'],'array',array());
		@$delete = DevblocksPlatform::importGPC($_REQUEST['do_delete'],'integer',0);
		
		$feed_guid = str_replace(' ','_',strtolower($feed_guid));
		
		// Required args
		if(empty($feed_name))
			$feed_name = '(no feed name)';
		
		if(!empty($id) && $delete) {
			DAO_Feed::delete($id);
			
		} else {
			if(!empty($id)) { // update
				DAO_Feed::update($id, array(
					DAO_Feed::NAME => $feed_name,
					DAO_Feed::GUID => $feed_guid,
					DAO_Feed::SECRET_KEY => $feed_secret_key,
				));
	
			} else { // insert
				$id = DAO_Feed::create(array(
					DAO_Feed::NAME => $feed_name,
					DAO_Feed::GUID => $feed_guid,
					DAO_Feed::SECRET_KEY => $feed_secret_key,
	//				DAO_Feed::LAST_UPDATED => time(),
				));
			}
			
			if(!empty($id) && !empty($devices))
				DAO_FeedItem::setFeedDevices($id, $devices);
		}
		
		DevblocksPlatform::redirect(new DevblocksHttpResponse(array('config','feeds')));
	}
	
};

class PsHomePage extends PageExtension {
	function __construct($manifest) {
		parent::__construct($manifest);

//		$path = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
//		
//		DevblocksPlatform::registerClasses($path. 'api/DAO.php', array(
//		    'DAO_Faq'
//		));
	}

	function isVisible() {
		// check login
		$visit = Application::getVisit();
		
		if(empty($visit)) {
			return false;
		} else {
			return true;
		}
	}
	
	function render() {
		$tpl = DevblocksPlatform::getTemplateService();
		$tpl->cache_lifetime = "0";
		$tpl->assign('path', dirname(__FILE__) . '/templates/');
		
		$session = DevblocksPlatform::getSessionService();
		$visit = $session->getVisit(); /* @var $visit PortSensorVisit */
		$response = DevblocksPlatform::getHttpResponse();

		$stack = $response->path;

		@array_shift($stack); // home
		
		switch(array_shift($stack)) {
			default:
				$tpl->assign('now_secs', time());

				if($visit->is_feed) {
					$devices = DAO_FeedItem::getFeedDevices($visit->is_feed->id);
					$tpl->assign('devices', $devices);
					
				} elseif($visit->is_admin) {
					$devices = DAO_Device::getWhere();
					$tpl->assign('devices', $devices);
				}
				
				$sensors = DAO_Sensor::getWhere();
				$sensors_by_device = array();
				
				$total_ok = 0;
				$total_warning = 0;
				$total_critical = 0;
				
				foreach($sensors as $sensor) {
					if(!isset($devices[$sensor->device_id]))
						continue;
					
					if(!isset($sensors_by_device[$sensor->device_id]))
						$sensors_by_device[$sensor->device_id] = array();
						
					if(1==$sensor->status) {
						$total_warning++;
					} elseif(2==$sensor->status) {
						$total_critical++;
					} else {
						$total_ok++;
					}
						
					$sensors_by_device[$sensor->device_id][$sensor->id] = $sensor;
				}
				$tpl->assign('sensors_by_device', $sensors_by_device);
				
				$tpl->assign('total_ok', $total_ok);
				$tpl->assign('total_warning', $total_warning);
				$tpl->assign('total_critical', $total_critical);
				
				$tpl->display('file:' . dirname(__FILE__) . '/templates/home/index.tpl.php');
				break;
		}
	}
};

class PsEventsPage extends PageExtension {
	function __construct($manifest) {
		parent::__construct($manifest);

//		DevblocksPlatform::registerClasses($path. 'api/DAO.php', array(
//		    'DAO_Faq'
//		));
	}

	function isVisible() {
		// check login
		$visit = Application::getVisit();
		
		if(empty($visit)) {
			return false;
		} else {
			return true;
		}
	}
	
	function render() {
		$path = dirname(__FILE__);
		
		$tpl = DevblocksPlatform::getTemplateService();
		$tpl->cache_lifetime = "0";
		$tpl->assign('path', $path . '/templates/');
		
		$visit = Application::getVisit(); /* @var $visit PortSensorVisit */
		$response = DevblocksPlatform::getHttpResponse();

		$stack = $response->path;

		@array_shift($stack); // events
		@$id = array_shift($stack); // id
		
		if(empty($id)) {
			$tpl->assign('now_secs', time());
			
			// Devices
			if(!$visit->is_admin && !empty($visit->is_feed)) {
				$devices = DAO_FeedItem::getFeedDevices($visit->is_feed->id);
				$tpl->assign('devices', $devices);
				
				// Events
				$plots = DAO_SensorEvent::getWhere(sprintf("%s IN (%s) ",
					"se.device_id", // [TODO]: Eww
					implode(',', array_keys($devices))
				));
				$tpl->assign('plots', $plots);
				
			} elseif($visit->is_admin) {
				$devices = DAO_Device::getWhere();
				$tpl->assign('devices', $devices);
				
				// Events
				$plots = DAO_SensorEvent::getWhere();
				$tpl->assign('plots', $plots);
				
			}
			
			$tpl->display('file:' . dirname(__FILE__) . '/templates/events/index.tpl.php');
			
		} else {
			$db = DevblocksPlatform::getDatabaseService();
			$tpl->assign('now_secs', time());
			
			$sensor = DAO_Sensor::get($id);
			$tpl->assign('sensor', $sensor);
			
			$device = DAO_Device::getByID($sensor->device_id);
			$tpl->assign('device', $device);
			
			// Security check for device for current logged in user
			if(!$visit->is_admin && !empty($visit->is_feed)) {
				$feed_devices = DAO_FeedItem::getFeedDevices($visit->is_feed->id);
				if(!isset($feed_devices[$sensor->device_id])) {
					return;				
				}
			}
			
			$events = DAO_SensorEvent::getWhere(sprintf("%s = %d",
				DAO_SensorEvent::SENSOR_ID,
				$sensor->id
			));
			$tpl->assign('plots', $events);
			
			$tpl->display('file:' . dirname(__FILE__) . '/templates/events/sensor.tpl.php');
		}
		
	}
};
	
class PsLoginPage extends PageExtension {
	function __construct($manifest) {
		parent::__construct($manifest);

//		$path = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
//		
//		DevblocksPlatform::registerClasses($path. 'api/DAO.php', array(
//		    'DAO_Faq'
//		));
	}

	function isVisible() {
		return true;
	}
	
	function render() {
		$tpl = DevblocksPlatform::getTemplateService();
		$tpl->cache_lifetime = "0";
		$tpl->assign('path', dirname(__FILE__) . '/templates/');
		
		$session = DevblocksPlatform::getSessionService();
		$response = DevblocksPlatform::getHttpResponse();

		$stack = $response->path;

		@array_shift($stack); // home
		
		switch(array_shift($stack)) {
			default:
				$tpl->display('file:' . dirname(__FILE__) . '/templates/login/index.tpl.php');
				break;
		}
	}
	
	function signoutAction() {
		$session = DevblocksPlatform::getSessionService();
		$session->setVisit(null);
		DevblocksPlatform::redirect(new DevblocksHttpResponse(array('login')));
	}
	
	function doLoginAction() {
		@$login = DevblocksPlatform::importGPC($_POST['login'], 'string', '');
		@$password = DevblocksPlatform::importGPC($_POST['password'], 'string', '');

		$settings = PortSensorSettings::getInstance();
		$session = DevblocksPlatform::getSessionService();
		
		if(0 == strcasecmp('admin',$login)) {
			$admin_pass = $settings->get(PortSensorSettings::ADMIN_PASSWORD, null);
			
			if(0 == strcmp(md5(md5($password)),$admin_pass)) {
				$visit = new PortSensorVisit();
				$visit->is_admin = true;
				$visit->is_feed = false;
				$session->setVisit($visit);
				DevblocksPlatform::redirect(new DevblocksHttpResponse(array('home')));
				return;
			}
			
		// Feed Logins
		} elseif (null != ($feed = DAO_Feed::getByGUID($login))) {
			if(0 == strcmp($password,$feed->secret_key)) {
				$visit = new PortSensorVisit();
				$visit->is_admin = false;
				$visit->is_feed = $feed;
				$session->setVisit($visit);
				DevblocksPlatform::redirect(new DevblocksHttpResponse(array('home')));
				return;
			}
		}
		
		DevblocksPlatform::redirect(new DevblocksHttpResponse(array('login')));
		return;
	}
};

?>