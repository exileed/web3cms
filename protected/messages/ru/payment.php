<?php

// i18n - Russian Language Pack (Payment)
$retval=array(
    'Balance' => 'Баланс',
    'Cash' => 'Наличные',
    'Check' => 'Чек',
    'Credit card' => 'Кредитная карточка',
    'Credit[accounting]' => 'Кредит',
    'Debit' => 'Дебет',
    'Method' => 'Способ',
    'Number' => 'Номер',
    'Paypal' => 'Paypal',
    'Wire' => 'Перевод',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'_local'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;