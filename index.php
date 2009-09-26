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

if(version_compare(PHP_VERSION, "5.1.2", "<"))
	die("This project requires PHP 5.1.2 or later.");

require(getcwd() . '/framework.config.php');
require(DEVBLOCKS_PATH . 'Devblocks.class.php');

// If this is our first run, redirect to the installer
if('' == APP_DB_DRIVER 
	|| '' == APP_DB_HOST 
	|| '' == APP_DB_DATABASE 
	|| null == ($db = DevblocksPlatform::getDatabaseService())
	|| DevblocksPlatform::isDatabaseEmpty()) {
   		header('Location: '.dirname($_SERVER['PHP_SELF']).'/install/index.php'); // [TODO] change this to a meta redirect
   		exit;
	}

// [TODO] We could also put a temporary lock mode for upgrades here

require(APP_PATH . '/api/Application.class.php');

// [JAS]: [TODO] Is an explicit init() really required?  No anonymous static blocks?
DevblocksPlatform::init();

// Request
$request = DevblocksPlatform::readRequest();

// Patches (if not on the patch page)
if(@0 != strcasecmp(@$request->path[0],"update")
	&& !DevblocksPlatform::versionConsistencyCheck())
	DevblocksPlatform::redirect(new DevblocksHttpResponse(array('update','locked')));

//DevblocksPlatform::readPlugins();
$session = DevblocksPlatform::getSessionService();

// [JAS]: HTTP Request
DevblocksPlatform::processRequest($request);

exit;
?>