<?php
/**
 * Core Class
 */
class W3
{
    /**
     * Set jQuery UI CSS Framework.
     * Path is {root}/css/ui/{interface}
     * This is a simple wrapper to {@link MParams::setInterface($value)}.
     * @param string $value
     */
    public static function setInterface($value)
    {
        if((is_string($value) || is_int($value)) && array_key_exists($value,MParams::getAvailableInterfaces()) && MPath::interfaceExists($value))
            MParams::setInterface($value);
        else
            Yii::log(W3::t('system',
                'Incorrect parameter in method call: {method}.',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
    }

    /**
     * Set site language.
     * This is a simple wrapper to {@link MParams::setLanguage($value)}.
     * @param string $value
     */
    public static function setLanguage($value)
    {
        if((is_string($value) || is_int($value)) && array_key_exists($value,MParams::getAvailableLanguages()))
            MParams::setLanguage($value);
        else
            Yii::log(W3::t('system',
                'Incorrect parameter in method call: {method}.',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
    }

    /**
     * Translates a message to the specified language.
     * Usually used to translate system messages, because system language
     * does not necessary have to be the same as current site (UI) language.
     * See {@link YiiBase::t()} for details.
     */
    public static function t($category,$message,$params=array(),$source=null,$language=null)
    {
        if($category==='system')
            $language=MParams::getSystemLanguage();
        return Yii::t($category,$message,$params,$source,$language);
    }
}