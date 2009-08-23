<?php

/**
 * _CHtml
 * Add and redefine html methods to Yii core class CHtml
 */
class _CHtml extends CHtml
{
    public static $errorCss='ui-state-error';

    /* parent without error css class */
    public static function activeLabel($model,$attribute,$htmlOptions=array())
    {
        if(isset($htmlOptions['for']))
        {
            $for=$htmlOptions['for'];
            unset($htmlOptions['for']);
        }
        else
            $for=self::getIdByName(self::resolveName($model,$attribute));
        if(isset($htmlOptions['label']))
        {
            $label=$htmlOptions['label'];
            unset($htmlOptions['label']);
        }
        else
            $label=$model->getAttributeLabel($attribute);
        /*if($model->hasErrors($attribute))
            self::addErrorCss($htmlOptions);*/
        return self::label($label,$for,$htmlOptions);
    }
    
    /* 100% parent */
    public static function activeTextField($model,$attribute,$htmlOptions=array())
    {
        self::resolveNameID($model,$attribute,$htmlOptions);
        self::clientChange('change',$htmlOptions);
        return self::activeInputField('text',$model,$attribute,$htmlOptions);
    }
    
    /* 100% parent */
    public static function activePasswordField($model,$attribute,$htmlOptions=array())
    {
        self::resolveNameID($model,$attribute,$htmlOptions);
        self::clientChange('change',$htmlOptions);
        return self::activeInputField('password',$model,$attribute,$htmlOptions);
    }
    
    /* 100% parent */
    public static function activeTextArea($model,$attribute,$htmlOptions=array())
    {
        self::resolveNameID($model,$attribute,$htmlOptions);
        self::clientChange('change',$htmlOptions);
        if($model->hasErrors($attribute))
            self::addErrorCss($htmlOptions);
        return self::tag('textarea',$htmlOptions,self::encode($model->$attribute));
    }
    
    /* 100% parent */
    public static function activeDropDownList($model,$attribute,$data,$htmlOptions=array())
    {
        self::resolveNameID($model,$attribute,$htmlOptions);
        $selection=$model->$attribute;
        $options="\n".self::listOptions($selection,$data,$htmlOptions);
        self::clientChange('change',$htmlOptions);
        if($model->hasErrors($attribute))
            self::addErrorCss($htmlOptions);
        if(isset($htmlOptions['multiple']))
        {
            if(substr($htmlOptions['name'],-2)!=='[]')
                $htmlOptions['name'].='[]';
        }
        return self::tag('select',$htmlOptions,$options);
    }

    /* currently no need to wrap label in <p> and the whole thing in <div> */
    public static function errorSummary($model,$header=null,$footer=null,$htmlOptions=array())
    {
        $content='';
        if(!is_array($model))
            $model=array($model);
        foreach($model as $m)
        {
            foreach($m->getErrors() as $errors)
            {
                foreach($errors as $error)
                {
                    if($error!='')
                        $content.="<li>$error</li>\n";
                }
            }
        }
        if($content!=='')
        {
            if($header===null)
                $header=/*'<p>'.*/Yii::t('user','Please fix the following input errors:')/*.'</p>'*/;
            if(!isset($htmlOptions['class']))
                $htmlOptions['class']=self::$errorSummaryCss;
            return /*self::tag('div',$htmlOptions,*/$header."\n<ul>\n$content</ul>".$footer/*)*/;
        }
        else
            return '';
    }

    /* 100% parent */
    protected static function activeInputField($type,$model,$attribute,$htmlOptions)
    {
        $htmlOptions['type']=$type;
        if($type==='file')
            unset($htmlOptions['value']);
        else if(!isset($htmlOptions['value']))
            $htmlOptions['value']=$model->$attribute;
        if($model->hasErrors($attribute))
            self::addErrorCss($htmlOptions);
        return self::tag('input',$htmlOptions);
    }
    
    /* 100% parent */
    protected static function addErrorCss(&$htmlOptions)
    {
        if(isset($htmlOptions['class']))
            $htmlOptions['class'].=' '.self::$errorCss;
        else
            $htmlOptions['class']=self::$errorCss;
    }
}