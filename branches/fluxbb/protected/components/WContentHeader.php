<?php
/**
 * WContentHeader is a widget displaying header of the content of the page.
 * 
 * It has 2 parts: breadcrumbs list and page label.
 * The breadcrumb items are displayed as an HTML list with a grey border under it.
 * The page label is the H1 text (under breadcrumbs), stating what is this page about;
 * usually page label is the same string as html > head > title.
 * Additionally this widget is displaying a div under label, so space between page label
 * and the content itself is the same on all pages through the site.
 */
class WContentHeader extends CWidget
{
    /**
     * @var boolean whether display an empty div after the page label
     */
    public $afterLabel;

    /**
     * @var array of the breadcrumb links
     */
    public $breadcrumbs;

    /**
     * @var boolean whether display the breadcrumb list
     */
    public $displayBreadcrumbs;

    /**
     * @var boolean whether display the page label
     * (after breadcrumbs), default is true.
     */
    public $displayLabel;

    /**
     * @var string page label, displayed in h1
     */
    public $label;

    /**
     * @var boolean whether prepend the breadcrumbs array
     * with the home link.
     */
    public $prependHome;

    /**
     * When widget is called, following function is run.
     */
    public function run()
    {
        // set the default values and validate the data
        if($this->afterLabel===null)
            $this->afterLabel=true;
        if($this->displayBreadcrumbs===null)
            $this->displayBreadcrumbs=true;
        if($this->displayLabel===null)
            $this->displayLabel=true;
        $c=0;
        if($this->displayBreadcrumbs)
        {
            if($this->breadcrumbs===null)
                $this->breadcrumbs=array();
            if(!is_array($this->breadcrumbs))
                $this->breadcrumbs=array();
            if($this->prependHome===null)
                $this->prependHome=true;
            if($this->prependHome)
                // add home link to the beginning of the breadcrumbs array
                array_unshift($this->breadcrumbs,array(
                    'text'=>Yii::t('link','Home'),
                    'url'=>Yii::app()->homeUrl,
                    'active'=>false
                ));
            // the new array of the breadcrumbs is a validated one
            $breadcrumbs=array();
            foreach($this->breadcrumbs as $breadcrumb)
            {
                if(isset($breadcrumb['visible']) && !$breadcrumb['visible'])
                    continue;
                if(is_array($breadcrumb) && (isset($breadcrumb['text']) || isset($breadcrumb['url']) || isset($breadcrumb['active']) || isset($breadcrumb['options'])))
                {
                    $breadcrumbs[]=array(
                        'text'=>isset($breadcrumb['text']) ? (string)$breadcrumb['text'] : MParams::getPageLabel(),
                        'url'=>(isset($breadcrumb['url']) && (is_array($breadcrumb['url']) || is_string($breadcrumb['url']))) ? $breadcrumb['url'] : '#',
                        'active'=>isset($breadcrumb['active']) ? (boolean)$breadcrumb['active'] : false,
                        'options'=>(isset($breadcrumb['options']) && is_array($breadcrumb['options'])) ? $breadcrumb['options'] : array()
                    );
                }
            }
            // do not display the breadcrumbs list if it has no links
            if(($c=count($breadcrumbs))===0)
                $this->displayBreadcrumbs=false;
            $this->breadcrumbs=$breadcrumbs;
        }
        if($this->displayLabel)
        {
            if($this->label===null)
                $this->label=MParams::getPageLabel();
        }
        // data for the renderer
        $data=array(
            'afterLabel'=>$this->afterLabel,
            'breadcrumbs'=>$this->breadcrumbs,
            'displayBreadcrumbs'=>$this->displayBreadcrumbs,
            'displayLabel'=>$this->displayLabel,
            'label'=>$this->label,
            'c'=>$c,
            'class','n'
        );
        // render the view file
        $this->render('wContentHeader',$data);
    }
}