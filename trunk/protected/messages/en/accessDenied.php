<?php

// i18n - English Language Pack (Access Denied)
$retval=array(
    'company/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse companies.',
    'company/update' => 'Access denied. We are sorry, but you don\'t have enough rights to edit a company record.',
    'companyPayment/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse company payments.',
    'companyPayment/update' => 'Access denied. We are sorry, but you don\'t have enough rights to edit a company payment record.',
    'expense/delete' => 'Access denied. We are sorry, but you don\'t have enough rights to delete an expense record.',
    'expense/deleteWhenInvoiceIsSet' => 'Access denied. We are sorry, but you don\'t have enough rights to delete an expense record associated with an invoice.',
    'expense/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse expenses.',
    'expense/update' => 'Access denied. We are sorry, but you don\'t have enough rights to update an expense record.',
    'expense/updateWhenInvoiceIsSet' => 'Access denied. We are sorry, but you don\'t have enough rights to update an expense record associated with an invoice.',
    'invoice/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse invoices.',
    'invoice/update' => 'Access denied. We are sorry, but you don\'t have enough rights to edit an invoice record.',
    'project/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse projects.',
    'project/update' => 'Access denied. We are sorry, but you don\'t have enough rights to edit a project record.',
    'task/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse tasks.',
    'task/update' => 'Access denied. We are sorry, but you don\'t have enough rights to edit a task record.',
    'time/delete' => 'Access denied. We are sorry, but you don\'t have enough rights to delete a time record.',
    'time/deleteWhenInvoiceIsSet' => 'Access denied. We are sorry, but you don\'t have enough rights to delete a time record associated with an invoice.',
    'time/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse the time records.',
    'time/update' => 'Access denied. We are sorry, but you don\'t have enough rights to update a time record.',
    'time/updateWhenInvoiceIsSet' => 'Access denied. We are sorry, but you don\'t have enough rights to update a time record associated with an invoice.',
    'user/grid' => 'Access denied. We are sorry, but you don\'t have enough rights to browse members.',
    'user/update' => 'Access denied. We are sorry, but you don\'t have enough rights to edit a member account.',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'mycustom'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;