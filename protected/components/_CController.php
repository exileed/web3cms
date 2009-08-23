<?php

/**
 * _CController
 * Add and redefine controller methods of Yii core class CController.
 * Main initialization - it's run before everything else.
 */
class _CController extends CController
{
    /**
     * Call cms initialization.
     */
    public function init()
    {
        parent::init();
        W3Init::controller();
        if(!Yii::app()->user->isGuest)
        {
            // set user defined css theme
            $cssTheme=Yii::app()->user->cssTheme;
            if((is_string($cssTheme) || is_int($cssTheme)) && array_key_exists($cssTheme,MParams::getAvailableCssThemes()))
                MParams::setCssTheme($cssTheme);
            // set user defined language
            $language=Yii::app()->user->language;
            if((is_string($language) || is_int($language)) && array_key_exists($language,MParams::getAvailableLanguages()))
                MParams::setLanguage($language);
        }
        MParams::i18n();
    }
}