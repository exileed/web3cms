<?php

// i18n - Russian Vice-Versa Language Pack (Emails)
$retval=array(
    'New member account' => 'Новая учётная запись',
    'Content(New member account)' => 'Добро пожаловать к {siteTitle}

Новая учётная запись "{screenName}" была успешно создана.

--------------------------------------------------
Ключ подтверждения: {emailConfirmationKey}
--------------------------------------------------

Чтобы подтвердить ваш емайл адрес, пожалуйста посетите следующую ссылку
{emailConfirmationLink}',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'_local'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;