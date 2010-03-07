<?php
/**
 * Manage Site Parameters.
 * If a parameter has a wrong type or it's out of allowed values,
 * then default value is applied.
 * In the beginning all parameters are loaded from {@link _CController::init},
 * then strings are translated (after _CController has set preferred language).
 */
class MParams
{
    /**
     * Whether class is loaded (it is loaded when {@link load} was called).
     * @var bool
     */
    public static $isLoaded;

    /**
     * Array of your custom parameters.
     * Is being accessed using public functions {@link get} and {@link set}.
     * @var array
     */
    protected static $data;

    /**
     * Array of web3cms core parameters.
     * Is being accessed using private functions {@link _getCore} and {@link _setCore}.
     * @var array
     */
    private static $coreData;

    /**
     * Array of core default parameters.
     * Is being used in {@link load} when a parameter in config/params.php is missed.
     * @var array
     */
    private static $coreDefaultData;

    /**
     * Array of core parameters allowed values.
     * @var array
     */
    private static $coreDataAllowedValue;

    /**
     * Constants.
     */
    const _default='_default';
    const jqueryUIVersion='1.7.2';

    /**
     * Load params from Yii::app()->params into class properties.
     */
    public static function load()
    {
        // initialize core default parameters
        self::$coreDefaultData=array(
            /**
             * From: "adminEmailName" <adminEmailAddress>
             */
            'adminEmailAddress'=>'phpdevmd@web3cms.com',
            /**
             * From: "adminEmailName" <adminEmailAddress>
             */
            'adminEmailName'=>'Web3CMS Staff',
            /**
             * Set array of available interfaces (to choose from).
             * This array is used in e.g. create a member account.
             */
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
            /**
             * Get array of available languages (to choose from).
             * This array is used in e.g. create an user account.
             */
            'availableLanguages'=>array(
                'en'=>'English',
                'ru'=>'Russian',
            ),
            /**
             * Displayed in the footer of every page.
             * Copyright © 2009 by {copyrightBy}. All Rights Reserved.
             */
            'copyrightBy'=>'My Company',
            /**
             * Title of your cms, displayed in the header section (above menu).
             */
            'headerTitle'=>MArea::isBackend() ? 'Web3CMS Administrator Area' : 'My Web3CMS',
            /**
             * Html document type.
             * <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
             */
            'htmlDoctype'=>'transitional',
            /**
             * jQuery UI CSS Framework.
             * Path is {root}/css/ui/{interface}
             */
            'interface'=>'start',
            /**
             * Site language.
             */
            'language'=>'en',
            /**
             * Whether or not main menu box should be 100% of the page width (not just 960px).
             */
            'mainMenuFullWidth'=>true,
            /**
             * html > head > meta[description]
             */
            'metaDescription'=>'Web3CMS - Web 2.0 Content Management System based on Yii Framework.',
            /**
             * html > head > meta[keywords]
             */
            'metaKeywords'=>array('web3cms','yii'),
            /**
             * Page label, displayed in the top of content area,
             * right after breadcrumbs.
             * <h1 class="w3-page-label">{pageLabel}</h1>
             */
            'pageLabel'=>'Home',
            /**
             * Page title formula, used by {@link setPageTitle} to set
             * html > head > title
             */
            'pageTitleFormula'=>'{pageLabel} - {siteTitle}',
            /**
             * HDD path to files folder.
             * Must be a valid directory accessible within your hosting.
             */
            'pathToFiles'=>dirname(Yii::app()->basePath).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR,
            /**
             * Whether or not register jquery-ui css in {@link W3Init::css}.
             */
            'registerJqueryUI'=>true,
            /**
             * Title of your site.
             * html > head > title
             */
            'siteTitle'=>MArea::isBackend() ? 'Web3CMS Administrator' : 'Web3CMS',
            /**
             * System language - language for system messages (mostly in logs).
             */
            'systemLanguage'=>'en',
            /**
             * Database table prefix, used by {@link _CActiveRecord}.
             */
            'tablePrefix'=>'w3_',
            /**
             * Web-accessible url to files directory.
             * Supposed to begin with either http:// or / (slash). 
             * Should contain trailing slash.
             */
            'urlToFiles'=>Yii::app()->request->baseUrl.'/files/',
            /**
             * Which field to log user in with.
             * Should be one of username/email/_any_
             */
            'userLoginWithField'=>'username',
        );
        // initialize core parameters allowed values
        self::$coreDataAllowedValue=array(
            'htmlDoctype'=>array('strict','transitional'),
            'modelAttributes'=>array(
                'User'=>array(
                    'email2'=>false,
                )
            ),
            'userLoginWithField'=>array('_any_','email','username'),
        );
        // set data from params.php
        $data=Yii::app()->params;
        $parameters=array(
            'systemLanguage', // should be first
            'adminEmailAddress',
            'adminEmailName',
            'availableInterfaces',
            'availableLanguages',
            'language', // should go after availableLanguages
            'copyrightBy',
            'headerTitle',
            'htmlDoctype',
            'interface',
            'mainMenuFullWidth',
            'metaDescription',
            'metaKeywords',
            'modelAttributes',
            'pageTitleFormula',
            'pathToFiles',
            'registerJqueryUI',
            'siteTitle',
            'tablePrefix',
            'urlToFiles',
            'userLoginWithField'
        );
        // our universal setters
        foreach($parameters as $parameter)
        {
            $setter='set'.ucfirst($parameter);
            if(isset($data[$parameter]))
                call_user_func(array('self',$setter),$data[$parameter]);
            else
            {
                call_user_func(array('self',$setter),self::_default);
                Yii::log(W3::t('system','Missing parameter in file params.php: {parameter}.',array('{parameter}'=>$parameter)),'error','w3');
            }
        }
        // class is loaded
        self::$isLoaded=true;
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
        self::$coreDefaultData['pageLabel']=Yii::t('link','Home');
        // css themes array
        $array=self::getAvailableInterfaces();
        foreach($array as $n=>$item)
            $array[$n]=Yii::t('ui',$item);
        self::setAvailableInterfaces($array);
        // languages array
        $array=self::getAvailableLanguages();
        foreach($array as $n=>$item)
            $array[$n]=Yii::t('t',$item.'[language]');
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
     * Get the value of a parameter. Parameter is case sensitive.
     * @param string $parameter
     * @return mixed
     */
    public static function get($parameter)
    {
        return isset(self::$data[$parameter]) ? self::$data[$parameter] : null;
    }

    /**
     * Get the value of a core parameter. Parameter is case sensitive.
     * @param string $parameter
     * @return mixed
     */
    private static function _getCore($parameter)
    {
        if(!is_string($parameter))
            return null;
        $value=self::$coreData[$parameter];
        // validate value
        if((in_array($parameter,array('adminEmailAddress','adminEmailName','copyrightBy','headerTitle','metaDescription','pageLabel','pageTitleFormula','siteTitle','systemLanguage','tablePrefix'))
            && !is_string($value) && !is_numeric($value))
        || (in_array($parameter,array('mainMenuFullWidth','registerJqueryUI'))
            && !is_bool($value))
        || (in_array($parameter,array('availableInterfaces','availableLanguages','metaKeywords'))
            && !is_array($value))
        || (in_array($parameter,array('urlToFiles'))
            && empty($value))
        || (in_array($parameter,array('htmlDoctype','userLoginWithField'))
            && ($value===true || !in_array($value,self::$coreDataAllowedValue[$parameter])))
        || (in_array($parameter,array('pathToFiles'))
            && !is_dir($value))
        )
        {
            self::log(array('method'=>__METHOD__,'parameter'=>$parameter,'value'=>$value));
            $value=self::$coreDefaultData[$parameter]; // set the inappropriate value to default
        }
        return $value;
    }
    
    /**
     * Public aliases of private function {@link _getCore}.
     * @return mixed
     */
    public static function getAdminEmailAddress()
    {
        return self::_getCore('adminEmailAddress');
    }
    public static function getAdminEmailName()
    {
        return self::_getCore('adminEmailName');
    }
    public static function getAvailableInterfaces()
    {
        return self::_getCore('availableInterfaces');
    }
    public static function getAvailableLanguages()
    {
        return self::_getCore('availableLanguages');
    }
    public static function getCopyrightBy()
    {
        return self::_getCore('copyrightBy');
    }
    public static function getHeaderTitle()
    {
        return self::_getCore('headerTitle');
    }
    public static function getHtmlDoctype()
    {
        return self::_getCore('htmlDoctype');
    }
    public static function getMainMenuFullWidth()
    {
        return self::_getCore('mainMenuFullWidth');
    }
    public static function getMetaDescription()
    {
        return self::_getCore('metaDescription');
    }
    public static function getMetaKeywords()
    {
        return self::_getCore('metaKeywords');
    }
    public static function getPageLabel()
    {
        return self::_getCore('pageLabel');
    }
    public static function getPageTitleFormula()
    {
        return self::_getCore('pageTitleFormula');
    }
    public static function getPathToFiles()
    {
        return self::_getCore('pathToFiles');
    }
    public static function getRegisterJqueryUI()
    {
        return self::_getCore('registerJqueryUI');
    }
    public static function getSiteTitle()
    {
        return self::_getCore('siteTitle');
    }
    public static function getSystemLanguage()
    {
        return self::_getCore('systemLanguage');
    }
    public static function getTablePrefix()
    {
        return self::_getCore('tablePrefix');
    }
    public static function getUrlToFiles()
    {
        return self::_getCore('urlToFiles');
    }
    public static function getUserLoginWithField()
    {
        return self::_getCore('userLoginWithField');
    }

    /**
     * Set the value of a parameter. Parameter is case sensitive.
     * @param string $parameter
     * @param mixed $value
     */
    public static function set($parameter,$value)
    {
        self::$data[$parameter]=$value;
    }

    /**
     * Set the value of a core parameter. Parameter is case sensitive.
     * @param string $parameter
     * @param mixed $value
     */
    private static function _setCore($parameter,$value)
    {
        if(!is_string($parameter))
            return null;
        if($value===self::_default)
            // we are asking to set the default value
            $value=self::$coreDefaultData[$parameter];
        // validate value
        if((in_array($parameter,array('adminEmailAddress','adminEmailName','copyrightBy','headerTitle','metaDescription','pageLabel','pageTitleFormula','siteTitle','systemLanguage','tablePrefix'))
            && !is_string($value) && !is_numeric($value))
        || (in_array($parameter,array('mainMenuFullWidth','registerJqueryUI'))
            && !is_bool($value))
        || (in_array($parameter,array('availableInterfaces','availableLanguages','metaKeywords'))
            && !is_array($value))
        || (in_array($parameter,array('urlToFiles'))
            && empty($value))
        || (in_array($parameter,array('htmlDoctype','userLoginWithField'))
            && ($value===true || !in_array($value,self::$coreDataAllowedValue[$parameter])))
        || (in_array($parameter,array('pathToFiles'))
            && !is_dir($value))
        )
        {
            self::log(array('method'=>__METHOD__,'values'=>array($parameter,$value)));
            $value=self::$coreDefaultData[$parameter]; // set the inappropriate value to default
        }
        // set
        self::$coreData[$parameter]=$value;
        // parameter custom code
        if(in_array($parameter,array('availableInterfaces','availableLanguages','metaKeywords')))
            self::$coreData[$parameter]=array_unique(self::$coreData[$parameter]); // this array should not contain duplicate entries
        else if($parameter==='pageLabel' || $parameter==='siteTitle')
            // got a param involved in page title => set it
            self::setPageTitle();
    }

    /**
     * Public aliases of private function {@link _setCore}.
     * @param mixed $value
     */
    public static function setAdminEmailAddress($value)
    {
        self::_setCore('adminEmailAddress',$value);
    }
    public static function setAdminEmailName($value)
    {
        self::_setCore('adminEmailName',$value);
    }
    public static function setAvailableInterfaces($value)
    {
        self::_setCore('availableInterfaces',$value);
    }
    public static function setAvailableLanguages($value)
    {
        self::_setCore('availableLanguages',$value);
    }
    public static function setCopyrightBy($value)
    {
        self::_setCore('copyrightBy',$value);
    }
    public static function setHeaderTitle($value)
    {
        self::_setCore('headerTitle',$value);
    }
    public static function setHtmlDoctype($value)
    {
        self::_setCore('htmlDoctype',$value);
    }
    public static function setMainMenuFullWidth($value)
    {
        self::_setCore('mainMenuFullWidth',$value);
    }
    public static function setMetaDescription($value)
    {
        self::_setCore('metaDescription',$value);
    }
    public static function setMetaKeywords($value)
    {
        self::_setCore('metaKeywords',$value);
    }
    public static function setPageLabel($value)
    {
        self::_setCore('pageLabel',$value);
    }
    public static function setPageTitleFormula($value)
    {
        self::_setCore('pageTitleFormula',$value);
    }
    public static function setPathToFiles($value)
    {
        self::_setCore('pathToFiles',$value);
    }
    public static function setRegisterJqueryUI($value)
    {
        self::_setCore('registerJqueryUI',$value);
    }
    public static function setSiteTitle($value)
    {
        self::_setCore('siteTitle',$value);
    }
    public static function setSystemLanguage($value)
    {
        self::_setCore('systemLanguage',$value);
    }
    public static function setTablePrefix($value)
    {
        self::_setCore('tablePrefix',$value);
    }
    public static function setUrlToFiles($value)
    {
        self::_setCore('urlToFiles',$value);
    }
    public static function setUserLoginWithField($value)
    {
        self::_setCore('userLoginWithField',$value);
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
     * jQuery UI CSS Framework.
     * Path is {root}/css/ui/{interface}
     * @return string
     */
    public static function getInterface()
    {
        $value=self::$coreData['interface'];
        $availableInterfaces=self::getAvailableInterfaces();
        if((!is_string($value) && !is_int($value)) || !array_key_exists($value,$availableInterfaces) || !MPath::interfaceExists($value))
        {
            $append=W3::t('system','Available interfaces: {availableInterfaces}.',array(
                '{availableInterfaces}'=>var_export($availableInterfaces,true)
            ));
            self::log(array('method'=>__METHOD__,'parameter'=>'interface','value'=>$value,'append'=>$append));
            $value=self::$coreDefaultData['interface']; // set the wrong value to default
        }
        return $value;
    }

    /**
     * Site language.
     * @return string
     */
    public static function getLanguage()
    {
        $value=self::$coreData['language'];
        $availableLanguages=self::getAvailableLanguages();
        if((!is_string($value) && !is_int($value)) || !array_key_exists($value,$availableLanguages))
        {
            $append=W3::t('system','Available languages: {availableLanguages}.',array(
                '{availableLanguages}'=>var_export($availableLanguages,true)
            ));
            self::log(array('method'=>__METHOD__,'parameter'=>'language','value'=>$value,'append'=>$append));
            $value=self::$coreDefaultData['language']; // set the wrong value to default
        }
        return $value;
    }

    /**
     * String representation of {@link getMetaKeywords} array.
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
        $value=self::$coreData['modelAttributes'];
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'parameter'=>'modelAttributes','value'=>$value));
            $value=self::$coreDataAllowedValue['modelAttributes']; // set the wrong value to default
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
     * jQuery UI CSS Framework.
     * Path is {root}/css/ui/{interface}
     * @param string $value
     */
    public static function setInterface($value)
    {
        $availableInterfaces=self::getAvailableInterfaces();
        if($value===self::_default)
        {
            if(MPath::interfaceExists(self::$coreDefaultData['interface']))
                $value=self::$coreDefaultData['interface'];
            else
            {
                self::log(array('method'=>__METHOD__,'value'=>$value,'append'=>self::$coreDefaultData['interface']));
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
            $value=self::$coreDefaultData['interface']; // set the wrong value to default
        }
        self::$coreData['interface']=$value;
    }

    /**
     * Site language.
     * @param string $value
     */
    public static function setLanguage($value)
    {
        $availableLanguages=self::getAvailableLanguages();
        if($value===self::_default)
            $value=self::$coreDefaultData['language'];
        else if((!is_string($value) && !is_int($value)) || !array_key_exists($value,$availableLanguages))
        {
            $append=W3::t('system','Available languages: {availableLanguages}.',array(
                '{availableLanguages}'=>var_export($availableLanguages,true)
            ));
            self::log(array('method'=>__METHOD__,'value'=>$value,'append'=>$append));
            $value=self::$coreDefaultData['language']; // set the wrong value to default
        }
        self::$coreData['language']=$value;
        Yii::app()->language=$value;
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
            $value=self::$coreDataAllowedValue['modelAttributes'];
            $setToDefault=true;
        }
        if(!is_array($value))
        {
            self::log(array('method'=>__METHOD__,'values'=>array($value,$model)));
            $value=self::$coreDataAllowedValue['modelAttributes']; // set the wrong value to default
            $setToDefault=true;
        }
        $value=array_unique($value); // this array should not contain duplicate entries
        if(is_null($model))
            // set array of models
            self::$coreData['modelAttributes']=$value;
        else if(!$setToDefault)
            // set array of model attributes
            self::$coreData['modelAttributes'][$model]=$value;
    }

    /**
     * Set page title, based on pageTitleFormula
     * html > head > title
     */
    public static function setPageTitle()
    {
        // home page might have no page label
        $title=!isset(self::$coreData['pageLabel']) ? self::getSiteTitle() : strtr(self::getPageTitleFormula(),array(
            '{siteTitle}'=>self::getSiteTitle(),
            '{pageLabel}'=>self::getPageLabel()
        ));
        Yii::app()->controller->setPageTitle($title);
    }
}