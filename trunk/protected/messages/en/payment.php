<?php

// i18n - English Language Pack (Payment)
$retval=array(
    'Balance' => 'Balance',
    'Cash' => 'Cash',
    'Check' => 'Check',
    'Credit card' => 'Credit card',
    'Credit[accounting]' => 'Credit',
    'Debit amount' => 'Debit amount',
    'Method' => 'Method',
    'Number' => 'Number',
    'Paypal' => 'Paypal',
    'Wire' => 'Wire',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'_local'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;