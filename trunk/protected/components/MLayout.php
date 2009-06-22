<?php
/**
 * Manage Layout
 */
class MLayout
{
    public static $countContentItem;
    public static $countSidebar1Item;
    public static $countSidebar2Item;
    public static $countTopItem;
    protected static $cssTheme;
    protected static $doctype;
    protected static $numberOfColumns;
    protected static $numberOfColumnsContent;
    protected static $numberOfColumnsSidebar1;
    protected static $numberOfColumnsSidebar2;
    protected static $wrapInGridCssClass;
    const jqueryUIVersion='1.7.2';

    /**
    * Check whether a css theme exists.
    * 
    * @param string $theme
    * @return bool
    */
    public static function cssThemeExists($theme=null)
    {
        return !empty($theme) && file_exists(dirname(Yii::app()->basePath).DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'jquery-ui-'.self::jqueryUIVersion.'.custom.css');
    }

    /**
    * Count number of content items
    */
    public static function countContentItem()
    {
        return is_null(self::$countContentItem)?0:self::$countContentItem;
    }

    /**
    * Count number of either sidebar1 or sidebar2 items
    */
    public static function countSidebarItem($snum=null)
    {
        if($snum==='sidebar1' || $snum==='1' || $snum===1)
            return self::countSidebar1Item();
        else if($snum==='sidebar2' || $snum==='2' || $snum===2)
            return self::countSidebar2Item();
        else
            Yii::log(Yii::t('w3','Incorrect parameter in method call {method}',array('{method}'=>__METHOD__.'('.var_export($snum,true).')')),'notice','w3');
        return false;
    }

    /**
    * Count number of sidebar1 items
    */
    public static function countSidebar1Item()
    {
        return is_null(self::$countSidebar1Item)?0:self::$countSidebar1Item;
    }

    /**
    * Count number of sidebar2 items
    */
    public static function countSidebar2Item()
    {
        return is_null(self::$countSidebar2Item)?0:self::$countSidebar2Item;
    }

    /**
    * Count number of top items
    */
    public static function countTopItem()
    {
        return is_null(self::$countTopItem)?0:self::$countTopItem;
    }

    public static function getBodytagCssClass()
    {
        $column=$details='';
        if(!self::hasContent() && !self::hasSidebar1() && !self::hasSidebar2())
            ($column='w3-layout-zero-column') && ($details='w3-layout');
        else if(self::hasContent() && !self::hasSidebar1() && !self::hasSidebar2())
            ($column='w3-layout-one-column') && ($details='w3-layout-content');
        else if(!self::hasContent() && self::hasSidebar1() && !self::hasSidebar2())
            ($column='w3-layout-one-column') && ($details='w3-layout-sidebar1');
        else if(!self::hasContent() && !self::hasSidebar1() && self::hasSidebar2())
            ($column='w3-layout-one-column') && ($details='w3-layout-sidebar2');
        else if(self::hasContent() && self::hasSidebar1() && !self::hasSidebar2())
            ($column='w3-layout-two-column') && ($details='w3-layout-sidebar1-content');
        else if(!self::hasContent() && self::hasSidebar1() && self::hasSidebar2())
            ($column='w3-layout-two-column') && ($details='w3-layout-sidebar1-sidebar2');
        else if(self::hasContent() && !self::hasSidebar1() && self::hasSidebar2())
            ($column='w3-layout-two-column') && ($details='w3-layout-content-sidebar2');
        else if(self::hasContent() && self::hasSidebar1() && self::hasSidebar2())
            ($column='w3-layout-three-column') && ($details='w3-layout-sidebar1-content-sidebar2');
        $controller='w3-controller-'.Yii::app()->controller->getId();
        $controllerAction=$controller.'-'.Yii::app()->controller->getAction()->getId();
        return $column.' '.$details.' '.$controller.' '.$controllerAction;
    }

    public static function getCssTheme()
    {
        if(is_null(self::$cssTheme))
            return Yii::app()->params['defaultCssTheme'];
        return self::$cssTheme;
    }

    public static function getContainerCssClass()
    {
        return 'container_'.self::getNumberOfColumns();
    }

    public static function getDoctype()
    {
        if(is_null(self::$doctype))
            return Yii::app()->params['layoutDoctype'];
        return self::$doctype;
    }

