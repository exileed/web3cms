<?php

// i18n - English Language Pack (HTTP)
$retval=array(
    'Invalid request. Please do not repeat this request again.' => 'Invalid request. Please do not repeat this request again.',
    'Page not found.' => 'Page not found.',
    'The requested page does not exist.' => 'The requested page does not exist.',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'_local'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;