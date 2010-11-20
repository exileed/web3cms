<?php MParams::setPageLabel(Yii::t('page','Create a new invoice')); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($model)); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('hint','Required: {authRoles}.',
    array(1,'{authRoles}'=>implode(', ',array(Yii::t('t',User::ADMINISTRATOR_T))))
)); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','List of invoices'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal',
            'visible'=>false,
        ),
        array(
            'text'=>Yii::t('link','Grid of invoices'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Invoices'),
            'url'=>array($this->id.'/'.$this->defaultAction),
            'active'=>false,
            'visible'=>Yii::app()->user->checkAccess($this->id.'/'.$this->defaultAction),
        ),
        array(
            'url'=>array($this->action->id),
            'active'=>true,
        ),
    ),
)); ?>
<?php echo $this->renderPartial('_form', array(
    'model'=>$model,
    'update'=>false,
)); ?>