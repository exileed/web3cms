<?php

$backend=dirname(dirname(__FILE__));
$frontend=dirname($backend);
Yii::setPathOfAlias('backend',$backend);

// This is the main Web application backend configuration. Any writable
// CWebApplication properties can be configured here.
$retval=CMap::mergeArray(
    require($frontend.DIRECTORY_SEPARATOR.'config/main.php'),
    array(
        'basePath'=>$frontend,

        'controllerPath'=>$backend.DIRECTORY_SEPARATOR.'controllers',
        'viewPath'=>$backend.DIRECTORY_SEPARATOR.'views',
        'runtimePath'=>$backend.DIRECTORY_SEPARATOR.'runtime',

        // autoloading model and component classes
        'import'=>array(
            'backend.models.*',
            'backend.components.*',
            'application.models.*',
            'application.components.*',
            'application.extensions.*',
        ),

        // main is the default layout
        'layout'=>'main',
        // alternate layoutPath
        'layoutPath'=>dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'_layouts'.DIRECTORY_SEPARATOR,

        // application-level parameters that can be accessed
        // using Yii::app()->params['paramName'] and MParams class
        'params'=>require(dirname(__FILE__).DIRECTORY_SEPARATOR.'params.php'),

        // application components
        'components'=>array(
            'urlManager'=>array(
                'rules'=>require(dirname(__FILE__).DIRECTORY_SEPARATOR.'routes.php'),
            ),
        ),
    )
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'_local'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;