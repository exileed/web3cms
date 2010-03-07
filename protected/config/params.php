<?php

// this contains the application parameters that can be maintained via GUI
$retval=array(
    // this is your primary email address
    // reset this on any page with MParams::setAdminEmailAddress($email);
    'adminEmailAddress'=>'phpdevmd@web3cms.com', //'webmaster@example.com',
    // From: "adminEmailName" <adminEmail>
    // reset this on any page with MParams::setAdminEmailName($name);
    'adminEmailName'=>'Web3CMS Staff', //'Webmaster'
    // array of available interfaces
    // reset this on any page with MParams::setAvailableInterfaces($array);
    // or MParams::addAvailableInterfaces($interfaces,$prepend);
    'availableInterfaces'=>array(
        'ui-lightness'=>'UI Lightness',
        'ui-darkness'=>'UI Darkness',
        'smoothness'=>'Smoothness',
        'start'=>'Start',
        'redmond'=>'Redmond',
        'sunny'=>'Sunny',
        'overcast'=>'Overcast',
        'le-frog'=>'Le frog',
        'flick'=>'Flick',
        'pepper-grinder'=>'Pepper grinder',
        'eggplant'=>'Eggplant',
        'dark-hive'=>'Dark hive',
        'cupertino'=>'Cupertino',
        'south-street'=>'South street',
        'blitzer'=>'Blitzer',
        'humanity'=>'Humanity',
        'hot-sneaks'=>'Hot sneaks',
        'excite-bike'=>'Excite bike',
        'vader'=>'Vader',
        'dot-luv'=>'Dot luv',
        'mint-choc'=>'Mint choc',
        'black-tie'=>'Black tie',
        'trontastic'=>'Trontastic',
        'swanky-purse'=>'Swanky purse',
    ),
    // array of available languages
    // reset this on any page with MParams::setAvailableLanguages($array);
    // or MParams::addAvailableLanguages($languages,$prepend);
    'availableLanguages'=>array(
        'en'=>'English',
        'ru'=>'Russian',
    ),
    // displayed in the footer of every page
    // reset this on any page with MParams::setCopyrightBy('My Company');
    'copyrightBy'=>'My Company', //'My Company'
    // title of your cms, displayed in the header section (above menu)
    // reset this on any page with MParams::setHeaderTitle($title);
    'headerTitle'=>'My Web3CMS', //'My Web3CMS'
    // html doctype. possible values: strict/transitional
    // reset this on any page with MParams::setHtmlDoctype('transitional');
    'htmlDoctype'=>'transitional', //'transitional'
    // jQuery UI CSS Framework to use by default
    // possible values: see 'availableInterfaces' above
    // copy any from http://jqueryui.com/themeroller to {root}/css/ui/{interface}
    // reset this on any page with MParams::setInterface('start');
    'interface'=>'start', //'start'
    // site language. should be one of 'availableLanguages'
    // reset this on any page with MParams::setLanguage('en');
    'language'=>'en', //en
    // number of columns for content. possible values: 1-16
    // reset this on any page with MLayout::setNOCContent(12); or MLayout::hideSidebars();
    'layoutNumberOfColumnsContent'=>12, //12of16=700px
    // number of columns for sidebar1. possible values: 1-16
    // reset this on any page with MLayout::setNOCSidebar1(0); or MLayout::hideSidebar1('sidebar2');
    'layoutNumberOfColumnsSidebar1'=>0, //0=sidebar1 is disabled
    // number of columns for sidebar2. possible values: 1-16
    // reset this on any page with MLayout::setNOCSidebar2(4); or MLayout::hideSidebar2('content');
    'layoutNumberOfColumnsSidebar2'=>4, //4of16=220px
    // number of columns based on http://960.gs/ grid system. possible values: 12/16
    // reset this on any page with MLayout::setNOC(16);
    'layoutNumberOfColumnsTotal'=>16, //16=960px
    // whether or not wrap everything in css "grid_16" class. possible values: true/false
    // set it to false only(!) when using your own 960 css, e.g. .container_16 .grid_4 {width: 240px;}
    // if false, page will get 20px wider, from 940px to 960px.
    // see _layouts/main.php for an example
    'layoutWrapInGridCssClass'=>true, //true
    // whether or not main menu box should be 100% of the page width (not just 960px). possible values: true/false
    // reset this on any page with MParams::setMainMenuFullWidth(true);
    'mainMenuFullWidth'=>true, //true
    // html > head > meta[description]
    // reset this on any page with MParams::setMetaDescription($description);
    // or MParams::addMetaDescription($description,$delimiter=' ');
    'metaDescription'=>'Web 2.0 Content Management System based on Yii Framework. Web3CMS - Bringing Color to Powerful Yii Application!',
    // html > head > meta[keywords]
    // reset this on any page with MParams::setMetaKeywords($keywords);
    // or MParams::addMetaKeywords($keywords,$prepend);
    'metaKeywords'=>array('web3cms','cms','content management system','php','yii','jquery','ajax','web 2.0','jquery-ui','free','open source','mvc','framework'),
    // array of model attributes that system should (not) use
    // reset this on any page with MParams::setModelAttributes($attributes,$model);
    // or MParams::restoreModelAttributes($model,$attributes);
    'modelAttributes'=>array(
        'User'=>array(
            //'accessLevel'=>false,
            // email2 is 'Repeat email' field on the register user page
            'email2'=>false,
            //'id'=>false,
            //'isEmailConfirmed'=>false,
            //'isEmailVisible'=>false,
            // if username is false, set 'userLoginWithField' below to 'email'
            //'username'=>false,
        ),
    ),
    // page title formula to be used by MParams::setPageTitle();
    // to set html > head > title
    // reset this on any page with MParams::setPageTitleFormula('{pageLabel} - {siteTitle}');
    'pageTitleFormula'=>'{pageLabel} - {siteTitle}', //'{pageLabel} - {siteTitle}'
    // hdd path to files folder. must be a valid directory accessible within your hosting
    // reset this on any page with MParams::setPathToFiles($path);
    'pathToFiles'=>dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR,
    // whether or not register jquery-ui css in {@link W3Init::css}. possible values: true/false
    // reset this on any page with MParams::setRegisterJqueryUI(true);
    'registerJqueryUI'=>true, //true
    // title of your site, used in html > head > title
    // see also 'pageTitleFormula' parameter
    // reset this on any page with MParams::setSiteTitle('Web3CMS');
    'siteTitle'=>'Web3CMS', //'Web3CMS'
    // system language - language for system messages (mostly in logs)
    // reset this on any page with MParams::setSystemLanguage('en');
    'systemLanguage'=>'en', //'en'
    // database tables prefix
    'tablePrefix' => 'w3_',
    // web-accessible url to files directory
    // supposed to begin with either http:// or / (slash). should contain trailing slash
    // reset this on any page with MParams::setUrlToFiles($url);
    'urlToFiles'=>dirname($_SERVER['SCRIPT_NAME']).'/files/',
    // which field to log user in with. possible values: username/email/_any_
    // reset this on any page with MParams::setUserLoginWithField('username');
    'userLoginWithField'=>'username', //'username'
);
$myfile=dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'mycustom'.DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;