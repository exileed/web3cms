<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
$retval=array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'My Console Application',
);
$myfile=dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'mycustom'.DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;