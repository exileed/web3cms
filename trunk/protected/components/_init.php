<?php

/**
 * Initialize
 */
class _init
{
    /**
    * Alias. Initialize all
    */
    public static function all()
    {
        self::css();
        self::params();
        self::script();
    }
    /**
    * Alias. Initialize site parameters from controller
    */
    public static function fromController()
    {
        self::params();
    }
    /**
    * Alias. Initialize javascripts and css
    */
    public static function jsCss()
    {
        self::css();
        self::script();
    }

    /**
    * Initialize css
    */
    public function css()
    {
        $cs=Yii::app()->getClientScript();
        // main css
        $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/main.css');
        // 960 css
        $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/960.css');
        // yii css
        $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/yii.css');
        // all jquery plugins css
        //$cs->registerCssFile(Yii::app()->request->baseUrl.'/css/jquery-1.3.x.plugins.css');
        // jquery-ui
        $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/themes/'.MLayout::getCssTheme().'/jquery-ui-'.MLayout::jqueryUIVersion.'.custom.css');
    }

    /**
    * Check params to avoid problems with wrong type or value out of range
    */
    public static function params()
    {
        if(!MLayout::cssThemeExists(Yii::app()->params['defaultCssTheme']))
            Yii::log(Yii::t('w3','Wrong value of {parameter} system parameter: {value}',array('{parameter}'=>"'defaultCssTheme'",'{value}'=>var_export(Yii::app()->params['defaultCssTheme'],true))),'error','w3');
        /*if(!in_array(Yii::app()->params['layoutDoctype'],array('strict','transitional')))
            Yii::app()->params['layoutDoctype']='transitional';*/
        Yii::app()->params['layoutNumberOfColumns']=intval(Yii::app()->params['layoutNumberOfColumns']);
        /*if(!in_array(Yii::app()->params['layoutNumberOfColumns'],array(12,16)))
            Yii::app()->params['layoutNumberOfColumns']=16;*/
        $max=Yii::app()->params['layoutNumberOfColumns'];
        Yii::app()->params['layoutNumberOfColumnsSidebar1']=intval(Yii::app()->params['layoutNumberOfColumnsSidebar1']);
        if(Yii::app()->params['layoutNumberOfColumnsSidebar1']<0)
            Yii::app()->params['layoutNumberOfColumnsSidebar1']=0;
        else if(Yii::app()->params['layoutNumberOfColumnsSidebar1']>$max)
            Yii::app()->params['layoutNumberOfColumnsSidebar1']=1;
        Yii::app()->params['layoutNumberOfColumnsContent']=intval(Yii::app()->params['layoutNumberOfColumnsContent']);
        if(Yii::app()->params['layoutNumberOfColumnsContent']<0)
            Yii::app()->params['layoutNumberOfColumnsContent']=0;
        else if(Yii::app()->params['layoutNumberOfColumnsContent']>$max)
            Yii::app()->params['layoutNumberOfColumnsContent']=1;
        Yii::app()->params['layoutNumberOfColumnsSidebar2']=intval(Yii::app()->params['layoutNumberOfColumnsSidebar2']);
        if(Yii::app()->params['layoutNumberOfColumnsSidebar2']<0)
            Yii::app()->params['layoutNumberOfColumnsSidebar2']=0;
        else if(Yii::app()->params['layoutNumberOfColumnsSidebar2']>$max)
            Yii::app()->params['layoutNumberOfColumnsSidebar2']=1;
        if(Yii::app()->params['layoutNumberOfColumnsSidebar1']+Yii::app()->params['layoutNumberOfColumnsContent']+Yii::app()->params['layoutNumberOfColumnsSidebar2'] > $max)
        {
            Yii::app()->params['layoutNumberOfColumnsSidebar1']=0;
            Yii::app()->params['layoutNumberOfColumnsContent']=Yii::app()->params['layoutNumberOfColumns']==16?12:9;
            Yii::app()->params['layoutNumberOfColumnsSidebar2']=Yii::app()->params['layoutNumberOfColumns']==16?4:3;
        }
        if(empty(Yii::app()->params['pathToFiles']))
            Yii::app()->params['pathToFiles']=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR;
        if(empty(Yii::app()->params['urlToFiles']))
            Yii::app()->params['urlToFiles']=Yii::app()->request->baseUrl.'/files/';
    }

    /**
    * Initialize javascripts
    */
    public function script()
    {
        $cs=Yii::app()->getClientScript();
        // jquery must be always loaded
        $cs->registerCoreScript('jquery');
        // all jquery plugins
        //$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery-1.3.x.plugins.js',CClientScript::POS_HEAD);
        // jquery-ui
        $cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery-ui-'.MLayout::jqueryUIVersion.'.custom.min.js',CClientScript::POS_HEAD);
        // call noConflict() function if prototype.js was included before jquery. details at http://docs.jquery.com/Using_jQuery_with_Other_Libraries
        /*$cs->registerScript('jQuery.noConflict();',CClientScript::POS_HEAD);*/
    }
}