<?php
/**
 * Widget Content Header
 * 
 * Content breadcrumbs list and h1 page label
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
        is_null($this->displayLabel) && ($this->displayLabel=true);
        is_null($this->label) && ($this->label=MParams::getPageLabel());
        is_null($this->afterLabel) && ($this->afterLabel=true);
        is_null($this->displayBreadcrumbs) && ($this->displayBreadcrumbs=true);
        is_null($this->breadcrumbs) && ($this->breadcrumbs=array());
        is_null($this->prependHome) && ($this->prependHome=true);
        // add home link to the beginning of breadcrumbs
        if($this->prependHome)
            array_unshift($this->breadcrumbs,array(
                'label'=>Yii::t('t','Home',array(1)),
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
        );
        // call the view
        $this->render('wContentHeader',$data);
    }
}