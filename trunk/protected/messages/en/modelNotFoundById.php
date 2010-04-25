<?php

// i18n - English Language Pack (Model Not Found By Id)
$retval=array(
    'company' => 'We are sorry, but company record number {id} could not be found in the database. Sorry for inconvenience.',
    'companyPayment' => 'We are sorry, but company payment record number {id} could not be found in the database. Sorry for inconvenience.',
    'expense' => 'We are sorry, but expense record number {id} could not be found in the database. Sorry for inconvenience.',
    'invoice' => 'We are sorry, but invoice record number {id} could not be found in the database. Sorry for inconvenience.',
    'project' => 'We are sorry, but project record number {id} could not be found in the database. Sorry for inconvenience.',
    'task' => 'We are sorry, but task record number {id} could not be found in the database. Sorry for inconvenience.',
    'time' => 'We are sorry, but time record number {id} could not be found in the database. Sorry for inconvenience.',
    'user' => 'We are sorry, but member account number {id} could not be found in the database. Sorry for inconvenience.',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'mycustom'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;