<?php MParams::setPageLabel(Yii::t('page','Forum'));
MLinkList::set('sidebar',array(
        'links'=>array(
                array(
                        'text'=>Yii::t('link','Add a section'),
                        'url'=>array('forum/newsection'),
                        'icon'=>'plus',
                        'visible'=>User::isAdministrator() && $this->action->id != 'newsection',
                ),
        ),
));

$arr = array(
        array(
                'text'=>Yii::t('link', 'Forum'),
                'url'=>array('forum/index'),
                'active'=>true));
if (isset($breadcrumbs)) array_push($arr, $breadcrumbs);
$this->widget('application.components.WContentHeader', array(
        'afterLabel'=>false,
        'breadcrumbs'=>$arr,
        )
); ?>