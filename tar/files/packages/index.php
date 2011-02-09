<?php
$_GET['page'] = $_REQUEST['page'] = 'GetPackageUpdateXML';

require_once('./global.php');
RequestHandler::handle(ArrayUtil::appendSuffix($packageDirs, 'lib/'));
?>