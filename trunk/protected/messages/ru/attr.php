<?php

// i18n - Russian Language Pack (Attributes)
$retval=array(
    'By default (Do not bill to company)' => 'По умолчанию (Не выставлять счёт компании)',
    'By default (Email is not visible by other members)' => 'По умолчанию (Емайл не виден другими участниками)',
    'By default (Member account is On)' => 'По умолчанию (Учётная запись включена)',
    'By default (The record is not confirmed by the project manager)' => 'По умолчанию (Запись не подтверждена руководителем проекта)',
    'By default (The record will not be shown in search results)' => 'По умолчанию (Запись не будет показана в результатах поиска)',
    'No (Do not bill to company)' => 'Нет (Не выставлять счёт компании)',
    'No (Email is not visible by other members)' => 'Нет (Емайл не виден другими участниками)',
    'No (Member account is Off)' => 'Нет (Учётная запись выключена)',
    'No (Member has not yet confirmed indicated email)' => 'Нет (Участник ещё не подтвердил указанный емайл)',
    'No (The record is not confirmed by the project manager)' => 'Нет (Запись не подтверждена руководителем проекта)',
    'No (The record will not be shown in search results)' => 'Нет (Запись не будет показана в результатах поиска)',
    'Yes (Bill to company)' => 'Да (Выставлять счёт компании)',
    'Yes (Email is visible by all members)' => 'Да (Емайл видим всеми участниками)',
    'Yes (Member account is On)' => 'Да (Учётная запись включена)',
    'Yes (Member has confirmed indicated email)' => 'Да (Участник подтвердил указанный емайл)',
    'Yes (The record is confirmed by the project manager)' => 'Да (Запись подтверждена руководителем проекта)',
    'Yes (The record will be shown in search results)' => 'Да (Запись будет показана в результатах поиска)',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'_local'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;