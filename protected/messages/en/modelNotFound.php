<?php

// i18n - English Language Pack (Model Not Found)
$retval=array(
    'company' => 'We are sorry, but requested company could not be found in the database. Sorry for inconvenience.',
    'companyPayment' => 'We are sorry, but requested company payment could not be found in the database. Sorry for inconvenience.',
    'expense' => 'We are sorry, but requested expense could not be found in the database. Sorry for inconvenience.',
    'invoice' => 'We are sorry, but requested invoice could not be found in the database. Sorry for inconvenience.',
    'project' => 'We are sorry, but requested project could not be found in the database. Sorry for inconvenience.',
    'task' => 'We are sorry, but requested task could not be found in the database. Sorry for inconvenience.',
    'time' => 'We are sorry, but requested time record could not be found in the database. Sorry for inconvenience.',
    'user' => 'We are sorry, but requested member account could not be found in the database. Sorry for inconvenience.',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'_local'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;