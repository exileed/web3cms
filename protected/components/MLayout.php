<?php
/**
 * Manage Layout
 */
class MLayout
{
    protected static $numberOfColumnsContent;
    protected static $numberOfColumnsSidebar1;
    protected static $numberOfColumnsSidebar2;
    protected static $numberOfColumnsTotal;
    protected static $wrapInGridCssClass;
    protected static $numberOfItemsContent;
    protected static $numberOfItemsSidebar1;
    protected static $numberOfItemsSidebar2;
    protected static $numberOfItemsTop;
    protected static $allowedNumberOfColumnsContent;
    protected static $allowedNumberOfColumnsSidebar1;
    protected static $allowedNumberOfColumnsSidebar2;
    protected static $allowedNumberOfColumnsTotal;
    const __default='__default';
    const defaultNumberOfColumnsContent=12;
    const defaultNumberOfColumnsSidebar1=0;
    const defaultNumberOfColumnsSidebar2=4;
    const defaultNumberOfColumnsTotal=16;
    const defaultWrapInGridCssClass=true;

    /**
    * Load params from Yii::app()->params into class properties.
    */
    public static function load()
    {
        self::validateDefaultNumberOfColumns();
        self::$allowedNumberOfColumnsContent=range(0,self::defaultNumberOfColumnsTotal);
        self::$allowedNumberOfColumnsSidebar1=range(0,self::defaultNumberOfColumnsTotal);
        self::$allowedNumberOfColumnsSidebar2=range(0,self::defaultNumberOfColumnsTotal);
        self::$allowedNumberOfColumnsTotal=array(12,16);
        $data=Yii::app()->params;
        self::setNumberOfColumnsContent(isset($data['layoutNumberOfColumnsContent']) ? $data['layoutNumberOfColumnsContent'] : self::__default);
        self::setNumberOfColumnsSidebar1(isset($data['layoutNumberOfColumnsSidebar1']) ? $data['layoutNumberOfColumnsSidebar1'] : self::__default);
        self::setNumberOfColumnsSidebar2(isset($data['layoutNumberOfColumnsSidebar2']) ? $data['layoutNumberOfColumnsSidebar2'] : self::__default);
        self::setNumberOfColumnsTotal(isset($data['layoutNumberOfColumnsTotal']) ? $data['layoutNumberOfColumnsTotal'] : self::__default);
        self::validateNumberOfColumns();
        self::setWrapInGridCssClass(isset($data['layoutWrapInGridCssClass']) ? $data['layoutWrapInGridCssClass'] : self::__default);
    }

    /**
    * Decrement number of content area items.
    */
    public static function decrementNumberOfItemsContent()
    {
        is_null(self::$numberOfItemsContent)? self::$numberOfItemsContent=0 : --self::$numberOfItemsContent;
    }

