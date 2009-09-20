<?php
/**
 * Manage Site Parameters.
 * If a parameter has a wrong type or it's out of allowed values,
 * then default value is applied.
 * In the beginning all parameters are loaded from {@link _CController::init()},
 * then strings are translated (after _CController has set preferred language).
 */
class MParams
{
    protected static $adminEmailAddress;
    protected static $adminEmailName;
    protected static $availableInterfaces;
    protected static $availableLanguages;
    protected static $copyrightBy;
    protected static $headerTitle;
    protected static $htmlDoctype;
    protected static $interface;
    protected static $language;
    protected static $metaDescription;
    protected static $metaKeywords;
    protected static $modelAttributes;
    protected static $pageLabel;
    protected static $pageTitleFormula;
    protected static $pathToFiles;
    protected static $registerJqueryUI;
    protected static $siteTitle;
    protected static $systemLanguage;
    protected static $urlToFiles;
    protected static $userLoginWithField;
    protected static $_data;
    protected static $allowedHtmlDoctype;
    protected static $allowedUserLoginWithField;
    protected static $defaultAvailableInterfaces;
    protected static $defaultAvailableLanguages;
    protected static $defaultHeaderTitle;
    protected static $defaultMetaKeywords;
    protected static $defaultModelAttributes;
    protected static $defaultPageLabel;
    protected static $defaultPathToFiles;
    protected static $defaultSiteTitle;
    protected static $defaultUrlToFiles;
    const _default='_default';
    const jqueryUIVersion='1.7.2';
    const defaultAdminEmailAddress='phpdevmd@web3cms.com';
    const defaultAdminEmailName='Web3CMS Staff';
    const defaultCopyrightBy='My Company';
    const defaultHtmlDoctype='transitional';
    const defaultInterface='start';
    const defaultLanguage='en';
    const defaultMetaDescription='Web3CMS - Web 2.0 Content Management System based on Yii Framework.';
    const defaultPageTitleFormula='{pageLabel} - {siteTitle}';
    const defaultRegisterJqueryUI=true;
    const defaultSystemLanguage='en';
    const defaultUserLoginWithField='username';

    /**
     * Load params from Yii::app()->params into class properties.
     */
    public static function load()
    {
        self::$allowedHtmlDoctype=array('strict','transitional');
        self::$allowedUserLoginWithField=array('_any_','email','username');
        self::$defaultAvailableInterfaces=array(
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
        );
        self::$defaultAvailableLanguages=array(
            'en'=>'English',
            'ru'=>'Russian',
        );
        self::$defaultHeaderTitle=MArea::isBackend() ? 'Web3CMS Administrator Area' : 'My Web3CMS';
        self::$defaultMetaKeywords=array('web3cms','yii');
        self::$defaultModelAttributes=array(
            'User'=>array(
                'email2'=>false,
            ),
        );
        self::$defaultPageLabel='Home';
        self::$defaultPathToFiles=dirname(Yii::app()->basePath).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR;
        self::$defaultSiteTitle=MArea::isBackend() ? 'Web3CMS Administrator' : 'Web3CMS';
        self::$defaultUrlToFiles=Yii::app()->request->baseUrl.'/files/';
        $data=Yii::app()->params;
        // system language
        self::setSystemLanguage(isset($data['systemLanguage']) ? $data['systemLanguage'] : self::_default);
        self::setAdminEmailAddress(isset($data['adminEmailAddress']) ? $data['adminEmailAddress'] : self::_default);
        self::setAdminEmailName(isset($data['adminEmailName']) ? $data['adminEmailName'] : self::_default);
        self::setAvailableInterfaces(isset($data['availableInterfaces']) ? $data['availableInterfaces'] : self::_default);
        self::setAvailableLanguages(isset($data['availableLanguages']) ? $data['availableLanguages'] : self::_default);
        self::setLanguage(isset($data['language']) ? $data['language'] : self::_default); // should be after setAvailableLanguages()
        self::setCopyrightBy(isset($data['copyrightBy']) ? $data['copyrightBy'] : self::_default);
        self::setHeaderTitle(isset($data['headerTitle']) ? $data['headerTitle'] : self::_default);
        self::setHtmlDoctype(isset($data['htmlDoctype']) ? $data['htmlDoctype'] : self::_default);
        self::setInterface(isset($data['interface']) ? $data['interface'] : self::_default);
        self::setMetaDescription(isset($data['metaDescription']) ? $data['metaDescription'] : self::_default);
        self::setMetaKeywords(isset($data['metaKeywords']) ? $data['metaKeywords'] : self::_default);
        self::setModelAttributes(isset($data['modelAttributes']) ? $data['modelAttributes'] : self::_default);
        self::setPageTitleFormula(isset($data['pageTitleFormula']) ? $data['pageTitleFormula'] : self::_default);
        self::setPathToFiles(isset($data['pathToFiles']) ? $data['pathToFiles'] : self::_default);
        self::setRegisterJqueryUI(isset($data['registerJqueryUI']) ? $data['registerJqueryUI'] : self::_default);
        self::setSiteTitle(isset($data['siteTitle']) ? $data['siteTitle'] : self::_default);
        self::setUrlToFiles(isset($data['urlToFiles']) ? $data['urlToFiles'] : self::_default);
        self::setUserLoginWithField(isset($data['userLoginWithField']) ? $data['userLoginWithField'] : self::_default);
    }

