<?php

// i18n - Russian Language Pack (Emails)
$retval=array(
    'New member account' => 'РќРѕРІР°СЏ СѓС‡С‘С‚РЅР°СЏ Р·Р°РїРёСЃСЊ', /*'Новая учётная запись'*/
    'Content(New member account)' => 'Р”РѕР±СЂРѕ РїРѕР¶Р°Р»РѕРІР°С‚СЊ Рє {siteTitle}

РќРѕРІР°СЏ СѓС‡С‘С‚РЅР°СЏ Р·Р°РїРёСЃСЊ "{screenName}" Р±С‹Р»Р° СѓСЃРїРµС€РЅРѕ СЃРѕР·РґР°РЅР°.

--------------------------------------------------
РљР»СЋС‡ РїРѕРґС‚РІРµСЂР¶РґРµРЅРёСЏ: {emailConfirmationKey}
--------------------------------------------------

Р§С‚РѕР±С‹ РїРѕРґС‚РІРµСЂРґРёС‚СЊ РІР°С€ РµРјР°Р№Р» Р°РґСЂРµСЃ, РїРѕР¶Р°Р»СѓР№СЃС‚Р° РїРѕСЃРµС‚РёС‚Рµ СЃР»РµРґСѓСЋС‰СѓСЋ СЃСЃС‹Р»РєСѓ
{emailConfirmationLink}', /*'Добро пожаловать к {siteTitle}

Новая учётная запись "{screenName}" была успешно создана.

--------------------------------------------------
Ключ подтверждения: {emailConfirmationKey}
--------------------------------------------------

Чтобы подтвердить ваш емайл адрес, пожалуйста посетите следующую ссылку
{emailConfirmationLink}'*/
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'mycustom'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;