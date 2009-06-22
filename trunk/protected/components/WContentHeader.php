<?php
/**
 * Widget Content Header
 * 
 * Content breadcrumbs list and h1 page label
 */
class WContentHeader extends CWidget
{
    public $displayLabel=true;
    public $label='';
    public $afterLabel=true;
    public $displayBreadcrumbs=true;
    public $breadcrumbs=array();
    public $prependHome=true;

    public function run()
    {
        if($this->prependHome)
            array_unshift($this->breadcrumbs,array(
                'label'=>'Home',
                'url'=>Yii::app()->homeUrl,
                'active'=>false
            ));
        for($i=0;$i<count($this->breadcrumbs);$i++)
            !isset($this->breadcrumbs[$i]['active']) && $this->breadcrumbs[$i]['active']=false;
        $data=array(
            'displayLabel'=>$this->displayLabel,
            'label'=>$this->label,
            'afterLabel'=>$this->afterLabel,
            'displayBreadcrumbs'=>$this->displayBreadcrumbs,
            'breadcrumbs'=>$this->breadcrumbs,
        );
        $this->render('wContentHeader',$data);
    }
}