    public static function getGridCssClass()
    {
        if(self::getWrapInGridCssClass())
            return 'grid_'.self::getNumberOfColumns();
    }

    public static function getGridCssClassContent()
    {
        // if no sidebar then no need to wrap in "grid_16" as we have it above "w3-body-wrapper"
        if(self::hasSidebar())
        {
            if(self::hasSidebar1() && !self::hasSidebar2())
                $pos=' omega'; // only left sidebar is active
            else if(!self::hasSidebar1() && self::hasSidebar2())
                $pos=' alpha'; // only right sidebar is active
            else
                $pos=''; // both sidebars are active
            return 'grid_'.self::getNumberOfColumnsContent().$pos;
        }
    }

    public static function getGridCssClassSidebar1()
    {
        if(self::hasContent() || self::hasSidebar2())
        {
            $pos=' alpha';
            return 'grid_'.self::getNumberOfColumnsSidebar1().$pos;
        }
    }

    public static function getGridCssClassSidebar2()
    {
        if(self::hasContent() || self::hasSidebar1())
        {
            $pos=' omega';
            return 'grid_'.self::getNumberOfColumnsSidebar2().$pos;
        }
    }

    public static function getNumberOfColumns()
    {
        if(is_null(self::$numberOfColumns))
            return Yii::app()->params['layoutNumberOfColumns'];
        return self::$numberOfColumns;
    }

    public static function getNumberOfColumnsContent()
    {
        if(is_null(self::$numberOfColumnsContent))
            return Yii::app()->params['layoutNumberOfColumnsContent'];
        return self::$numberOfColumnsContent;
    }

    public static function getNumberOfColumnsSidebar1()
    {
        if(is_null(self::$numberOfColumnsSidebar1))
            return Yii::app()->params['layoutNumberOfColumnsSidebar1'];
        return self::$numberOfColumnsSidebar1;
    }

    public static function getNumberOfColumnsSidebar2()
    {
        if(is_null(self::$numberOfColumnsSidebar2))
            return Yii::app()->params['layoutNumberOfColumnsSidebar2'];
        return self::$numberOfColumnsSidebar2;
    }

    public static function getWrapInGridCssClass()
    {
        if(is_null(self::$wrapInGridCssClass))
            return Yii::app()->params['layoutWrapInGridCssClass'];
        return self::$wrapInGridCssClass;
    }

    public static function hasContent()
    {
        return (bool)self::getNumberOfColumnsContent();
    }

    public static function hasSidebar()
    {
        return (bool)(self::hasSidebar1() || self::hasSidebar2());
    }

    public static function hasSidebar1()
    {
        return (bool)self::getNumberOfColumnsSidebar1();
    }

    public static function hasSidebar2()
    {
        return (bool)self::getNumberOfColumnsSidebar2();
    }

    /**
    * Hide sidebar1, sidebar2 and make content page wide
    */
    public static function hideSidebars()
    {
        self::setNumberOfColumnsSidebar1(0);
        self::setNumberOfColumnsSidebar2(0);
        self::setNumberOfColumnsContent(self::getNumberOfColumns());
    }

    /**
    * Hide sidebar1 and enlarge sidebar2 or content
    * 
    * @param string $inFavourOf
    */
    public static function hideSidebar1($inFavourOf='content')
    {
        $num=self::getNumberOfColumnsSidebar1();
        self::setNumberOfColumnsSidebar1(0);
        if($num>0)
        {
            if($inFavourOf==='sidebar' || $inFavourOf==='sidebar2')
                self::setNumberOfColumnsSidebar2(self::getNumberOfColumnsSidebar2()+$num);
            else
                self::setNumberOfColumnsContent(self::getNumberOfColumnsContent()+$num);
        }
    }

    /**
    * Hide sidebar2 and enlarge sidebar1 or content
    * 
    * @param string $inFavourOf
    */
    public static function hideSidebar2($inFavourOf='content')
    {
        $num=self::getNumberOfColumnsSidebar2();
        self::setNumberOfColumnsSidebar2(0);
        if($num>0)
        {
            if($inFavourOf==='sidebar' || $inFavourOf==='sidebar1')
                self::setNumberOfColumnsSidebar1(self::getNumberOfColumnsSidebar1()+$num);
            else
                self::setNumberOfColumnsContent(self::getNumberOfColumnsContent()+$num);
        }
    }

