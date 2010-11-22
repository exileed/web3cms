<?php

// i18n - English Language Pack (System Messages)
$retval=array(
    'Available interfaces: {availableInterfaces}.' => 'Available interfaces: {availableInterfaces}.',
    'Available languages: {availableLanguages}.' => 'Available languages: {availableLanguages}.',
    'Class {class} does not exist. Method called: {method}.' => 'Class {class} does not exist. Method called: {method}.',
    'Could not delete the {model} model. Model ID: {modelId}. Method called: {method}.' => 'Could not delete the {model} model. Model ID: {modelId}. Method called: {method}.',
    'Could not load {model} model. Model ID: {modelId}. Method called: {method}.' => 'Could not load {model} model. Model ID: {modelId}. Method called: {method}.',
    'Could not save attributes of the {model} model. Model ID: {modelId}. Method called: {method}.' => 'Could not save attributes of the {model} model. Model ID: {modelId}. Method called: {method}.',
    'Failed creating UserDetails record. Member ID: {userId}. Method called: {method}.' => 'Failed creating UserDetails record. Member ID: {userId}. Method called: {method}.',
    'Incorrect parameter in method call: {method}.' => 'Incorrect parameter in method call: {method}.',
    'Member with ID {userId} has no UserDetails record associated. Method called: {method}.' => 'Member with ID {userId} has no UserDetails record associated. Method called: {method}.',
    'Missing parameter in file params.php: {parameter}.' => 'Missing parameter in file params.php: {parameter}.',
    'Unacceptable value of {parameter} system parameter: {value}. Method called: {method}.' => 'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
    'Unacceptable values of layout constants... content: {content}, sidebar1: {sidebar1}, sidebar2: {sidebar2}, total: {total}. Method called: {method}.' => 'Unacceptable values of layout constants... content: {content}, sidebar1: {sidebar1}, sidebar2: {sidebar2}, total: {total}. Method called: {method}.',
    'Unacceptable values of layout parameters... content: {content}, sidebar1: {sidebar1}, sidebar2: {sidebar2}, total: {total}. Method called: {method}.' => 'Unacceptable values of layout parameters... content: {content}, sidebar1: {sidebar1}, sidebar2: {sidebar2}, total: {total}. Method called: {method}.',
    'Uncommon parameter in method call: {method}.' => 'Uncommon parameter in method call: {method}.',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'_local'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;