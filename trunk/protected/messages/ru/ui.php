<?php

// i18n - Russian Language Pack (CSS Themes)
$retval=array(
    'Black tie' => 'Вечерний костюм',
    'Blitzer' => 'Блитцер',
    'Cupertino' => 'Купертино',
    'Dark hive' => 'Темный куст',
    'Dot luv' => 'Дот люв',
    'Eggplant' => 'Баклажан',
    'Excite bike' => 'Эмоциональный мотоцикл',
    'Flick' => 'Щелчок',
    'Hot sneaks' => 'Горячие кроссовки',
    'Humanity' => 'Человечество',
    'Le frog' => 'Лягушка',
    'Mint choc' => 'Шоколад с минтолом',
    'Overcast' => 'Облачный',
    'Pepper grinder' => 'Молотый перец',
    'Redmond' => 'Рэдмонд',
    'Smoothness' => 'Гладкий',
    'South street' => 'Южная улица',
    'Start' => 'Старт',
    'Sunny' => 'Солнечный',
    'Swanky purse' => 'Стильный кошелёк',
    'Trontastic' => 'Кофе с киви',
    'UI Darkness' => 'ВВ Тёмный',
    'UI Lightness' => 'ВВ Светлый',
    'Vader' => 'Вейдер',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'_local'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;