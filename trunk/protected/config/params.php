<?php

// this contains the application parameters that can be maintained via GUI
return array(
    // this is displayed in the header section
    'title'=>'My Web3CMS', //My Web3CMS
    // this is your primary email address
    'adminEmail'=>'rafail@pravoslavie.md', //'webmaster@example.com',
	// From: "adminEmailName" <adminEmail>
	'adminEmailName'=>'Pravoslavie.MD', //'Webmaster'
    // hdd path to files folder
    'pathToFiles'=>dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR,
    // web-accessible url to files. leave it empty for using the default value
    'urlToFiles'=>'',
    // displayed in the footer of every page
    'copyrightBy'=>'My Company', //'My Company'
);
