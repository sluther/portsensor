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

class DAO_WebapiKey extends DevblocksORMHelper {
	const ID = 'id';
	const NICKNAME = 'nickname';
	const ACCESS_KEY = 'access_key';
	const SECRET_KEY = 'secret_key';
	const RIGHTS = 'rights';

	static function create($fields) {
		$db = DevblocksPlatform::getDatabaseService();
		
		$id = $db->GenID('generic_seq');
		
		$sql = sprintf("INSERT INTO webapi_key (id) ".
			"VALUES (%d)",
			$id
		);
		$db->Execute($sql);
		
		self::update($id, $fields);
		
		return $id;
	}
	
	static function update($ids, $fields) {
		parent::_update($ids, 'webapi_key', $fields);
	}
	
	/**
	 * @param string $where
	 * @return Model_WebapiKey[]
	 */
	static function getWhere($where=null) {
		$db = DevblocksPlatform::getDatabaseService();
		
		$sql = "SELECT id, nickname, access_key, secret_key, rights ".
			"FROM webapi_key ".
			(!empty($where) ? sprintf("WHERE %s ",$where) : "").
			"ORDER BY nickname asc";
		$rs = $db->Execute($sql);
		
		return self::_getObjectsFromResult($rs);
	}

	/**
	 * @param integer $id
	 * @return Model_WebapiKey	 */
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
	 * @return Model_WebapiKey[]
	 */
	static private function _getObjectsFromResult($rs) {
		$objects = array();
		
		while(!$rs->EOF) {
			$object = new Model_WebapiKey();
			$object->id = intval($rs->fields['id']);
			$object->nickname = $rs->fields['nickname'];
			$object->access_key = $rs->fields['access_key'];
			$object->secret_key = $rs->fields['secret_key'];
			$rights = $rs->fields['rights'];
			
			if(!empty($rights)) {
				@$object->rights = unserialize($rights);
			}
			
			$objects[$object->id] = $object;
			$rs->MoveNext();
		}
		
		return $objects;
	}
	
	static function delete($ids) {
		if(!is_array($ids)) $ids = array($ids);
		$db = DevblocksPlatform::getDatabaseService();
		
		$ids_list = implode(',', $ids);
		
		$db->Execute(sprintf("DELETE FROM webapi_key WHERE id IN (%s)",$ids_list));
	}
};

class Model_WebapiKey {
	const ACL_NONE = 0;
	const ACL_READONLY = 1;
	const ACL_FULL = 2;
	
	public $id;
	public $nickname;
	public $access_key;
	public $secret_key;
	public $rights;
	
	public function isValidIp($ip) {
		@$valid_ips = $this->rights['ips'];
		if(!is_array($valid_ips) || empty($valid_ips))
			return true;
		
		foreach($valid_ips as $valid_ip) {
			if(substr($ip,0,strlen($valid_ip))==$valid_ip)
				return true;
		}
		
		return false;
	}
};

class PsRestFrontController extends DevblocksControllerExtension {
	function __construct($manifest) {
		parent::__construct($manifest);
		$router = DevblocksPlatform::getRoutingService();
		$router->addRoute('webapi','core.controller.rest');
	}
	
	function handleRequest(DevblocksHttpRequest $request) {
		$controllers = array(
			'devices' => 'Ps_DevicesRestController',
			'monitors' => 'Ps_MonitorsRestController',
			'sensors' => 'Ps_SensorsRestController',
		);

		$stack = $request->path;
		array_shift($stack); // webapi
		
		@$controller = array_shift($stack);

		if(isset($controllers[$controller])) {
			$inst = new $controllers[$controller]();
			$inst->handleRequest(new DevblocksHttpRequest($stack));
		}
	}
	
	function writeResponse(DevblocksHttpResponse $response) {
	}
};

abstract class Ps_RestController implements DevblocksHttpRequestHandler {
	protected $_format = 'xml';
	
	function handleRequest(DevblocksHttpRequest $request) {
		$stack = $request->path;
		
		// Figure out our format by looking at the last path argument
		@list($command,$format) = explode('.', array_pop($stack));
		array_push($stack, $command);
		$this->_format = $format;
		
		$request_method = strtolower($_SERVER['REQUEST_METHOD']);

		$method = '_'.$request_method .'Action';
		
		if(method_exists($this,$method)) {
			call_user_method($method,$this,$stack);
		}
	}
	
