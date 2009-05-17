<?php

/**
 * Initialize
 */
class _init
{
    public static function all()
    {
        self::css();
        self::params();
        self::script();
    }
    public function css()
    {
        $cs=Yii::app()->getClientScript();
        //main css
        $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/main.css');
        //yii css
        $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/yii.css');
        //jquery-ui
        $cs->registerCssFile(Yii::app()->request->baseUrl.'/css/jquery-ui-1.7.1.custom.css');
        //all jquery plugins css
        //$cs->registerCssFile(Yii::app()->request->baseUrl.'/css/jquery-1.3.x.plugins.css');
    }
    public static function params()
    {
        if(empty(Yii::app()->params['urlToFiles']))
            Yii::app()->params['urlToFiles']=Yii::app()->request->baseUrl.'/files/';
    }
    public function script()
    {
        $cs=Yii::app()->getClientScript();
        //jquery must be loaded anyway
        $cs->registerCoreScript('jquery');
        //all jquery plugins
        //$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery-1.3.x.plugins.js', CClientScript::POS_HEAD);
        //jquery-ui
        $cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery-ui-1.7.1.custom.min.js', CClientScript::POS_HEAD);
        //init plugins
    }
}