    /**
    * Increment number of content items
    */
    public static function incrementContentItem()
    {
        if(is_null(self::$countContentItem))
            self::$countContentItem=1;
        else
            ++self::$countContentItem;
    }

    /**
    * Increment number of either sidebar1 or sidebar2 items
    */
    public static function incrementSidebarItem($snum=null)
    {
        if($snum==='sidebar1' || $snum==='1' || $snum===1)
            self::incrementSidebar1Item();
        else if($snum==='sidebar2' || $snum==='2' || $snum===2)
            self::incrementSidebar2Item();
        else
            Yii::log(Yii::t('w3','Incorrect parameter in method call {method}',array('{method}'=>__METHOD__.'('.var_export($snum,true).')')),'notice','w3');
    }

    /**
    * Increment number of sidebar1 items
    */
    public static function incrementSidebar1Item()
    {
        if(is_null(self::$countSidebar1Item))
            self::$countSidebar1Item=1;
        else
            ++self::$countSidebar1Item;
    }

    /**
    * Increment number of sidebar2 items
    */
    public static function incrementSidebar2Item()
    {
        if(is_null(self::$countSidebar2Item))
            self::$countSidebar2Item=1;
        else
            ++self::$countSidebar2Item;
    }

    /**
    * Increment number of top items
    */
    public static function incrementTopItem()
    {
        if(is_null(self::$countTopItem))
            self::$countTopItem=1;
        else
            ++self::$countTopItem;
    }

    /**
    * Whether is strict doctype
    * 
    * @return bool
    */
    public static function isStrictDoctype()
    {
        return (bool)(self::getDoctype()==='strict');
    }

    /**
    * Whether is transitional doctype
    * 
    * @return bool
    */
    public static function isTransitionalDoctype()
    {
        return (bool)(self::getDoctype()==='transitional');
    }

    /**
    * Set jQuery UI CSS Framework theme
    * First defined in config/params.php as 'defaultCssTheme'.
    * Path is {root}/css/themes/{theme}. Default is 'start'.
    * 
    * @param string $doctype
    * @return bool
    */
    public static function setCssTheme($theme=null)
    {
        if(self::cssThemeExists($theme))
            return (bool)(self::$cssTheme=$theme);
        Yii::log(Yii::t('w3','Incorrect parameter in method call {method}',array('{method}'=>__METHOD__.'('.var_export($theme,true).')')),'warning','w3');
        return false;
    }

    /**
    * Set document type
    * First defined in config/params.php as 'layoutDoctype'.
    * Allowed values are 'strict' and 'transitional'. Default is 'transitional'.
    * 
    * @param string $doctype
    * @return bool
    */
    public static function setDoctype($doctype=null)
    {
        if(in_array($doctype,array('strict','transitional')))
            return (bool)(self::$doctype=$doctype);
        Yii::log(Yii::t('w3','Incorrect parameter in method call {method}',array('{method}'=>__METHOD__.'('.var_export($doctype,true).')')),'notice','w3');
        return false;
    }

    /**
    * Set number of columns this page should use.
    * First defined in config/params.php as 'layoutNumberOfColumns'.
    * Common values are 12 and 16. Default is 16.
    * Visit {@link http://960.gs/demo.html} grid system for more details.
    * 
    * @param int $num
    * @return bool
    */
    public static function setNumberOfColumns($num=null)
    {
        if($num>=0 && $num<=10000)
            return (bool)(self::$numberOfColumns=(int)$num);
        Yii::log(Yii::t('w3','Incorrect parameter in method call {method}',array('{method}'=>__METHOD__.'('.var_export($num,true).')')),'notice','w3');
        return false;
    }
    /**
    * Alias for {@link setNumberOfColumns}
    */
    public static function setNOC($num=null)
    {
        return self::setNumberOfColumns($num);
    }

