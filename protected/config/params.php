<?php

// this contains the application parameters that can be maintained via GUI
return array(
    // this is displayed in the header section
    'title'=>'My Web3CMS', //My Web3CMS
    // html > head > meta[description]
    'description'=>'Web 2.0 Content Management System based on Yii Framework. Web3CMS - Bringing Color to Powerful Yii Application!', //
    // html > head > meta[keywords]
    'keywords'=>'web3cms, cms, content management system, yii, jquery, ajax, web 2.0, jquery-ui, free, open source, mvc, framework', //
    // this is your primary email address
    'adminEmail'=>'rafail@pravoslavie.md', //'webmaster@example.com',
    // From: "adminEmailName" <adminEmail>
    'adminEmailName'=>'Pravoslavie.MD', //'Webmaster'
	// jQuery UI CSS Framework theme to use by default.
    // possible values: ui-lightness/ui-darkness/start
    // copy any from http://jqueryui.com/themeroller to {root}/css/themes/{theme}
    // reset this on any page with MLayout::setCssTheme('start');
	'defaultCssTheme'=>'start', //'start'
    // layout doctype. possible values: strict/transitional
    // reset this on any page with MLayout::setDoctype('transitional');
    'layoutDoctype'=>'transitional', //transitional
    // number of columns based on http://960.gs/ grid system. possible values: 12/16
    // reset this on any page with MLayout::setNOC(16);
    'layoutNumberOfColumns'=>16, //16=960px
    // number of columns for content. possible values: 1-16
    // reset this on any page with MLayout::setNOCContent(12); or MLayout::hideSidebars();
    'layoutNumberOfColumnsContent'=>12, //12of16=700px
    // number of columns for sidebar1. possible values: 1-16
    // reset this on any page with MLayout::setNOCSidebar1(0); or MLayout::hideSidebar1('sidebar2');
    'layoutNumberOfColumnsSidebar1'=>0, //0=sidebar1 is disabled
    // number of columns for sidebar2. possible values: 1-16
    // reset this on any page with MLayout::setNOCSidebar2(4); or MLayout::hideSidebar2('content');
    'layoutNumberOfColumnsSidebar2'=>4, //4of16=220px
    // whether or not wrap everything in css "grid_16" class. possible values: true/false
    // set it to false only(!) when using your own 960 css, e.g. .container_16 .grid_4 {width: 240px;}
    // if false, page will get 20px wider, from 940px to 960px.
    // see _layouts/main.php for an example
    'layoutWrapInGridCssClass'=>true, //true
    // hdd path to files folder. if empty, then it will be set in _init::params();
    'pathToFiles'=>dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR,
    // web-accessible url to files. if empty, then it will be set in _init::params();
    'urlToFiles'=>dirname($_SERVER['SCRIPT_NAME']).'/files/',
    // displayed in the footer of every page
    'copyrightBy'=>'My Company', //'My Company'
);
