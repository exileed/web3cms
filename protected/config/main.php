<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
$retval=array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',

    // preloading 'log' component, session - to track down problems with specific users
    'preload'=>array('log','session'),

    // autoloading model and component classes
    'import'=>array(
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

    // 'en_us' is the sourceLanguage used by default
    'sourceLanguage'=>'en_us',
    // if sourceLanguage == language then no translation will be done
    'language'=>'en',

    // application components
    'components'=>array(
        'db'=>array(
            //'connectionString'=>'sqlite:'.dirname(__FILE__).'/../data/web3cms.db',
            // CREATE DATABASE `web3cms_r34` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
            'class'=>'application.components.DBLayer',
            'connectionString'=>'mysql:host=localhost;dbname=db',
            'username'=>'root', //'web3cmsuser'
            'password'=>'', //'web3cmspass'
            'charset'=>'utf8', //comment this if you are using a different db charset
        ),
        'errorHandler'=>array(
            'errorAction'=>'site/error',
        ),
        'log'=>array(
        'class'=>'CLogRouter',
        'routes'=>array(
            array(
                'class'=>'CWebLogRoute',
                'levels'=>'trace,info,error,warning',
                'filter' => array(
                    'class' => 'CLogFilter',
                    'prefixSession' => true,
                    'prefixUser' => false,
                    'logUser' => false,
                    'logVars' => array(),
                ),
            ),
        )),
        'urlManager'=>array(
            //'urlFormat'=>'path',
            //'caseSensitive'=>false,
            //'showScriptName'=>true,
            //'urlSuffix'=>'.html',
            'rules'=>require(dirname(__FILE__).DIRECTORY_SEPARATOR.'routes.php'),
        ),
        'user'=>array(
            // override CWebUser class
            'class'=>'_CWebUser',
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
            // force 401 HTTP error if authentication needed
            'loginUrl'=>array('user/login'),
        ),
    ),
);
$myfile=dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'mycustom'.DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;