    /**
    * Alias. Set all NOCs together
    * 
    * @param int/array $columns
    * @param int $content
    * @param int $sidebar1
    * @param int $sidebar2
    */
    public static function setNumberOfColumnsBatch($columns=null,$content=null,$sidebar1=null,$sidebar2=null)
    {
        if(is_array($columns))
        {
            $content=isset($columns['content'])?$columns['content']:$content;
            $sidebar1=isset($columns['sidebar1'])?$columns['sidebar1']:$sidebar1;
            $sidebar2=isset($columns['sidebar2'])?$columns['sidebar2']:$sidebar2;
            $columns=isset($columns['columns'])?$columns['columns']:null;
        }
        self::setNumberOfColumns($columns);
        self::setNumberOfColumnsContent($content);
        self::setNumberOfColumnsSidebar1($sidebar1);
        self::setNumberOfColumnsSidebar2($sidebar2);
    }
    /**
    * Alias for {@link setNumberOfColumnsBatch}
    */
    public static function setNOCBatch($columns=null,$content=null,$sidebar1=null,$sidebar2=null)
    {
        self::setNumberOfColumnsBatch($columns,$content,$sidebar1,$sidebar2);
    }

    /**
    * Set number of columns content should use.
    * First defined in config/params.php as 'layoutNumberOfColumnsContent'.
    * Common values are 8 and 12. Default is 12.
    * Visit {@link http://960.gs/demo.html} grid system for more details.
    * 
    * @param int $num
    * @return bool
    */
    public static function setNumberOfColumnsContent($num)
    {
        if($num>=0 && $num<=self::getNumberOfColumns())
            return (bool)(self::$numberOfColumnsContent=(int)$num);
        Yii::log(Yii::t('w3','Incorrect parameter in method call {method}',array('{method}'=>__METHOD__.'('.var_export($num,true).')')),'notice','w3');
        return false;
    }
    /**
    * Alias for {@link setNumberOfColumnsContent}
    */
    public static function setNOCContent($num)
    {
        return self::setNumberOfColumnsContent($num);
    }

    /**
    * Set number of columns sidebar1 should use.
    * First defined in config/params.php as 'layoutNumberOfColumnsSidebar1'.
    * Common values are 0 and 4. Default is 0.
    * Visit {@link http://960.gs/demo.html} grid system for more details.
    * 
    * @param int $num
    * @return bool
    */
    public static function setNumberOfColumnsSidebar1($num)
    {
        if($num>=0 && $num<=self::getNumberOfColumns())
            return (bool)(self::$numberOfColumnsSidebar1=(int)$num);
        Yii::log(Yii::t('w3','Incorrect parameter in method call {method}',array('{method}'=>__METHOD__.'('.var_export($num,true).')')),'notice','w3');
        return false;
    }
    /**
    * Alias for {@link setNumberOfColumnsSidebar1}
    */
    public static function setNOCSidebar1($num)
    {
        return self::setNumberOfColumnsSidebar1($num);
    }

    /**
    * Set number of columns sidebar2 should use.
    * First defined in config/params.php as 'layoutNumberOfColumnsSidebar2'.
    * Common values are 0 and 4. Default is 4.
    * Visit {@link http://960.gs/demo.html} grid system for more details.
    * 
    * @param int $num
    * @return bool
    */
    public static function setNumberOfColumnsSidebar2($num)
    {
        if($num>=0 && $num<=self::getNumberOfColumns())
            return (bool)(self::$numberOfColumnsSidebar2=(int)$num);
        Yii::log(Yii::t('w3','Incorrect parameter in method call {method}',array('{method}'=>__METHOD__.'('.var_export($num,true).')')),'notice','w3');
        return false;
    }
    /**
    * Alias for {@link setNumberOfColumnsSidebar2}
    */
    public static function setNOCSidebar2($num)
    {
        return self::setNumberOfColumnsSidebar2($num);
    }

    /**
    * Whether wrap everything in css "grid_16" (sub)class.
    * First defined in config/params.php as 'layoutWrapInGridCssClass'.
    * If true, layout will look like <div class="container_16"><div class="grid_16">..header..</div></div>
    * If false, layout will look like <div class="container_16"><div class="">..header..</div></div>
    * 
    * @param int $num
    * @return bool
    */
    public static function setWrapInGridCssClass($bool)
    {
        return (bool)(self::$wrapInGridCssClass=(bool)$bool);
    }
}