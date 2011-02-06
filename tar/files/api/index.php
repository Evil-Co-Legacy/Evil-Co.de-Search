<?php
$_GET['page'] = $_REQUEST['page'] = 'API';

require_once('./global.php');
RequestHandler::handle(ArrayUtil::appendSuffix($packageDirs, 'lib/'));
?>
