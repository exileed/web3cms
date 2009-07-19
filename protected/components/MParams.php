<?php
/**
 * Manage Params
 */
class MParams
{
    protected static $adminEmailAddress;
    protected static $adminEmailName;
    protected static $copyrightBy;
    protected static $cssTheme;
    protected static $headerTitle;
    protected static $htmlDoctype;
    protected static $metaDescription;
    protected static $metaKeywords;
    protected static $pageLabel;
    protected static $pageTitleFormula;
    protected static $pathToFiles;
    protected static $siteTitle;
    protected static $urlToFiles;
    protected static $userLoginWithField;
    protected static $__data;
    protected static $allowedHtmlDoctype;
    protected static $allowedUserLoginWithField;
    protected static $defaultMetaKeywords;
    protected static $defaultPathToFiles;
    protected static $defaultUrlToFiles;
    const __default='__default';
    const jqueryUIVersion='1.7.2';
    const defaultAdminEmailAddress='phpdevmd@web3cms.com';
    const defaultAdminEmailName='Web3CMS Staff';
    const defaultCopyrightBy='My Company';
    const defaultCssTheme='start';
    const defaultHeaderTitle='My Web3CMS';
    const defaultHtmlDoctype='transitional';
    const defaultMetaDescription='Web3CMS - Web 2.0 Content Management System based on Yii Framework.';
    const defaultPageLabel='Home';
    const defaultPageTitleFormula='{pageLabel} - {siteTitle}';
    const defaultSiteTitle='Web3CMS';
    const defaultUserLoginWithField='username';

    /**
    * Load params from Yii::app()->params into class properties.
    * 
    */
    public static function load()
    {
        self::$allowedHtmlDoctype=array('strict','transitional');
        self::$allowedUserLoginWithField=array('_any_','email','username');
        self::$defaultMetaKeywords=array('web3cms','yii');
        self::$defaultPathToFiles=dirname(Yii::app()->basePath).DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR;
        self::$defaultUrlToFiles=Yii::app()->request->baseUrl.'/files/';
        $data=Yii::app()->params;
        self::setAdminEmailAddress(isset($data['adminEmailAddress']) ? $data['adminEmailAddress'] : self::__default);
        self::setAdminEmailName(isset($data['adminEmailName']) ? $data['adminEmailName'] : self::__default);
        self::setCopyrightBy(isset($data['copyrightBy']) ? $data['copyrightBy'] : self::__default);
        self::setCssTheme(isset($data['cssTheme']) ? $data['cssTheme'] : self::__default);
        self::setHeaderTitle(isset($data['headerTitle']) ? $data['headerTitle'] : self::__default);
        self::setHtmlDoctype(isset($data['htmlDoctype']) ? $data['htmlDoctype'] : self::__default);
        self::setMetaDescription(isset($data['metaDescription']) ? $data['metaDescription'] : self::__default);
        self::setMetaKeywords(isset($data['metaKeywords']) ? $data['metaKeywords'] : self::__default);
        self::setPageTitleFormula(isset($data['pageTitleFormula']) ? $data['pageTitleFormula'] : self::__default);
        self::setPathToFiles(isset($data['pathToFiles']) ? $data['pathToFiles'] : self::__default);
        self::setSiteTitle(isset($data['siteTitle']) ? $data['siteTitle'] : self::__default);
        self::setUrlToFiles(isset($data['urlToFiles']) ? $data['urlToFiles'] : self::__default);
        self::setUserLoginWithField(isset($data['userLoginWithField']) ? $data['userLoginWithField'] : self::__default);
    }

    /**
    * Get the value of a parameter.
    * Parameter is case sensitive.
    * 
    * @param string $param
    * @return mixed
    */
    public static function get($param)
    {
        return isset(self::$__data[$param]) ? self::$__data[$param] : null;
    }

    /**
    * Set the value of a parameter.
    * Parameter is case sensitive.
    * 
    * @param string $param
    * @param mixed $value
    */
    public static function set($param,$value)
    {
        self::$__data[$param]=$value;
    }

