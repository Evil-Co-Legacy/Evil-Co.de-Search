<?php
$_GET['page'] = 'API';

require_once('./global.php');
RequestHandler::handle(ArrayUtil::appendSuffix($packageDirs, 'lib/'));
?>