    /**
    * decrement number of either sidebar1 or sidebar2 area items.
    * 
    * @param mixed $in
    */
    public static function decrementNumberOfItemsSidebar($in)
    {
        if($in==='sidebar1' || $in==='1' || $in===1)
            self::incrementNumberOfItemsSidebar1();
        else if($in==='sidebar2' || $in==='2' || $in===2)
            self::incrementNumberOfItemsSidebar2();
        else
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($in,true).')')
            ),'warning','w3');
    }

    /**
    * Decrement number of sidebar1 area items.
    */
    public static function decrementNumberOfItemsSidebar1()
    {
        is_null(self::$numberOfItemsSidebar1)? self::$numberOfItemsSidebar1=0 : --self::$numberOfItemsSidebar1;
    }

    /**
    * Decrement number of sidebar2 area items.
    */
    public static function decrementNumberOfItemsSidebar2()
    {
        is_null(self::$numberOfItemsSidebar2)? self::$numberOfItemsSidebar2=0 : --self::$numberOfItemsSidebar2;
    }

    /**
    * Decrement number of top area items.
    */
    public static function decrementNumberOfItemsTop()
    {
        is_null(self::$numberOfItemsTop)? self::$numberOfItemsTop=0 : --self::$numberOfItemsTop;
    }

    /**
    * Generate css classes for html's tag "body".
    * <body class="_return_">
    * 
    * @return string
    */
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
        $cssTheme='css-theme-'.MParams::getCssTheme();
        return $column.' '.$details.' '.$controller.' '.$controllerAction.' '.$cssTheme;
    }

    /**
    * Get container css class.
    * <div class="container_16"><div class="grid_16">...</div></div>
    * 
    * @return string
    */
    public static function getContainerCssClass()
    {
        return 'container_'.self::getNumberOfColumnsTotal();
    }

    /**
    * Get grid css class.
    * <div class="container_16"><div class="grid_16">...</div></div>
    * 
    * @return string
    */
    public static function getGridCssClass()
    {
        if(self::getWrapInGridCssClass())
            return 'grid_'.self::getNumberOfColumnsTotal();
    }

    /**
    * Get grid css class for content area.
    * <div class="grid_12 alpha"><div class="w3-content">...</div></div>
    * 
    * @return string
    */
    public static function getGridCssClassContent()
    {
        // no sidebars = no need to wrap in "grid_16" class
        // because we have it above "w3-body-wrapper" div
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

    /**
    * Get grid css class for sidebar2 area.
    * <div class="grid_4 alpha"><div class="w3-sidebar1">...</div></div>
    * 
    * @return string
    */
    public static function getGridCssClassSidebar1()
    {
        if(self::hasContent() || self::hasSidebar2())
        {
            $pos=' alpha';
            return 'grid_'.self::getNumberOfColumnsSidebar1().$pos;
        }
    }

    /**
    * Get grid css class for sidebar2 area.
    * <div class="grid_4 omega"><div class="w3-sidebar2">...</div></div>
    * 
    * @return string
    */
    public static function getGridCssClassSidebar2()
    {
        if(self::hasContent() || self::hasSidebar1())
        {
            $pos=' omega';
            return 'grid_'.self::getNumberOfColumnsSidebar2().$pos;
        }
    }

    /**
    * GS number of columns for content.
    * Common values are 8 and 12. Default is 12.
    * Visit {@link http://960.gs/demo.html} grid system for more details.
    * 
    * @return int
    */
    public static function getNumberOfColumnsContent()
    {
        $value=self::$numberOfColumnsContent;
        if($value===true || !in_array($value,self::$allowedNumberOfColumnsContent))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} layout parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'numberOfColumnsContent'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultNumberOfColumnsContent; // set the wrong value to default
        }
        return $value;
    }

    /**
    * GS number of columns for sidebar1.
    * Common values are 0 and 4. Default is 0.
    * Visit {@link http://960.gs/demo.html} grid system for more details.
    * 
    * @return int
    */
    public static function getNumberOfColumnsSidebar1()
    {
        $value=self::$numberOfColumnsSidebar1;
        if($value===true || !in_array($value,self::$allowedNumberOfColumnsSidebar1))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} layout parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'numberOfColumnsSidebar1'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultNumberOfColumnsSidebar1; // set the wrong value to default
        }
        return $value;
    }

    /**
    * GS number of columns for sidebar2.
    * Common values are 0 and 4. Default is 4.
    * Visit {@link http://960.gs/demo.html} grid system for more details.
    * 
    * @return int
    */
    public static function getNumberOfColumnsSidebar2()
    {
        $value=self::$numberOfColumnsSidebar2;
        if($value===true || !in_array($value,self::$allowedNumberOfColumnsSidebar2))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} layout parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'numberOfColumnsSidebar2'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultNumberOfColumnsSidebar2; // set the wrong value to default
        }
        return $value;
    }

    /**
    * GS number of columns total.
    * Common values are 12 and 16. Default is 16.
    * Visit {@link http://960.gs/demo.html} grid system for more details.
    * 
    * @return int
    */
    public static function getNumberOfColumnsTotal()
    {
        $value=self::$numberOfColumnsTotal;
        if($value===true || !in_array($value,self::$allowedNumberOfColumnsTotal))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} layout parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'numberOfColumnsTotal'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultNumberOfColumnsTotal; // set the wrong value to default
        }
        return $value;
    }

    /**
    * Get number of items displayed in content area.
    * 
    * @return int
    */
    public static function getNumberOfItemsContent()
    {
        $value=self::$numberOfItemsContent;
        if(!is_null($value) && !is_int($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} layout parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'numberOfItemsContent'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
        }
        // (int)null == 0
        return (int)$value;
    }

    /**
    * Get number of items displayed in either sidebar1 or sidebar2 area.
    * 
    * @param mixed $in
    * @return int
    */
    public static function getNumberOfItemsSidebar($in)
    {
        if($in==='sidebar1' || $in==='1' || $in===1)
            return self::getNumberOfItemsSidebar1();
        else if($in==='sidebar2' || $in==='2' || $in===2)
            return self::getNumberOfItemsSidebar2();
        else
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($in,true).')')
            ),'warning','w3');
        return false;
    }

    /**
    * Get number of items displayed in sidebar1 area.
    * 
    * @return int
    */
    public static function getNumberOfItemsSidebar1()
    {
        $value=self::$numberOfItemsSidebar1;
        if(!is_null($value) && !is_int($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} layout parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'numberOfItemsSidebar1'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
        }
        // (int)null == 0
        return (int)$value;
    }

    /**
    * Get number of items displayed in sidebar2 area.
    * 
    * @return int
    */
    public static function getNumberOfItemsSidebar2()
    {
        $value=self::$numberOfItemsSidebar2;
        if(!is_null($value) && !is_int($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} layout parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'numberOfItemsSidebar2'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
        }
        // (int)null == 0
        return (int)$value;
    }

    /**
    * Get number of items displayed in top area.
    * 
    * @return int
    */
    public static function getNumberOfItemsTop()
    {
        $value=self::$numberOfItemsTop;
        if(!is_null($value) && !is_int($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} layout parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'numberOfItemsTop'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
        }
        // (int)null == 0
        return (int)$value;
    }

    /**
    * GS wrap in css "grid_16" (sub)class.
    * If true, layout will look like <div class="container_16"><div class="grid_16">..header..</div></div>
    * If false, layout will look like <div class="container_16"><div class="">..header..</div></div>
    * 
    * @return bool
    */
    public static function getWrapInGridCssClass()
    {
        $value=self::$wrapInGridCssClass;
        if(!is_bool($value))
        {
            Yii::log(Yii::t('w3',
                'Wrong value of {parameter} layout parameter: {value}. Method called: {method}.',
                array(
                    '{parameter}'=>"'wrapInGridCssClass'",
                    '{value}'=>var_export($value,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            $value=self::defaultWrapInGridCssClass; // set the wrong value to default
        }
        return $value;
    }

    /**
    * Whether current page has content area.
    * 
    * @return bool
    */
    public static function hasContent()
    {
        return (bool)self::getNumberOfColumnsContent();
    }

    /**
    * Whether current page has sidebar1 or sidebar2 area.
    * 
    * @return bool
    */
    public static function hasSidebar()
    {
        return (bool)(self::hasSidebar1() || self::hasSidebar2());
    }

    /**
    * Whether current page has sidebar1 area.
    * 
    * @return bool
    */
    public static function hasSidebar1()
    {
        return (bool)self::getNumberOfColumnsSidebar1();
    }

    /**
    * Whether current page has sidebar2 area.
    * 
    * @return bool
    */
    public static function hasSidebar2()
    {
        return (bool)self::getNumberOfColumnsSidebar2();
    }

    /**
    * Hide sidebar1, sidebar2 and make content area width = page width.
    */
    public static function hideSidebars()
    {
        self::setNumberOfColumnsSidebar1(0);
        self::setNumberOfColumnsSidebar2(0);
        self::setNumberOfColumnsContent(self::getNumberOfColumnsTotal());
    }

    /**
    * Hide sidebar1 and enlarge sidebar2 or content.
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
    * Hide sidebar2 and enlarge sidebar1 or content.
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
    * Increment number of content area items.
    */
    public static function incrementNumberOfItemsContent()
    {
        is_null(self::$numberOfItemsContent)? self::$numberOfItemsContent=1 : ++self::$numberOfItemsContent;
    }

    /**
    * Increment number of either sidebar1 or sidebar2 area items.
    * 
    * @param mixed $in
    */
    public static function incrementNumberOfItemsSidebar($in)
    {
        if($in==='sidebar1' || $in==='1' || $in===1)
            self::incrementNumberOfItemsSidebar1();
        else if($in==='sidebar2' || $in==='2' || $in===2)
            self::incrementNumberOfItemsSidebar2();
        else
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($in,true).')')
            ),'warning','w3');
    }

    /**
    * Increment number of sidebar1 area items.
    */
    public static function incrementNumberOfItemsSidebar1()
    {
        is_null(self::$numberOfItemsSidebar1)? self::$numberOfItemsSidebar1=1 : ++self::$numberOfItemsSidebar1;
    }

    /**
    * Increment number of sidebar2 area items.
    */
    public static function incrementNumberOfItemsSidebar2()
    {
        is_null(self::$numberOfItemsSidebar2)? self::$numberOfItemsSidebar2=1 : ++self::$numberOfItemsSidebar2;
    }

    /**
    * Increment number of top area items.
    */
    public static function incrementNumberOfItemsTop()
    {
        is_null(self::$numberOfItemsTop)? self::$numberOfItemsTop=1 : ++self::$numberOfItemsTop;
    }

    /**
    * GS number of columns for content.
    * Common values are 8 and 12. Default is 12.
    * Visit {@link http://960.gs/demo.html} grid system for more details.
    * 
    * @param int $value
    */
    public static function setNumberOfColumnsContent($value)
    {
        if($value===self::__default)
            $value=self::defaultNumberOfColumnsContent;
        if($value===true || !in_array($value,self::$allowedNumberOfColumnsContent))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultNumberOfColumnsContent; // set the wrong value to default
        }
        self::$numberOfColumnsContent=$value;
    }

    /**
    * GS number of columns for sidebar1.
    * Common values are 0 and 4. Default is 0.
    * Visit {@link http://960.gs/demo.html} grid system for more details.
    * 
    * @param int $value
    */
    public static function setNumberOfColumnsSidebar1($value)
    {
        if($value===self::__default)
            $value=self::defaultNumberOfColumnsSidebar1;
        if($value===true || !in_array($value,self::$allowedNumberOfColumnsSidebar1))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultNumberOfColumnsSidebar1; // set the wrong value to default
        }
        self::$numberOfColumnsSidebar1=$value;
    }

    /**
    * GS number of columns for sidebar2.
    * Common values are 0 and 4. Default is 4.
    * Visit {@link http://960.gs/demo.html} grid system for more details.
    * 
    * @param int $value
    */
    public static function setNumberOfColumnsSidebar2($value)
    {
        if($value===self::__default)
            $value=self::defaultNumberOfColumnsSidebar2;
        if($value===true || !in_array($value,self::$allowedNumberOfColumnsSidebar2))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultNumberOfColumnsSidebar2; // set the wrong value to default
        }
        self::$numberOfColumnsSidebar2=$value;
    }

    /**
    * GS number of columns total.
    * Common values are 12 and 16. Default is 16.
    * Visit {@link http://960.gs/demo.html} grid system for more details.
    * 
    * @param int $value
    */
    public static function setNumberOfColumnsTotal($value)
    {
        if($value===self::__default)
            $value=self::defaultNumberOfColumnsTotal;
        if($value===true || !in_array($value,self::$allowedNumberOfColumnsTotal))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultNumberOfColumnsTotal; // set the wrong value to default
        }
        self::$numberOfColumnsTotal=$value;
    }

    /**
    * Alias of setNumberOfColumns...()
    * Set all GS number of columns from array.
    * 
    * @param array $array
    */
    public static function setNumberOfColumnsArray($array)
    {
        isset($array['total']) && self::setNumberOfColumnsTotal($array['total']);
        isset($array['content']) && self::setNumberOfColumnsContent($array['content']);
        isset($array['sidebar1']) && self::setNumberOfColumnsSidebar1($array['sidebar1']);
        isset($array['sidebar2']) && self::setNumberOfColumnsSidebar2($array['sidebar2']);
    }

    /**
    * Set number of items displayed in content area.
    * 
    * @param int $value
    */
    public static function setNumberOfItemsContent($value)
    {
        if(is_numeric($value))
            self::$numberOfItemsContent=(int)$value;
        else
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'warning','w3');
        }
    }

    /**
    * Set number of items displayed in either sidebar1 or sidebar2 area.
    * 
    * @param mixed $in
    * @param int $value
    */
    public static function setNumberOfItemsSidebar($in,$value)
    {
        if($in==='sidebar1' || $in==='1' || $in===1)
            self::setNumberOfItemsSidebar1();
        else if($in==='sidebar2' || $in==='2' || $in===2)
            self::setNumberOfItemsSidebar2();
        else
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($in,true).','.var_export($value,true).')')
            ),'warning','w3');
    }

    /**
    * Set number of items displayed in sidebar1 area.
    * 
    * @param int $value
    */
    public static function setNumberOfItemsSidebar1($value)
    {
        if(is_numeric($value))
            self::$numberOfItemsSidebar1=(int)$value;
        else
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'warning','w3');
        }
    }

    /**
    * Set number of items displayed in sidebar2 area.
    * 
    * @param int $value
    */
    public static function setNumberOfItemsSidebar2($value)
    {
        if(is_numeric($value))
            self::$numberOfItemsSidebar2=(int)$value;
        else
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'warning','w3');
        }
    }

    /**
    * Set number of items displayed in top area.
    * 
    * @param int $value
    */
    public static function setNumberOfItemsTop($value)
    {
        if(is_numeric($value))
            self::$numberOfItemsTop=(int)$value;
        else
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'warning','w3');
        }
    }

    /**
    * GS wrap in css "grid_16" (sub)class.
    * If true, layout will look like <div class="container_16"><div class="grid_16">..header..</div></div>
    * If false, layout will look like <div class="container_16"><div class="">..header..</div></div>
    * 
    * @param bool $value
    */
    public static function setWrapInGridCssClass($value)
    {
        if($value===self::__default)
            $value=self::defaultWrapInGridCssClass;
        if(!is_bool($value))
        {
            Yii::log(Yii::t('w3',
                'Incorrect parameter in method call {method}',
                array('{method}'=>__METHOD__.'('.var_export($value,true).')')
            ),'notice','w3');
            $value=self::defaultWrapInGridCssClass; // set the wrong value to default
        }
        self::$wrapInGridCssClass=$value;
    }

    /**
    * Validate GS number of columns.
    * Sum of GS content, sidebar1 & sidebar2 must be = GS total;
    * if not, set all to default.
    */
    public static function validateNumberOfColumns()
    {
        if(self::getNumberOfColumnsContent()+self::getNumberOfColumnsSidebar1()+self::getNumberOfColumnsSidebar2() != self::getNumberOfColumnsTotal())
        {
            Yii::log(Yii::t('w3',
                'Wrong value of layout parameters... content: {content}, sidebar1: {sidebar1}, sidebar2: {sidebar2}, total: {total}. Method called: {method}.',
                array(
                    '{content}'=>var_export(self::getNumberOfColumnsContent(),true),
                    '{sidebar1}'=>var_export(self::getNumberOfColumnsSidebar1(),true),
                    '{sidebar2}'=>var_export(self::getNumberOfColumnsSidebar2(),true),
                    '{total}'=>var_export(self::getNumberOfColumnsTotal(),true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'warning','w3');
            self::setNumberOfColumnsContent(self::__default);
            self::setNumberOfColumnsSidebar1(self::__default);
            self::setNumberOfColumnsSidebar2(self::__default);
            self::setNumberOfColumnsTotal(self::__default);
        }
    }

    /**
    * Validate GS default number of columns.
    * Sum of GS default content, sidebar1 & sidebar2 must be = GS default total.
    */
    public static function validateDefaultNumberOfColumns()
    {
        if(self::defaultNumberOfColumnsContent+self::defaultNumberOfColumnsSidebar1+self::defaultNumberOfColumnsSidebar2 != self::defaultNumberOfColumnsTotal)
        {
            Yii::log(Yii::t('w3',
                'Wrong value of layout constants... content: {content}, sidebar1: {sidebar1}, sidebar2: {sidebar2}, total: {total}. Method called: {method}.',
                array(
                    '{content}'=>var_export(self::defaultNumberOfColumnsContent,true),
                    '{sidebar1}'=>var_export(self::defaultNumberOfColumnsSidebar1,true),
                    '{sidebar2}'=>var_export(self::defaultNumberOfColumnsSidebar2,true),
                    '{total}'=>var_export(self::defaultNumberOfColumnsTotal,true),
                    '{method}'=>__METHOD__.'()'
                )
            ),'error','w3');
        }
    }
}