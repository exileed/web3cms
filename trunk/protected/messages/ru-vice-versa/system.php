<?php

// i18n - Russian Vice-Versa Language Pack (System Messages)
$retval=array(
    'Available interfaces: {availableInterfaces}.' => 'Доступные внешние виды: {availableInterfaces}.',
    'Available languages: {availableLanguages}.' => 'Доступные языки: {availableLanguages}.',
    'Class {class} does not exist. Method called: {method}.' => 'Класса {class} не существует. Вызывался метод: {method}.',
    'Could not delete the {model} model. Model ID: {modelId}. Method called: {method}.' => 'Не удалось удалить модель {model}. ID модели: {modelId}. Вызываемый метод: {method}.',
    'Could not load {model} model. Model ID: {modelId}. Method called: {method}.' => 'Не удалось загрузить модель {model}. ID модели: {modelId}. Вызываемый метод: {method}.',
    'Could not save attributes of the {model} model. Model ID: {modelId}. Method called: {method}.' => 'Не удалось сохранить атрибуты модели {model}. ID модели: {modelId}. Вызываемый метод: {method}.',
    'Failed creating UserDetails record. Member ID: {userId}. Method called: {method}.' => 'Не удалось создать запись UserDetails. ID участника: {userId}. Вызывался метод: {method}.',
    'Incorrect parameter in method call: {method}.' => 'Неверный параметер в вызове метода: {method}.',
    'Member with ID {userId} has no UserDetails record associated. Method called: {method}.' => 'Участник с ID {userId} не имеет соответствующей UserDetails записи. Вызывался метод: {method}.',
    'Missing parameter in file params.php: {parameter}.' => 'Пропущен параметр в файле params.php: {parameter}.',
    'Unacceptable value of {parameter} system parameter: {value}. Method called: {method}.' => 'Недопустимое значение {parameter} системного параметра: {value}. Вызывался метод: {method}.',
    'Unacceptable values of layout constants... content: {content}, sidebar1: {sidebar1}, sidebar2: {sidebar2}, total: {total}. Method called: {method}.' => 'Недопустимые значения констант расположения... содержание: {content}, колонка1: {sidebar1}, колонка2: {sidebar2}, всего: {total}. Вызывался метод: {method}.',
    'Unacceptable values of layout parameters... content: {content}, sidebar1: {sidebar1}, sidebar2: {sidebar2}, total: {total}. Method called: {method}.' => 'Недопустимые значения параметров расположения... содержание: {content}, колонка1: {sidebar1}, колонка2: {sidebar2}, всего: {total}. Вызывался метод: {method}.',
    'Uncommon parameter in method call: {method}.' => 'Необычный параметер в вызове метода: {method}.',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'_local'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;