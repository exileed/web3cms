<?php

// url routes
$retval=array(
    //'user/confirm-email'=>'user/confirmEmail',
);
$myfile=dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'_local'.DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;