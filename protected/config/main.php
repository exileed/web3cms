<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Web3CMS', //'Web3CMS'

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
        'application.extensions.*',
	),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>require(dirname(__FILE__).'/params.php'),

	// application components
	'components'=>array(
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error',
                    'logFile'=>'error.log',
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
                    'class'=>'CFileLogRoute',
                    'levels'=>'trace',
                    'logFile'=>'trace.log',
                ),
			),
		),
        'user'=>array(
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
            // force 401 HTTP error if authentication needed
            'loginUrl'=>array('user/login'),
        ),
        'db'=>array(
            //'connectionString'=>'sqlite:'.dirname(__FILE__).'/../data/web3cms.db',
            'connectionString'=>'mysql:host=localhost;dbname=web3cms', //dbname=web3cms
            'username'=>'buzz_db', //'xyz'
            'password'=>'Jy3llow', //'xxx'
        ),
        'urlManager'=>array(
            'urlFormat'=>'path', //uncomment if htaccess is supported by your server
            'rules'=>array(
            ),
        ),
	),
);