    /**
    * Add meta description.
    * html > head > meta[description]
    * 
    * @param string $value
    * @param string $delimiter
    */
    public function addMetaDescription($value,$delimiter=' ')
    {
        if($value===self::__default)
            $value=self::defaultMetaDescription;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=''; // clear the wrong value
        }
        self::setMetaDescription(self::getMetaDescription().$delimiter.$value);
    }

    /**
    * Add meta keywords.
    * html > head > meta[keywords]
    * 
    * @param array/string $value
    * @param bool $append. false = prepend
    */
    public function addMetaKeywords($value,$append=true)
    {
        if($value===self::__default)
            $value=self::$defaultMetaKeywords;
        if(is_string($value) || is_numeric($value))
            // convert string to array
            $value=array($value);
        if(!is_array($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=array(); // clear the wrong value
        }
        if($append)
            $value=array_merge(self::getMetaKeywords(),$value); // append
        else
            $value=array_merge($value,self::getMetaKeywords()); // prepend
        self::setMetaKeywords($value);
    }

    /**
    * Remove meta description.
    * html > head > meta[description]
    * 
    * @param string $value
    */
    public function removeMetaDescription($value)
    {
        if($value===self::__default)
            $value=self::defaultMetaDescription;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=''; // clear the wrong value
        }
        self::setMetaDescription(str_replace($value,'',self::getMetaDescription()));
    }

    /**
    * Remove meta keywords.
    * html > head > meta[keywords]
    * 
    * @param array/string $value
    */
    public function removeMetaKeywords($value)
    {
        if(is_string($value) || is_numeric($value))
            // convert string to array
            $value=array($value);
        if(!is_array($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
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
    * From: "adminEmailName" <adminEmailAddress>
    * 
    * @return string
    */
    public static function getAdminEmailAddress()
    {
        $value=self::$adminEmailAddress;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'adminEmailAddress'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultAdminEmailAddress; // set the wrong value to default
        }
        return $value;
    }

    /**
    * From: "adminEmailName" <adminEmailAddress>
    * 
    * @return string
    */
    public static function getAdminEmailName()
    {
        $value=self::$adminEmailName;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'adminEmailName'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultAdminEmailName; // set the wrong value to default
        }
        return $value;
    }

    /**
    * Displayed in the footer of every page.
    * Copyright © 2009 by {copyrightBy}. All Rights Reserved.
    * 
    * @return string
    */
    public static function getCopyrightBy()
    {
        $value=self::$copyrightBy;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'copyrightBy'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultCopyrightBy; // set the wrong value to default
        }
        return $value;
    }

    /**
    * jQuery UI CSS Framework theme.
    * Path is {root}/css/themes/{theme}
    * 
    * @return string
    */
    public static function getCssTheme()
    {
        $value=self::$cssTheme;
        if(!MPath::cssThemeExists($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'cssTheme'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultCssTheme; // set the wrong value to default
        }
        return $value;
    }

    /**
    * Title of your cms, displayed in the header section (above menu).
    * 
    * @return string
    */
    public static function getHeaderTitle()
    {
        $value=self::$headerTitle;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'headerTitle'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultHeaderTitle; // set the wrong value to default
        }
        return $value;
    }

    /**
    * Html document type.
    * <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    * 
    * @return string
    */
    public static function getHtmlDoctype()
    {
        $value=self::$htmlDoctype;
        if($value===true || !in_array($value,self::$allowedHtmlDoctype))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'htmlDoctype'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultHtmlDoctype; // set the wrong value to default
        }
        return $value;
    }

    /**
    * html > head > meta[description]
    * 
    * @return string
    */
    public static function getMetaDescription()
    {
        $value=self::$metaDescription;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'metaDescription'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultMetaDescription; // set the wrong value to default
        }
        return $value;
    }

    /**
    * html > head > meta[keywords]
    * 
    * @return array
    */
    public static function getMetaKeywords()
    {
        $value=self::$metaKeywords;
        if(!is_array($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'metaKeywords'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::$defaultMetaKeywords; // set the wrong value to default
        }
        return $value;
    }

    /**
    * String representation of {@link MParams::getMetaKeywords()} array.
    * html > head > meta[keywords]
    * 
    * @param string $glue
    * @return string
    */
    public static function getMetaKeywordsAsString($glue=', ')
    {
        if(!is_string($glue) && !is_numeric($glue))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($glue,true).')')
            ),'notice','w3');
            $glue=', '; // set the wrong value to default
        }
        $value=implode($glue,self::getMetaKeywords());
        return $value;
    }

    /**
    * Page label, displayed in the top of content area,
    * right after breadcrumbs.
    * <h1 class="w3-page-label">{pageLabel}</h1>
    * 
    * @return string
    */
    public static function getPageLabel()
    {
        $value=self::$pageLabel;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'pageLabel'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultPageLabel; // set the wrong value to default
        }
        return $value;
    }

    /**
    * Displayed in the footer of every page.
    * 
    * @return string
    */
    public static function getPageTitleFormula()
    {
        $value=self::$pageTitleFormula;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'pageTitleFormula'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultPageTitleFormula; // set the wrong value to default
        }
        return $value;
    }

    /**
    * HDD path to files folder.
    * Must be a valid directory accessible within your hosting.
    * 
    * @return string
    */
    public static function getPathToFiles()
    {
        $value=self::$pathToFiles;
        if(!is_dir($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'pathToFiles'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::$defaultPathToFiles; // set the wrong value to default
        }
        return $value;
    }

    /**
    * Title of your site.
    * html > head > title
    * 
    * @return string
    */
    public static function getSiteTitle()
    {
        $value=self::$siteTitle;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'siteTitle'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultSiteTitle; // set the wrong value to default
        }
        return $value;
    }

    /**
    * Web-accessible url to files directory.
    * Supposed to begin with either http:// or / (slash). 
    * Should contain trailing slash.
    * 
    * @return string
    */
    public static function getUrlToFiles()
    {
        $value=self::$urlToFiles;
        if(empty($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'urlToFiles'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::$defaultUrlToFiles; // set the wrong value to default
        }
        return $value;
    }

    /**
    * Which field to log user in with.
    * Is one of username/email/_any_
    * 
    * @return string
    */
    public static function getUserLoginWithField()
    {
        $value=self::$userLoginWithField;
        if($value===true || !in_array($value,self::$allowedUserLoginWithField))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} system parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'userLoginWithField'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultUserLoginWithField; // set the wrong value to default
        }
        return $value;
    }

    /**
    * From: "adminEmailName" <adminEmailAddress>
    * 
    * @param string $value
    */
    public static function setAdminEmailAddress($value)
    {
        if($value===self::__default)
            $value=self::defaultAdminEmailAddress;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultAdminEmailAddress; // set the wrong value to default
        }
        self::$adminEmailAddress=$value;
    }

    /**
    * From: "adminEmailName" <adminEmailAddress>
    * 
    * @param string $value
    */
    public static function setAdminEmailName($value)
    {
        if($value===self::__default)
            $value=self::defaultAdminEmailName;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultAdminEmailName; // set the wrong value to default
        }
        self::$adminEmailName=$value;
    }

    /**
    * Displayed in the footer of every page.
    * Copyright © 2009 by {copyrightBy}. All Rights Reserved.
    * 
    * @param string $value
    */
    public static function setCopyrightBy($value)
    {
        if($value===self::__default)
            $value=self::defaultCopyrightBy;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultCopyrightBy; // set the wrong value to default
        }
        self::$copyrightBy=$value;
    }

    /**
    * jQuery UI CSS Framework theme.
    * Path is {root}/css/themes/{theme}
    * 
    * @param string $value
    */
    public static function setCssTheme($value)
    {
        if($value===self::__default)
            $value=self::defaultCssTheme;
        if(!MPath::cssThemeExists($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultCssTheme; // set the wrong value to default
        }
        self::$cssTheme=$value;
    }

    /**
    * Title of your cms, displayed in the header section (above menu).
    * 
    * @param string $value
    */
    public static function setHeaderTitle($value)
    {
        if($value===self::__default)
            $value=self::defaultHeaderTitle;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultHeaderTitle; // set the wrong value to default
        }
        self::$headerTitle=$value;
    }

    /**
    * Html document type.
    * <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    * 
    * @param string $value
    */
    public static function setHtmlDoctype($value)
    {
        if($value===self::__default)
            $value=self::defaultHtmlDoctype;
        else if($value===true || !in_array($value,self::$allowedHtmlDoctype))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultHtmlDoctype; // set the wrong value to default
        }
        self::$htmlDoctype=$value;
    }

    /**
    * html > head > meta[description]
    * 
    * @param string $value
    */
    public static function setMetaDescription($value)
    {
        if($value===self::__default)
            $value=self::defaultMetaDescription;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultMetaDescription; // set the wrong value to default
        }
        self::$metaDescription=$value;
    }

    /**
    * html > head > meta[keywords]
    * 
    * @param array $value
    */
    public static function setMetaKeywords($value)
    {
        if($value===self::__default)
            $value=self::$defaultMetaKeywords;
        if(!is_array($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::$defaultMetaKeywords; // set the wrong value to default
        }
        $value=array_unique($value); // google don't like repeating keywords
        self::$metaKeywords=$value;
    }

    /**
    * Page label, displayed in the top of content area,
    * right after breadcrumbs.
    * <h1 class="w3-page-label">{pageLabel}</h1>
    * 
    * @param string $value
    */
    public static function setPageLabel($value)
    {
        if($value===self::__default)
            $value=self::defaultPageLabel;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultPageLabel; // set the wrong value to default
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
    * 
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
    * 
    * @param string $value
    */
    public static function setPageTitleFormula($value)
    {
        if($value===self::__default)
            $value=self::defaultPageTitleFormula;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultPageTitleFormula; // set the wrong value to default
        }
        self::$pageTitleFormula=$value;
    }

    /**
    * HDD path to files folder.
    * Must be a valid directory accessible within your hosting.
    * 
    * @param string $value
    */
    public static function setPathToFiles($value)
    {
        if($value===self::__default)
            $value=self::$defaultPathToFiles;
        if(!is_dir($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::$defaultPathToFiles; // set the wrong value to default
        }
        self::$pathToFiles=$value;
    }

    /**
    * Title of your site.
    * html > head > title
    * 
    * @param string $value
    */
    public static function setSiteTitle($value)
    {
        if($value===self::__default)
            $value=self::defaultSiteTitle;
        if(!is_string($value) && !is_numeric($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultSiteTitle; // set the wrong value to default
        }
        self::$siteTitle=$value;
        // got a param involved in page title => set it
        self::setPageTitle();
    }

    /**
    * Web-accessible url to files directory.
    * Supposed to begin with either http:// or / (slash). 
    * Should contain trailing slash.
    * 
    * @param string $value
    */
    public static function setUrlToFiles($value)
    {
        if($value===self::__default)
            $value=self::$defaultUrlToFiles;
        else if(empty($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::$defaultUrlToFiles; // set the wrong value to default
        }
        self::$urlToFiles=$value;
    }

    /**
    * Which field to log user in with.
    * Should be one of username/email/_any_
    * 
    * @param string $value
    */
    public static function setUserLoginWithField($value)
    {
        if($value===self::__default)
            $value=self::defaultUserLoginWithField;
        else if($value===true || !in_array($value,self::$allowedUserLoginWithField))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultUserLoginWithField; // set the wrong value to default
        }
        self::$userLoginWithField=$value;
    }
}