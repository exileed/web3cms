<?php

// i18n - Russian Vice-Versa Language Pack (HTTP)
$retval=array(
    'Invalid request. Please do not repeat this request again.' => '�������� ������. ���������� �� ���������� ������ ������ �����.',
    'Page not found.' => '�������� �� �������.',
    'The requested page does not exist.' => '������������� �������� �� ����������.',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'mycustom'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;