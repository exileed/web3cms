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
    public $displayLabel;
    public $label;
    public $afterLabel;
    public $displayBreadcrumbs;
    public $breadcrumbs;
    public $prependHome;

    public function run()
    {
        // init
        $this->displayLabel===null && ($this->displayLabel=true);
        $this->label===null && ($this->label=MParams::getPageLabel());
        $this->afterLabel===null && ($this->afterLabel=true);
        $this->displayBreadcrumbs===null && ($this->displayBreadcrumbs=true);
        $this->breadcrumbs===null && ($this->breadcrumbs=array());
        $this->prependHome===null && ($this->prependHome=true);
        // add home link to the beginning of breadcrumbs
        if($this->prependHome)
            array_unshift($this->breadcrumbs,array(
                'label'=>Yii::t('link','Home'),
                'url'=>Yii::app()->homeUrl,
                'active'=>false
            ));
        // make sure 'active' is set
        for($i=0;$i<count($this->breadcrumbs);$i++)
        {
            !isset($this->breadcrumbs[$i]['label']) && ($this->breadcrumbs[$i]['label']=MParams::getPageLabel());
            !isset($this->breadcrumbs[$i]['active']) && ($this->breadcrumbs[$i]['active']=false);
        }
        // pack variables
        $data=array(
            'displayLabel'=>$this->displayLabel,
            'label'=>$this->label,
            'afterLabel'=>$this->afterLabel,
            'displayBreadcrumbs'=>$this->displayBreadcrumbs,
            'breadcrumbs'=>$this->breadcrumbs,
            'c'=>count($this->breadcrumbs),
            'n','liClass'
        );
        // render the view file
        $this->render('wContentHeader',$data);
    }
}