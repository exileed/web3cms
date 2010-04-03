<?php

// i18n - Russian Original Language Pack (HTTP)
$retval=array(
    'Page not found.' => 'Страница не найдена.',
    'The requested page does not exist.' => 'Запрашиваемая страница не существует.',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'mycustom'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;