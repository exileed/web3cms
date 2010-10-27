<?php
/**
 * Initialize Site
 */
class W3Init
{
    /**
     * Alias. Initialize all.
     */
    public static function all()
    {
        self::css();
        self::params();
        self::script();
    }
    /**
     * Alias. Initialize site parameters. Is called from controller.
     */
    public static function controller()
    {
        self::params();
    }
    /**
     * Alias. Initialize javascripts and css.
     */
    public static function jsCss()
    {
        self::css();
        self::script();
    }

    /**
     * Initialize css.
     */
    public static function css()
    {
        $cs=Yii::app()->getClientScript();
        // main css
        $cs->registerCssFile(Yii::app()->request->baseUrl.'/static/css/main.css');
        // 960 css
        $cs->registerCssFile(Yii::app()->request->baseUrl.'/static/css/960.css');
        // all jquery plugins css
        //$cs->registerCssFile(Yii::app()->request->baseUrl.'/static/css/jquery-1.3.x.plugins.css');
        // jquery-ui
        if(MParams::getRegisterJqueryUI() && MPath::interfaceExists(MParams::getInterface()))
            $cs->registerCssFile(Yii::app()->request->baseUrl.'/static/css/ui/'.MParams::getInterface().'/jquery-ui-'.MParams::jqueryUIVersion.'.custom.css');
        // use this css if you want to globally redefine jquery-ui css framework classes
        $redefineJqueryUI=dirname(Yii::app()->basePath).DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'ui.css';
        if(file_exists($redefineJqueryUI) && filesize($redefineJqueryUI)!==0)
            $cs->registerCssFile(Yii::app()->request->baseUrl.'/static/css/ui.css');
    }

    /**
     * Load config/params.php params into MParams and MLayout.
     * Use MParams wrappers to avoid problems with wrong type or value out of range.
     */
    public static function params()
    {
        MParams::load();
        MLayout::load();
    }

    /**
     * Initialize javascripts.
     */
    public static function script()
    {
        $cs=Yii::app()->getClientScript();
        // jquery must be always loaded
        $cs->registerCoreScript('jquery');
        // all jquery plugins
        //$cs->registerScriptFile(Yii::app()->request->baseUrl.'/static/js/jquery-1.3.x.plugins.js',CClientScript::POS_HEAD);
        // jquery-ui
        $cs->registerScriptFile(Yii::app()->request->baseUrl.'/static/js/jquery-ui-'.MParams::jqueryUIVersion.'.custom.min.js',CClientScript::POS_HEAD);
        // attribute 'target' is not allowed by xhtml strict doctype
        $cs->registerScript('targetBlank',"jQuery(\"a[rel^='external']\").attr({'target': '_blank'});",CClientScript::POS_READY);
        // call noConflict() function if prototype.js was included before jquery
        // details at http://docs.jquery.com/Using_jQuery_with_Other_Libraries
        /*$cs->registerScript('noConflict','jQuery.noConflict();',CClientScript::POS_HEAD);*/
    }
}