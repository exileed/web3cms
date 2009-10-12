<?php
/**
 * _CController class file.
 * Add and redefine controller methods of Yii core class CController.
 * Main initialization - it's run before everything else.
 */
class _CController extends CController
{
    /**
     * @var instance of MVariable
     */
    public $var;

    /**
     * Call cms initialization.
     */
    public function init()
    {
        // Yii initialization is a must
        parent::init();
        // MVariable is a storage of variables to share between all classes
        // access it using Yii::app()->controller->var (or $this->var in the views) 
        $this->var=new MVariable;
        // call our initialization class
        W3Init::controller();
        // set user preferences (interface, language, and so on)
        if(!Yii::app()->user->isGuest)
        {
            if(isset(Yii::app()->user->interface) && !empty(Yii::app()->user->interface))
                // set user preferred interface
                W3::setInterface(Yii::app()->user->interface);
            if(isset(Yii::app()->user->language) && !empty(Yii::app()->user->language))
                // set user preferred language
                W3::setLanguage(Yii::app()->user->language);
        }
        // parameters were loaded before language was set, now need to translate
        MParams::i18n();
    }
}