	protected function _render($xml) {
		if('json' == $this->_format) {
			header("Content-type: text/javascript");
			echo Zend_Json::fromXml($xml, true);
		} else {
			header("Content-type: text/xml");
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
	
	protected function getRawPost() {
		$contents = "";
		
		$putdata = fopen( "php://input" , "rb" ); 
		while(!feof( $putdata )) 
			$contents .= fread($putdata, 4096); 
		fclose($putdata);

		return $contents;
	}
};

class Ps_EntityRestController extends Ps_RestController {
	protected function _getAction($path) {
		// Single GET
		if(1==count($path) && is_numeric($path[0]))
			$this->_getIdAction($path);
		
		// Actions
		switch(array_shift($path)) {
			case 'list':
				$this->_getListAction($path);
				break;
		}
	}

	protected function _putAction($path) {
		// Single PUT
		if(1==count($path) && is_numeric($path[0]))
			$this->_putIdAction($path);
	}
	
	protected function _postAction($path) {
		// Actions
		switch(array_shift($path)) {
			case 'create':
				$this->_postCreateAction($path);
				break;
		}
	}
	
	protected function _deleteAction($path) {
		// Single DELETE
		if(1==count($path) && is_numeric($path[0]))
			$this->_deleteIdAction($path);
	}
	
	protected function _postCreateAction($path) {}
	protected function _getIdAction($path) {}
	protected function _getListAction($path) {}
	protected function _putIdAction($path) {}
	protected function _deleteIdAction($path) {}
};

class Ps_DevicesRestController extends Ps_EntityRestController {
	protected function _postCreateAction($path) {
		$xmlstr = $this->getRawPost();
		$xml_in = simplexml_load_string($xmlstr);
		
		@$in_guid = (string) $xml_in->guid; 
		@$in_name = (string) $xml_in->name; 
		
		if(empty($in_guid) || empty($in_name))
			$this->_error("All required fields were not provided.");
		
		if(null != ($device = DAO_Device::getByGUID($in_guid)))
			$this->_error("GUID already exists.");
		
		$id = DAO_Device::create(array(
			DAO_Device::GUID => $in_guid,
			DAO_Device::NAME => $in_name,
			DAO_Device::LAST_UPDATED => time(),
		));
		
		// Render the new device
		$this->_getAction(array($id));
	}
	
	protected function _getIdAction($path) {
		$in_id = array_shift($path);
		
		if(empty($in_id))
			$this->_error("ID was not provided.");
			
		if(null == ($device = DAO_Device::getByID($in_id)))
			$this->_error("ID not valid.");
		
		$xml_out = new SimpleXMLElement("<device></device>");
		$xml_out->addChild('id', $device->id);
		$xml_out->addChild('name', $device->name);
		$xml_out->addChild('guid', $device->guid);
		$xml_out->addChild('last_updated', $device->last_updated);
		
		$this->_render($xml_out->asXML());
	}
	
	protected function _getListAction($path) {
		$devices = DAO_Device::getWhere();
		$xml = new SimpleXMLElement("<devices></devices>"); 
		
		foreach($devices as $device) { /* @var $device Model_Device */
			$eDevice =& $xml->addChild('device');
			$eDevice->addChild('id', $device->id);
			$eDevice->addChild('name', $device->name);
			$eDevice->addChild('guid', $device->guid);
			$eDevice->addChild('last_updated', $device->last_updated);
		}

		$this->_render($xml->asXML());
	}
	
	protected function _putIdAction($path) {
		$in_id = array_shift($path);
		
		if(empty($in_id))
			$this->_error("ID was not provided.");
			
		if(null == ($device = DAO_Device::getByID($in_id)))
			$this->_error("ID not valid.");
		
		$fields = array();
		
		$xmlstr = $this->getRawPost();
		$xml_in = new SimpleXMLElement($xmlstr);
		
		@$in_name = (string) $xml_in->name;
		if(!empty($in_name))
			$fields[DAO_Device::NAME] = $in_name;
			
		@$in_guid = (string) $xml_in->guid;
		if(!empty($in_guid))
			$fields[DAO_Device::GUID] = $in_guid;
		
		if(!empty($fields))
			DAO_Device::update($device->id,$fields);

		$this->_getIdAction(array($device->id));
	}
	
	protected function _deleteIdAction($path) {
		$in_id = array_shift($path);

		if(empty($in_id))
			$this->_error("ID was not provided.");
			
		if(null == ($device = DAO_Device::getByID($in_id)))
			$this->_error("ID is not valid.");
		
		DAO_Device::delete($device->id);
		
		$out_xml = new SimpleXMLElement('<success></success>');
		$this->_render($out_xml->asXML());
	}
};

class Ps_MonitorsRestController extends Ps_EntityRestController {
	protected function _postCreateAction($path) {
		$xmlstr = $this->getRawPost();
		$xml_in = simplexml_load_string($xmlstr);
		
		@$in_guid = (string) $xml_in->guid; 
		@$in_name = (string) $xml_in->name; 
		@$in_mia_secs = (string) $xml_in->mia_secs; 
		
		if(empty($in_guid) || empty($in_name))
			$this->_error("All required fields were not provided.");
		
		if(null != ($device = DAO_Monitor::getByGUID($in_guid)))
			$this->_error("GUID already exists.");
		
		$fields = array();
		$fields[DAO_Monitor::LAST_UPDATED] = time();
		
		if(!empty($in_guid))
			$fields[DAO_Monitor::GUID] = $in_guid;
		if(!empty($in_name))
			$fields[DAO_Monitor::NAME] = $in_name;
		if(!empty($in_mia_secs))
			$fields[DAO_Monitor::MIA_SECS] = intval($in_mia_secs);
			
		$id = DAO_Monitor::create($fields);
		
		// Render the new entity
		$this->_getAction(array($id));
	}
	
	protected function _getIdAction($path) {
		$in_id = array_shift($path);
		
		if(empty($in_id))
			$this->_error("ID was not provided.");
			
		if(null == ($monitor = DAO_Monitor::getByID($in_id)))
			$this->_error("ID not valid.");
		
		$xml_out = new SimpleXMLElement("<monitor></monitor>");
		$xml_out->addChild('id', $monitor->id);
		$xml_out->addChild('name', $monitor->name);
		$xml_out->addChild('guid', $monitor->guid);
		$xml_out->addChild('last_updated', $monitor->last_updated);
		$xml_out->addChild('mia_secs', $monitor->mia_secs);
		
		$this->_render($xml_out->asXML());
	}
	
	protected function _getListAction($path) {
		$monitors = DAO_Monitor::getWhere();
		$xml = new SimpleXMLElement("<monitors></monitors>"); 
		
		foreach($monitors as $monitor) { /* @var $$monitor Model_Monitor */
			$eMonitor =& $xml->addChild('monitor');
			$eMonitor->addChild('id', $monitor->id);
			$eMonitor->addChild('name', $monitor->name);
			$eMonitor->addChild('guid', $monitor->guid);
			$eMonitor->addChild('last_updated', $monitor->last_updated);
			$eMonitor->addChild('mia_secs', $monitor->mia_secs);
		}

		$this->_render($xml->asXML());
	}
	
	protected function _putIdAction($path) {
		$in_id = array_shift($path);
		
		if(empty($in_id))
			$this->_error("ID was not provided.");
			
		if(null == ($monitor = DAO_Monitor::getByID($in_id)))
			$this->_error("ID not valid.");
		
		$fields = array();
		
		$xmlstr = $this->getRawPost();
		$xml_in = new SimpleXMLElement($xmlstr);
		
		@$in_name = (string) $xml_in->name;
		if(!empty($in_name))
			$fields[DAO_Monitor::NAME] = $in_name;
			
		@$in_guid = (string) $xml_in->guid;
		if(!empty($in_guid))
			$fields[DAO_Monitor::GUID] = $in_guid;
		
		@$in_mia_secs = (string) $xml_in->mia_secs;
		if(!empty($in_mia_secs))
			$fields[DAO_Monitor::MIA_SECS] = intval($in_mia_secs);
		
		if(!empty($fields))
			DAO_Monitor::update($monitor->id,$fields);

		$this->_getIdAction(array($monitor->id));
	}
	
	protected function _deleteIdAction($path) {
		$in_id = array_shift($path);

		if(empty($in_id))
			$this->_error("ID was not provided.");
			
		if(null == ($monitor = DAO_Monitor::getByID($in_id)))
			$this->_error("ID is not valid.");
		
		DAO_Monitor::delete($monitor->id);
		
		$out_xml = new SimpleXMLElement('<success></success>');
		$this->_render($out_xml->asXML());
	}
};

class Ps_SensorsRestController extends Ps_EntityRestController {
	protected function _getIdAction($path) {
		$in_id = array_shift($path);
		
		if(empty($in_id))
			$this->_error("ID was not provided.");
			
		if(null == ($sensor = DAO_Sensor::get($in_id)))
			$this->_error("ID not valid.");
		
		$xml_out = new SimpleXMLElement("<sensor></sensor>");
		$xml_out->addChild('id', $sensor->id);
		$xml_out->addChild('device_id', $sensor->device_id);
		$xml_out->addChild('monitor_id', $sensor->monitor_id);
		$xml_out->addChild('status', $sensor->status);
		$xml_out->addChild('output', $sensor->output);
		$xml_out->addChild('metric', $sensor->metric);
		$xml_out->addChild('last_updated', $sensor->last_updated);
		
		$this->_render($xml_out->asXML());
	}
	
	protected function _getListAction($path) {
		$sensors = DAO_Sensor::getWhere();
		$xml = new SimpleXMLElement("<sensors></sensors>"); 
		
		foreach($sensors as $sensor) { /* @var $device Model_Sensor */
			$eSensor =& $xml->addChild('sensor');
			$eSensor->addChild('id', $sensor->id);
			$eSensor->addChild('device_id', $sensor->device_id);
			$eSensor->addChild('monitor_id', $sensor->monitor_id);
			$eSensor->addChild('status', $sensor->status);
			$eSensor->addChild('output', $sensor->output);
			$eSensor->addChild('metric', $sensor->metric);
			$eSensor->addChild('last_updated', $sensor->last_updated);
		}

		$this->_render($xml->asXML());
	}
	
};

?>