    /**
     * Internationalization - translate parameters at the end of _CController initialization.
     * 1. Load parameters.
     * 2. Set language (require parameters to be loaded).
     * 3. Translate loaded parameters.
     */
    public static function i18n()
    {
        // translate class default variables
        self::$defaultPageLabel=Yii::t('t','Home',array(1));
        // css themes array
        $array=self::getAvailableInterfaces();
        foreach($array as $n=>$item)
            $array[$n]=Yii::t('ui',$item);
        self::setAvailableInterfaces($array);
        // languages array
        $array=self::getAvailableLanguages();
        foreach($array as $n=>$item)
            $array[$n]=Yii::t('t',$item,array(0));
        self::setAvailableLanguages($array);
        // one line strings
        self::setHeaderTitle(Yii::t('t',self::getHeaderTitle()));
        self::setSiteTitle(Yii::t('t',self::getSiteTitle()));
    }

    /**
     * Save system logs
     * @param array $params
     */
    protected static function log($params=array())
    {
        if(!is_array($params))
            return;
        $append=isset($params['append']) ? ' '.$params['append'] : '';
        if(isset($params['method'],$params['parameter'],$params['value']))
        {
            Yii::log(W3::t('system',
                'Unacceptable value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'{$params['parameter']}'",
                    '{value}'=>var_export($params['value'],true),
                    '{method}'=>$params['method'].'()'
                )
            ).$append,'warning','w3');
        }
        else if(isset($params['method']) && (isset($params['value']) || isset($params['values'])))
        {
            if(isset($params['values']) && is_array($params['values']))
            {
                $value='';
                foreach($params['values'] as $item)
                    $value.=($value?',':'').var_export($item,true);
            }
            else
                $value=var_export((isset($params['value']) ? $params['value'] : $params['values']), true);
            Yii::log(W3::t('system',
                'Incorrect parameter in method call: {method}.',
                array('{method}'=>$params['method'].'('.$value.')')
            ).$append,'warning','w3');
        }
    }

    /**
     * Get the value of a parameter.
     * Parameter is case sensitive.
     * @param string $param
     * @return mixed
     */
    public static function get($param)
    {
        return isset(self::$_data[$param]) ? self::$_data[$param] : null;
    }

    /**
     * Set the value of a parameter.
     * Parameter is case sensitive.
     * @param string $param
     * @param mixed $value
     */
    public static function set($param,$value)
    {
        self::$_data[$param]=$value;
    }

    /**
     * Add to array of available interfaces (to choose from).
     * This array is used in e.g. create a member account.
     * @param array/string $value
     * @param bool $prepend. false = append
     */
    public static function addAvailableInterfaces($value,$prepend=false)
    {
        if($value===self::_default)
            $value=self::$defaultAvailableInterfaces;
        if(is_string($value) || is_numeric($value))
            // convert string to array
            $value=array($value=>$value);
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=array(); // clear the wrong value
        }
        if($prepend)
            $value=array_merge($value,self::getAvailableInterfaces());
        else
            $value=array_merge(self::getAvailableInterfaces(),$value);
        self::setAvailableInterfaces($value);
    }

    /**
     * Add to array of available languages (to choose from).
     * This array is used in e.g. create an user account.
     * @param array/string $value
     * @param bool $prepend. false = append
     */
    public static function addAvailableLanguages($value,$prepend=false)
    {
        if($value===self::_default)
            $value=self::$defaultAvailableLanguages;
        if(is_string($value) || is_numeric($value))
            // convert string to array
            $value=array($value=>$value);
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=array(); // clear the wrong value
        }
        if($prepend)
            $value=array_merge($value,self::getAvailableLanguages());
        else
            $value=array_merge(self::getAvailableLanguages(),$value);
        self::setAvailableLanguages($value);
    }

    /**
     * Add meta description.
     * html > head > meta[description]
     * @param string $value
     * @param string $delimiter
     */
    public static function addMetaDescription($value,$delimiter=' ')
    {
        if($value===self::_default)
            $value=self::defaultMetaDescription;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=''; // clear the wrong value
        }
        self::setMetaDescription(self::getMetaDescription().$delimiter.$value);
    }

    /**
     * Add meta keywords.
     * html > head > meta[keywords]
     * @param array/string $value
     * @param bool $prepend. false = append
     */
    public static function addMetaKeywords($value,$prepend=false)
    {
        if($value===self::_default)
            $value=self::$defaultMetaKeywords;
        if(is_string($value) || is_numeric($value))
            // convert string to array
            $value=array($value);
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=array(); // clear the wrong value
        }
        if($prepend)
            $value=array_merge($value,self::getMetaKeywords());
        else
            $value=array_merge(self::getMetaKeywords(),$value);
        self::setMetaKeywords($value);
    }

    /**
     * Remove from array of available interfaces (to choose from).
     * This array is used in e.g. create a member account.
     * @param array/string $value
     */
    public static function removeAvailableInterfaces($value)
    {
        if(is_string($value) || is_numeric($value))
            // convert string to array
            $value=array($value);
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=array(); // clear the wrong value
        }
        $array=self::getAvailableInterfaces();
        foreach($array as $n=>$item)
        {
            if(in_array($item,$value))
                unset($array[$n]);
        }
        self::setAvailableInterfaces($array);
    }

    /**
     * Remove from array of available languages (to choose from).
     * This array is used in e.g. create an user account.
     * @param array/string $value
     */
    public static function removeAvailableLanguages($value)
    {
        if(is_string($value) || is_numeric($value))
            // convert string to array
            $value=array($value);
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=array(); // clear the wrong value
        }
        $array=self::getAvailableLanguages();
        foreach($array as $n=>$item)
        {
            if(in_array($item,$value))
                unset($array[$n]);
        }
        self::setAvailableLanguages($array);
    }

    /**
     * Remove meta description.
     * html > head > meta[description]
     * @param string $value
     */
    public static function removeMetaDescription($value)
    {
        if($value===self::_default)
            $value=self::defaultMetaDescription;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=''; // clear the wrong value
        }
        self::setMetaDescription(str_replace($value,'',self::getMetaDescription()));
    }

    /**
     * Remove meta keywords.
     * html > head > meta[keywords]
     * @param array/string $value
     */
    public static function removeMetaKeywords($value)
    {
        if(is_string($value) || is_numeric($value))
            // convert string to array
            $value=array($value);
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=array(); // clear the wrong value
        }
        $array=self::getMetaKeywords();
        foreach($array as $n=>$item)
        {
            if(in_array($item,$value))
                unset($array[$n]);
        }
        self::setMetaKeywords($array);
    }

    /**
     * Remove model attributes from system usage.
     * For these attributes $model->hasVirtualAttribute() will return false.
     * @param string $model
     * @param array/string $value
     */
    public static function removeModelAttributes($model,$value)
    {
        if(is_string($value) || is_numeric($value))
            // convert string to array
            $value=array($value);
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'values'=>array($model,$value)));
            $value=array(); // clear the wrong value
        }
        $array=self::getModelAttributes();
        foreach($value as $attribute)
            $array[$model][$attribute]=false;
        self::setModelAttributes($array);
    }

    /**
     * Restore model attributes for system usage.
     * For these attributes $model->hasVirtualAttribute() will return true.
     * @param string $model
     * @param array/string $value
     */
    public static function restoreModelAttributes($model,$value)
    {
        if(is_string($value) || is_numeric($value))
            // convert string to array
            $value=array($value);
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'values'=>array($model,$value)));
            $value=array(); // clear the wrong value
        }
        $array=self::getModelAttributes();
        foreach($value as $attribute)
            $array[$model][$attribute]=true;
        self::setModelAttributes($array);
    }

    /**
     * From: "adminEmailName" <adminEmailAddress>
     * @return string
     */
    public static function getAdminEmailAddress()
    {
        $value=self::$adminEmailAddress;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'adminEmailAddress','value'=>$value));
            $value=self::defaultAdminEmailAddress; // set the wrong value to default
        }
        return $value;
    }

    /**
     * From: "adminEmailName" <adminEmailAddress>
     * @return string
     */
    public static function getAdminEmailName()
    {
        $value=self::$adminEmailName;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'adminEmailName','value'=>$value));
            $value=self::defaultAdminEmailName; // set the wrong value to default
        }
        return $value;
    }

    /**
     * Get array of available css themes (to choose from).
     * This array is used in e.g. create an user account.
     * @return array
     */
    public static function getAvailableInterfaces()
    {
        $value=self::$availableInterfaces;
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'availableInterfaces','value'=>$value));
            $value=self::$defaultAvailableInterfaces; // set the wrong value to default
        }
        return $value;
    }

    /**
     * Get array of available languages (to choose from).
     * This array is used in e.g. create an user account.
     * @return array
     */
    public static function getAvailableLanguages()
    {
        $value=self::$availableLanguages;
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'availableLanguages','value'=>$value));
            $value=self::$defaultAvailableLanguages; // set the wrong value to default
        }
        return $value;
    }

    /**
     * Displayed in the footer of every page.
     * Copyright © 2009 by {copyrightBy}. All Rights Reserved.
     * @return string
     */
    public static function getCopyrightBy()
    {
        $value=self::$copyrightBy;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'copyrightBy','value'=>$value));
            $value=self::defaultCopyrightBy; // set the wrong value to default
        }
        return $value;
    }

    /**
     * Title of your cms, displayed in the header section (above menu).
     * @return string
     */
    public static function getHeaderTitle()
    {
        $value=self::$headerTitle;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'headerTitle','value'=>$value));
            $value=self::$defaultHeaderTitle; // set the wrong value to default
        }
        return $value;
    }

    /**
     * Html document type.
     * <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     * @return string
     */
    public static function getHtmlDoctype()
    {
        $value=self::$htmlDoctype;
        if($value===true || !in_array($value,self::$allowedHtmlDoctype))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'htmlDoctype','value'=>$value));
            $value=self::defaultHtmlDoctype; // set the wrong value to default
        }
        return $value;
    }

    /**
     * jQuery UI CSS Framework.
     * Path is {root}/css/ui/{interface}
     * @return string
     */
    public static function getInterface()
    {
        $value=self::$interface;
        $availableInterfaces=self::getAvailableInterfaces();
        if((!is_string($value) && !is_int($value)) || !array_key_exists($value,$availableInterfaces) || !MPath::interfaceExists($value))
        {
            $append=W3::t('system','Available interfaces: {availableInterfaces}.',array(
                '{availableInterfaces}'=>var_export($availableInterfaces,true)
            ));
            self::log(array('method'=>__METHOD__,'parameter'=>'interface','value'=>$value,'append'=>$append));
            $value=self::defaultInterface; // set the wrong value to default
        }
        return $value;
    }

    /**
     * Site language.
     * @return string
     */
    public static function getLanguage()
    {
        $value=self::$language;
        $availableLanguages=self::getAvailableLanguages();
        if((!is_string($value) && !is_int($value)) || !array_key_exists($value,$availableLanguages))
        {
            $append=W3::t('system','Available languages: {availableLanguages}.',array(
                '{availableLanguages}'=>var_export($availableLanguages,true)
            ));
            self::log(array('method'=>__METHOD__,'parameter'=>'language','value'=>$value,'append'=>$append));
            $value=self::defaultLanguage; // set the wrong value to default
        }
        return $value;
    }

    /**
     * html > head > meta[description]
     * @return string
     */
    public static function getMetaDescription()
    {
        $value=self::$metaDescription;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'metaDescription','value'=>$value));
            $value=self::defaultMetaDescription; // set the wrong value to default
        }
        return $value;
    }

    /**
     * html > head > meta[keywords]
     * @return array
     */
    public static function getMetaKeywords()
    {
        $value=self::$metaKeywords;
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'metaKeywords','value'=>$value));
            $value=self::$defaultMetaKeywords; // set the wrong value to default
        }
        return $value;
    }

    /**
     * String representation of {@link MParams::getMetaKeywords()} array.
     * html > head > meta[keywords]
     * @param string $glue
     * @return string
     */
    public static function getMetaKeywordsAsString($glue=', ')
    {
        if(!is_string($glue) && !is_numeric($glue))
        {
            self::log(array('method'=>__METHOD__,'value'=>$glue));
            $glue=', '; // set the wrong value to default
        }
        $value=implode($glue,self::getMetaKeywords());
        return $value;
    }

    /**
     * Array of model attributes that system should (not) use.
     * @param string $model
     * @param string $attribute
     * @return mixed
     */
    public static function getModelAttributes($model=null,$attribute=null)
    {
        $value=self::$modelAttributes;
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'modelAttributes','value'=>$value));
            $value=self::$defaultModelAttributes; // set the wrong value to default
        }
        if(!is_null($model))
        {
            if(isset($value[$model]))
                // attributes of a specific model
                $value=$value[$model];
            else
                $value=array();
        }
        if(!is_null($attribute))
        {
            if(isset($value[$attribute]))
                // specific attribute
                $value=$value[$attribute];
            else
                $value=null;
        }
        return $value;
    }

    /**
     * Page label, displayed in the top of content area,
     * right after breadcrumbs.
     * <h1 class="w3-page-label">{pageLabel}</h1>
     * @return string
     */
    public static function getPageLabel()
    {
        $value=self::$pageLabel;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'pageLabel','value'=>$value));
            $value=self::$defaultPageLabel; // set the wrong value to default
        }
        return $value;
    }

    /**
     * Page title formula, used by {@link MParams::setPageTitle()} to set
     * html > head > title
     * @return string
     */
    public static function getPageTitleFormula()
    {
        $value=self::$pageTitleFormula;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'pageTitleFormula','value'=>$value));
            $value=self::defaultPageTitleFormula; // set the wrong value to default
        }
        return $value;
    }

    /**
     * HDD path to files folder.
     * Must be a valid directory accessible within your hosting.
     * @return string
     */
    public static function getPathToFiles()
    {
        $value=self::$pathToFiles;
        if(!is_dir($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'pathToFiles','value'=>$value));
            $value=self::$defaultPathToFiles; // set the wrong value to default
        }
        return $value;
    }

    /**
     * Whether or not register jquery-ui css in {@link W3Init::css()}.
     * @return string
     */
    public static function getRegisterJqueryUI()
    {
        $value=self::$registerJqueryUI;
        if(!is_bool($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'registerJqueryUI','value'=>$value));
            $value=self::defaultRegisterJqueryUI; // set the wrong value to default
        }
        return $value;
    }

    /**
     * Title of your site.
     * html > head > title
     * @return string
     */
    public static function getSiteTitle()
    {
        $value=self::$siteTitle;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'siteTitle','value'=>$value));
            $value=self::$defaultSiteTitle; // set the wrong value to default
        }
        return $value;
    }

    /**
     * System language - language for system messages (mostly in logs).
     * @return string
     */
    public static function getSystemLanguage()
    {
        $value=self::$systemLanguage;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'systemLanguage','value'=>$value));
            $value=self::defaultSystemLanguage; // set the wrong value to default
        }
        return $value;
    }

    /**
     * Web-accessible url to files directory.
     * Supposed to begin with either http:// or / (slash). 
     * Should contain trailing slash.
     * @return string
     */
    public static function getUrlToFiles()
    {
        $value=self::$urlToFiles;
        if(empty($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'urlToFiles','value'=>$value));
            $value=self::$defaultUrlToFiles; // set the wrong value to default
        }
        return $value;
    }

    /**
     * Which field to log user in with.
     * Is one of username/email/_any_
     * @return string
     */
    public static function getUserLoginWithField()
    {
        $value=self::$userLoginWithField;
        if($value===true || !in_array($value,self::$allowedUserLoginWithField))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'userLoginWithField','value'=>$value));
            $value=self::defaultUserLoginWithField; // set the wrong value to default
        }
        return $value;
    }

    /**
     * From: "adminEmailName" <adminEmailAddress>
     * @param string $value
     */
    public static function setAdminEmailAddress($value)
    {
        if($value===self::_default)
            $value=self::defaultAdminEmailAddress;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::defaultAdminEmailAddress; // set the wrong value to default
        }
        self::$adminEmailAddress=$value;
    }

    /**
     * From: "adminEmailName" <adminEmailAddress>
     * @param string $value
     */
    public static function setAdminEmailName($value)
    {
        if($value===self::_default)
            $value=self::defaultAdminEmailName;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::defaultAdminEmailName; // set the wrong value to default
        }
        self::$adminEmailName=$value;
    }

    /**
     * Set array of available interfaces (to choose from).
     * This array is used in e.g. create a member account.
     * @param array $value
     */
    public static function setAvailableInterfaces($value)
    {
        if($value===self::_default)
            $value=self::$defaultAvailableInterfaces;
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::$defaultAvailableInterfaces; // set the wrong value to default
        }
        $value=array_unique($value); // this array should not contain duplicate entries
        self::$availableInterfaces=$value;
    }

    /**
     * Set array of available languages (to choose from).
     * This array is used in e.g. create an user account.
     * @param array $value
     */
    public static function setAvailableLanguages($value)
    {
        if($value===self::_default)
            $value=self::$defaultAvailableLanguages;
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::$defaultAvailableLanguages; // set the wrong value to default
        }
        $value=array_unique($value); // this array should not contain duplicate entries
        self::$availableLanguages=$value;
    }

    /**
     * Displayed in the footer of every page.
     * Copyright © 2009 by {copyrightBy}. All Rights Reserved.
     * @param string $value
     */
    public static function setCopyrightBy($value)
    {
        if($value===self::_default)
            $value=self::defaultCopyrightBy;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::defaultCopyrightBy; // set the wrong value to default
        }
        self::$copyrightBy=$value;
    }

    /**
     * Title of your cms, displayed in the header section (above menu).
     * @param string $value
     */
    public static function setHeaderTitle($value)
    {
        if($value===self::_default)
            $value=self::$defaultHeaderTitle;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::$defaultHeaderTitle; // set the wrong value to default
        }
        self::$headerTitle=$value;
    }

    /**
     * Html document type.
     * <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     * @param string $value
     */
    public static function setHtmlDoctype($value)
    {
        if($value===self::_default)
            $value=self::defaultHtmlDoctype;
        else if($value===true || !in_array($value,self::$allowedHtmlDoctype))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::defaultHtmlDoctype; // set the wrong value to default
        }
        self::$htmlDoctype=$value;
    }

    /**
     * jQuery UI CSS Framework.
     * Path is {root}/css/ui/{interface}
     * @param string $value
     */
    public static function setInterface($value)
    {
        $availableInterfaces=self::getAvailableInterfaces();
        if($value===self::_default)
        {
            if(MPath::interfaceExists(self::defaultInterface))
                $value=self::defaultInterface;
            else
            {
                self::log(array('method'=>__METHOD__,'value'=>$value,'append'=>self::defaultInterface));
                return false;
            }
        }
        // to set an interface that is not in availableInterfaces, use {@link MParams::setRegisterJqueryUI(false)}
        else if((!is_string($value) && !is_int($value)) || !array_key_exists($value,$availableInterfaces) || !MPath::interfaceExists($value))
        {
            $append=W3::t('system','Available interfaces: {availableInterfaces}.',array(
                '{availableInterfaces}'=>var_export($availableInterfaces,true)
            ));
            self::log(array('method'=>__METHOD__,'value'=>$value,'append'=>$append));
            $value=self::defaultInterface; // set the wrong value to default
        }
        self::$interface=$value;
    }

    /**
     * Site language.
     * @param string $value
     */
    public static function setLanguage($value)
    {
        $availableLanguages=self::getAvailableLanguages();
        if($value===self::_default)
            $value=self::defaultLanguage;
        else if((!is_string($value) && !is_int($value)) || !array_key_exists($value,$availableLanguages))
        {
            $append=W3::t('system','Available languages: {availableLanguages}.',array(
                '{availableLanguages}'=>var_export($availableLanguages,true)
            ));
            self::log(array('method'=>__METHOD__,'value'=>$value,'append'=>$append));
            $value=self::defaultLanguage; // set the wrong value to default
        }
        self::$language=$value;
        Yii::app()->language=$value;
    }

    /**
     * html > head > meta[description]
     * @param string $value
     */
    public static function setMetaDescription($value)
    {
        if($value===self::_default)
            $value=self::defaultMetaDescription;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::defaultMetaDescription; // set the wrong value to default
        }
        self::$metaDescription=$value;
    }

    /**
     * html > head > meta[keywords]
     * @param array $value
     */
    public static function setMetaKeywords($value)
    {
        if($value===self::_default)
            $value=self::$defaultMetaKeywords;
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::$defaultMetaKeywords; // set the wrong value to default
        }
        $value=array_unique($value); // this array should not contain duplicate entries
        self::$metaKeywords=$value;
    }

    /**
     * Array of model attributes that system should (not) use.
     * @param array $value
     * @param string $model
     */
    public static function setModelAttributes($value,$model=null)
    {
        $setToDefault=false;
        if($value===self::_default)
        {
            $value=self::$defaultModelAttributes;
            $setToDefault=true;
        }
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'values'=>array($value,$model)));
            $value=self::$defaultModelAttributes; // set the wrong value to default
            $setToDefault=true;
        }
        $value=array_unique($value); // this array should not contain duplicate entries
        if(is_null($model))
            // set array of models
            self::$modelAttributes=$value;
        else if(!$setToDefault)
            // set array of model attributes
            self::$modelAttributes[$model]=$value;
    }

    /**
     * Page label, displayed in the top of content area,
     * right after breadcrumbs.
     * <h1 class="w3-page-label">{pageLabel}</h1>
     * @param string $value
     */
    public static function setPageLabel($value)
    {
        if($value===self::_default)
            $value=self::$defaultPageLabel;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::$defaultPageLabel; // set the wrong value to default
        }
        self::$pageLabel=$value;
        // got a param involved in page title => set it
        self::setPageTitle();
    }

    /**
     * Set page title, based on MParams::$pageTitleFormula
     * html > head > title
     * {siteTitle} correspond MParams::$siteTitle
     * {pageLabel} correspond MParams::$pageLabel
     */
    public static function setPageTitle()
    {
        // home page might have no page label
        $title=is_null(self::$pageLabel)? self::getSiteTitle() : strtr(self::getPageTitleFormula(),array(
            '{siteTitle}'=>self::getSiteTitle(),
            '{pageLabel}'=>self::getPageLabel()
        ));
        Yii::app()->controller->setPageTitle($title);
    }

    /**
     * Page title formula, used by {@link MParams::setPageTitle()} to set
     * html > head > title
     * @param string $value
     */
    public static function setPageTitleFormula($value)
    {
        if($value===self::_default)
            $value=self::defaultPageTitleFormula;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::defaultPageTitleFormula; // set the wrong value to default
        }
        self::$pageTitleFormula=$value;
    }

    /**
     * HDD path to files folder.
     * Must be a valid directory accessible within your hosting.
     * @param string $value
     */
    public static function setPathToFiles($value)
    {
        if($value===self::_default)
            $value=self::$defaultPathToFiles;
        if(!is_dir($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::$defaultPathToFiles; // set the wrong value to default
        }
        self::$pathToFiles=$value;
    }

    /**
     * Whether or not register jquery-ui css in {@link W3Init::css()}.
     * @param string $value
     */
    public static function setRegisterJqueryUI($value)
    {
        if($value===self::_default)
            $value=self::defaultRegisterJqueryUI;
        if(!is_bool($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::defaultRegisterJqueryUI; // set the wrong value to default
        }
        self::$registerJqueryUI=$value;
    }

    /**
     * Title of your site.
     * html > head > title
     * @param string $value
     */
    public static function setSiteTitle($value)
    {
        if($value===self::_default)
            $value=self::$defaultSiteTitle;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::$defaultSiteTitle; // set the wrong value to default
        }
        self::$siteTitle=$value;
        // got a param involved in page title => set it
        self::setPageTitle();
    }

    /**
     * System language - language for system messages (mostly in logs).
     * @param string $value
     */
    public static function setSystemLanguage($value)
    {
        if($value===self::_default)
            $value=self::defaultSystemLanguage;
        if(!is_string($value) && !is_numeric($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::defaultSystemLanguage; // set the wrong value to default
        }
        self::$systemLanguage=$value;
    }

    /**
     * Web-accessible url to files directory.
     * Supposed to begin with either http:// or / (slash). 
     * Should contain trailing slash.
     * @param string $value
     */
    public static function setUrlToFiles($value)
    {
        if($value===self::_default)
            $value=self::$defaultUrlToFiles;
        else if(empty($value))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::$defaultUrlToFiles; // set the wrong value to default
        }
        self::$urlToFiles=$value;
    }

    /**
     * Which field to log user in with.
     * Should be one of username/email/_any_
     * @param string $value
     */
    public static function setUserLoginWithField($value)
    {
        if($value===self::_default)
            $value=self::defaultUserLoginWithField;
        else if($value===true || !in_array($value,self::$allowedUserLoginWithField))
        {
            self::log(array('method'=>__METHOD__,'value'=>$value));
            $value=self::defaultUserLoginWithField; // set the wrong value to default
        }
        self::$userLoginWithField=$value;
    }
}