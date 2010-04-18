<?php

// i18n - English Language Pack (Access Denied)
$retval=array(
    'company/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse companies.',
    'companyPayment/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse company payments.',
    'expense/delete' => 'Access denied. We are sorry, but you don\'t have enough rights to delete an expense record.',
    'expense/deleteWhenInvoiceSet' => 'Access denied. We are sorry, but you don\'t have enough rights to delete an expense record associated with an invoice.',
    'expense/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse expenses.',
    'invoice/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse invoices.',
    'project/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse projects.',
    'task/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse tasks.',
    'time/delete' => 'Access denied. We are sorry, but you don\'t have enough rights to delete a time record.',
    'time/deleteWhenInvoiceSet' => 'Access denied. We are sorry, but you don\'t have enough rights to delete a time record associated with an invoice.',
    'time/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse the time records.',
    'user/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse members.',
    'user/update' => 'Access denied. We are sorry, but you don\'t have enough rights to edit a member account.',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'mycustom'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;