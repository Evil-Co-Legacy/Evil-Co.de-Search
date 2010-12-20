<?php
/**
 * Reads the contents of all package srvers
 * Note: This script is not wcf conform ... ;-)
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */

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

//starting application
require_once(WWW_DIR.'lib/system/WWWCore.class.php');
new WWWCore();

// start cron handler
require_once(WWW_DIR.'lib/system/cron/WWWUpdateCronManager.class.php');
$cron = new WWWUpdateCronManager();

$cron->startUpdate();
?>