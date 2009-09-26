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

require(getcwd() . '/framework.config.php');
require(DEVBLOCKS_PATH . 'Devblocks.class.php');
require(APP_PATH . '/api/Application.class.php');

//@$uri = DevblocksPlatform::importGPC($_REQUEST['c']); // extension
//@$listener = DevblocksPlatform::importGPC($_REQUEST['a']); // listener
$request = DevblocksPlatform::readRequest();
//$request = new DevblocksHttpRequest(array($uri,$listener));

// [JAS]: [TODO] Is an explicit init() really required?
DevblocksPlatform::init();

$session = DevblocksPlatform::getSessionService();
//$settings = CerberusSettings::getInstance();
//$worker = CerberusApplication::getActiveWorker();

$tpl = DevblocksPlatform::getTemplateService();
$tpl->assign('translate', DevblocksPlatform::getTranslationService());
$tpl->assign('session', $_SESSION);
$tpl->assign('visit', $session->getVisit());
//$tpl->assign('active_worker', $worker);
//$tpl->assign('settings', $settings);

//if(!empty($worker)) {
//	$active_worker_memberships = $worker->getMemberships();
//	$tpl->assign('active_worker_memberships', $active_worker_memberships);
//}

DevblocksPlatform::processRequest($request,true);

exit;
?>
