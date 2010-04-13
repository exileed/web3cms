<?php

// i18n - Russian Vice-Versa Language Pack (Access Denied)
$retval=array(
    'company/grid' => 'Доступ запрещён. Сожалеем, но у вас недостаточно прав для просматривания компаний.',
    'companyPayment/grid' => 'Доступ запрещён. Сожалеем, но у вас недостаточно прав для просматривания платежей компаний.',
    'expense/grid' => 'Доступ запрещён. Сожалеем, но у вас недостаточно прав для просматривания расходов.',
    'invoice/grid' => 'Доступ запрещён. Сожалеем, но у вас недостаточно прав для просматривания счетов.',
    'project/grid' => 'Доступ запрещён. Сожалеем, но у вас недостаточно прав для просматривания проектов.',
    'task/grid' => 'Доступ запрещён. Сожалеем, но у вас недостаточно прав для просматривания задач.',
    'time/grid' => 'Доступ запрещён. Сожалеем, но у вас недостаточно прав для просматривания записей времени.',
    'user/grid' => 'Доступ запрещён. Сожалеем, но у вас недостаточно прав для просматривания участников.',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'mycustom'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;