<?php

// i18n - English Language Pack (CSS Themes)
$retval=array(
    'Black tie' => 'Black tie',
    'Blitzer' => 'Blitzer',
    'Cupertino' => 'Cupertino',
    'Dark hive' => 'Dark hive',
    'Dot luv' => 'Dot luv',
    'Eggplant' => 'Eggplant',
    'Excite bike' => 'Excite bike',
    'Flick' => 'Flick',
    'Hot sneaks' => 'Hot sneaks',
    'Humanity' => 'Humanity',
    'Le frog' => 'Le frog',
    'Mint choc' => 'Mint choc',
    'Overcast' => 'Overcast',
    'Pepper grinder' => 'Pepper grinder',
    'Redmond' => 'Redmond',
    'Smoothness' => 'Smoothness',
    'South street' => 'South street',
    'Start' => 'Start',
    'Sunny' => 'Sunny',
    'Swanky purse' => 'Swanky purse',
    'Trontastic' => 'Trontastic',
    'UI Darkness' => 'UI Darkness',
    'UI Lightness' => 'UI Lightness',
    'Vader' => 'Vader',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'mycustom'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;