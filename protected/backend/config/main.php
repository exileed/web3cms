<?php

$backend=dirname(dirname(__FILE__));
$frontend=dirname($backend);
Yii::setPathOfAlias('backend', $backend);

// This is the main Web application backend configuration. Any writable
// CWebApplication properties can be configured here.
return CMap::mergeArray(
    require($frontend.'/config/main.php'),
    array(
        'basePath'=>$frontend,

        'controllerPath'=>$backend.'/controllers',
        'viewPath'=>$backend.'/views',
        'runtimePath'=>$backend.'/runtime',

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
        'params'=>require(dirname(__FILE__).'/params.php'),

        // application components
        'components'=>array(
            'urlManager'=>array(
                'rules'=>require(dirname(__FILE__).'/routes.php'),
            ),
        ),
    )
);