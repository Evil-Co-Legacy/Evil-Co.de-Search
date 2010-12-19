<?php

//initialize package array
$packageDirs = array();

//include config
require_once(dirname(__FILE__).'/config.inc.php');

//include WCF
require_once(RELATIVE_WCF_DIR.'global.php');
if(!count($packageDirs)) $packageDirs[] = WWW_DIR;
$packageDirs[] = WCF_DIR;

//starting application
require_once(WWW_DIR.'lib/system/WWWCore.class.php');
new WWWCore();
?>
