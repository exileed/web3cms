<?php

// i18n - Russian Language Pack (Payment)
$retval=array(
    'Balance' => 'Р‘Р°Р»Р°РЅСЃ', /*'Баланс'*/
    'Cash' => 'РќР°Р»РёС‡РЅС‹Рµ', /*'Наличные'*/
    'Check' => 'Р§РµРє', /*'Чек'*/
    'Credit card' => 'РљСЂРµРґРёС‚РЅР°СЏ РєР°СЂС‚РѕС‡РєР°', /*'Кредитная карточка'*/
    'Credit[accounting]' => 'РљСЂРµРґРёС‚', /*'Кредит'*/
    'Debit' => 'Р”РµР±РµС‚', /*'Дебет'*/
    'Method' => 'РЎРїРѕСЃРѕР±', /*'Способ'*/
    'Number' => 'РќРѕРјРµСЂ', /*'Номер'*/
    'Paypal' => 'Paypal', /*'Paypal'*/
    'Wire' => 'РџРµСЂРµРІРѕРґ', /*'Перевод'*/
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'mycustom'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;