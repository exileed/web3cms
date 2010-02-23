<?php

// i18n - Russian Language Pack (HTTP)
$retval=array(
    'Page not found.' => 'РЎС‚СЂР°РЅРёС†Р° РЅРµ РЅР°Р№РґРµРЅР°.', /*'Страница не найдена.'*/
    'The requested page does not exist.' => 'Р—Р°РїСЂР°С€РёРІР°РµРјР°СЏ СЃС‚СЂР°РЅРёС†Р° РЅРµ СЃСѓС‰РµСЃС‚РІСѓРµС‚.', /*'Запрашиваемая страница не существует.'*/
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'mycustom'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;