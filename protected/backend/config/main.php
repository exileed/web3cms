<?php

$backend=dirname(dirname(__FILE__));
$frontend=dirname($backend);
Yii::setPathOfAlias('backend', $backend);

// This is the main Web application backend configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => $frontend,

    'controllerPath' => $backend.'/controllers',
    'viewPath' => $backend.'/views',
    'runtimePath' => $backend.'/runtime',

    // preloading 'log' component, session - to track down problems with specific users
    'preload'=>array('log', 'session'),

    // autoloading model and component classes
    'import'=>array(
        'backend.models.*',
        'backend.components.*',
        'application.models.*',
        'application.components.*',
        'application.extensions.*',
    ),

    // alternate layoutPath
    'layoutPath'=>dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'_layouts'.DIRECTORY_SEPARATOR,

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName'] and MParams class
    'params'=>require(dirname(__FILE__).'/params.php'),

    // 'en_us' is the sourceLanguage used by default
    'sourceLanguage'=>'en_us',
    // if sourceLanguage == language then no translation will be done
    'language'=>'en',

    // application components
    'components'=>array(
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error',
                    'logFile'=>'error.log',
                    'filter'=>'CLogFilter', // this saves _cookie, _server
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'warning',
                    'logFile'=>'warning.log',
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'notice',
                    'logFile'=>'notice.log',
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'info',
                    'logFile'=>'info.log',
                ),
                array(
                    'class'=>'CFileLogRoute', // 'CFileLogRoute','CWebLogRoute'
                    'levels'=>'trace', // 'trace,info,error,warning'
                    'logFile'=>'trace.log', //
                    'maxFileSize'=>1024, //
                    // to track down problems with specific users:
                    /*'filter' => array(
                        'class' => 'CLogFilter',
                        'prefixSession' => true,
                        'prefixUser' => false,
                        'logUser' => false,
                        'logVars' => array(),
                    ),*/
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'w3',
                    'categories'=>'w3',
                    'logFile'=>'w3.log',
                ),
            ),
        ),
        'user'=>array(
            // override CWebUser class
            'class'=>'_CWebUser',
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
            // force 401 HTTP error if authentication needed
            'loginUrl'=>array('user/login'),
        ),
        'db'=>array(
            //'connectionString'=>'sqlite:'.dirname(__FILE__).'/../data/web3cms.db',
            // CREATE DATABASE `web3cms_r8` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
            'connectionString'=>'mysql:host=localhost;dbname=web3cms_r8',
            'username'=>'web3cms', //'web3cms'
            'password'=>'web3cms', //'web3cms'
            'charset'=>'utf8', //comment this if you are using a different db charset
        ),
        'urlManager'=>array(
            'urlFormat'=>'path', //comment this if htaccess is not supported by your server
            'rules'=>array(
            ),
        ),
    ),
);