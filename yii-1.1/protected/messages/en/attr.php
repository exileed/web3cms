<?php

// i18n - English Language Pack (Attributes)
$retval=array(
    'By default (Do not bill to company)' => 'By default (Do not bill to company)',
    'By default (Email is not visible by other members)' => 'By default (Email is not visible by other members)',
    'By default (Member account is On)' => 'By default (Member account is On)',
    'By default (The record is not confirmed by the project manager)' => 'By default (The record is not confirmed by the project manager)',
    'By default (The record will not be shown in search results)' => 'By default (The record will not be shown in search results)',
    'No (Do not bill to company)' => 'No (Do not bill to company)',
    'No (Email is not visible by other members)' => 'No (Email is not visible by other members)',
    'No (Member account is Off)' => 'No (Member account is Off)',
    'No (Member has not yet confirmed indicated email)' => 'No (Member has not yet confirmed indicated email)',
    'No (The record is not confirmed by the project manager)' => 'No (The record is not confirmed by the project manager)',
    'No (The record will not be shown in search results)' => 'No (The record will not be shown in search results)',
    'Yes (Bill to company)' => 'Yes (Bill to company)',
    'Yes (Email is visible by all members)' => 'Yes (Email is visible by all members)',
    'Yes (Member account is On)' => 'Yes (Member account is On)',
    'Yes (Member has confirmed indicated email)' => 'Yes (Member has confirmed indicated email)',
    'Yes (The record is confirmed by the project manager)' => 'Yes (The record is confirmed by the project manager)',
    'Yes (The record will be shown in search results)' => 'Yes (The record will be shown in search results)',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'mycustom'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;