<?php
/**
 * _CHtml class file
 * 
 * Override some functions of Yii core class CHtml.
 */
class _CHtml extends CHtml
{
    public static $errorCss='ui-state-error';

    /* parent with a replacement for {jqueryUIScreenshot}. +2 lines, 1 line updated */
    public static function radioButtonList($name,$select,$data,$htmlOptions=array())
    {
        $template=isset($htmlOptions['template'])?$htmlOptions['template']:'{input} {label}';
        $separator=isset($htmlOptions['separator'])?$htmlOptions['separator']:"<br/>\n";
        unset($htmlOptions['template'],$htmlOptions['separator']);

        $labelOptions=isset($htmlOptions['labelOptions'])?$htmlOptions['labelOptions']:array();
        unset($htmlOptions['labelOptions']);

        $items=array();
        $baseID=self::getIdByName($name);
        $id=0;
        $hasJqueryUIScreenshot=strpos($template,'{jqueryUIScreenshot}')!==false;//+
        foreach($data as $value=>$label)
        {
            $jqueryUIScreenshot=$hasJqueryUIScreenshot?self::image(Yii::app()->request->baseUrl.'/static/css/ui/'.$value.'/screenshot.png',$label,array('height'=>105,'title'=>$label)):'';//+
            $checked=!strcmp($value,$select);
            $htmlOptions['value']=$value;
            $htmlOptions['id']=$baseID.'_'.$id++;
            $option=self::radioButton($name,$checked,$htmlOptions);
            $label=self::label($label,$htmlOptions['id'],$labelOptions);
            $items[]=strtr($template,array('{input}'=>$option,'{label}'=>$label,'{jqueryUIScreenshot}'=>$jqueryUIScreenshot));//!
        }
        return implode($separator,$items);
    }
    
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
            if(($label=$htmlOptions['label'])===false)
                return '';
            unset($htmlOptions['label']);
        }
        else
            $label=$model->getAttributeLabel($attribute);
        /*if($model->hasErrors($attribute))
            self::addErrorCss($htmlOptions);*/
        return self::label($label,$for,$htmlOptions);
    }
    
    /* 100% parent */
    public static function activeLabelEx($model,$attribute,$htmlOptions=array())
    {
        $realAttribute=$attribute;
        self::resolveName($model,$attribute); // strip off square brackets if any
        $htmlOptions['required']=$model->isAttributeRequired($attribute);
        return self::activeLabel($model,$realAttribute,$htmlOptions);
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
        return self::tag('textarea',$htmlOptions,isset($htmlOptions['encode']) && !$htmlOptions['encode'] ? $model->$attribute : self::encode($model->$attribute));
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
    
    /* 100% parent */
    public static function activeRadioButtonList($model,$attribute,$data,$htmlOptions=array())
    {
        self::resolveNameID($model,$attribute,$htmlOptions);
        $selection=$model->$attribute;
        if($model->hasErrors($attribute))
            self::addErrorCss($htmlOptions);
        $name=$htmlOptions['name'];
        unset($htmlOptions['name']);

        return self::hiddenField($name,'',array('id'=>self::ID_PREFIX.$htmlOptions['id']))
            . self::radioButtonList($name,$selection,$data,$htmlOptions);
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
                $header=/*'<p>'.*/Yii::t('hint','Please fix the following input errors:')/*.'</p>'*/;
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