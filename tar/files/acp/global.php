<?php

// define paths
define('RELATIVE_WWW_DIR', '../');

//initialize package array
$packageDirs = array();

//include config
require_once(dirname(dirname(__FILE__)).'/config.inc.php');

//include WCF
require_once(RELATIVE_WCF_DIR.'global.php');
if(!count($packageDirs)) $packageDirs[] = WWW_DIR;
$packageDirs[] = WCF_DIR;

// starting acp
require_once(WWW_DIR.'lib/system/WWWACP.class.php');
new WWWACP();
?>
