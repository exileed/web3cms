<?php
define('WEB_PATH', Yii::app()->request->getBaseUrl(true) . '/_forum/');
define('SHELL_PATH', Yii::app()->getBasePath() . '/../_forum/');
include(SHELL_PATH . $page . '.php');
?>