<?php MParams::setPageLabel(Yii::t('page','Edit task')); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($model)); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','Show task'),
            'url'=>array('show','id'=>$model->id),
            'icon'=>'zoomin',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/show'),
        ),
        array(
            'text'=>Yii::t('link','Add a time record'),
            'url'=>array('time/create','taskId'=>$model->id),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess('time/create'),
        ),
        array(
            'text'=>Yii::t('link','List of tasks'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal',
            'visible'=>false,
        ),
        array(
            'text'=>Yii::t('link','Grid of tasks'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
        array(
            'text'=>Yii::t('link','Create a new task'),
            'url'=>array('create'),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/create'),
        ),
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Tasks'),
            'url'=>array($this->id.'/'.$this->defaultAction),
            'active'=>false,
            'visible'=>Yii::app()->user->checkAccess($this->id.'/'.$this->defaultAction),
        ),
        array(
            'text'=>Yii::t('link','"{title}" task',array('{title}'=>$model->title)),
            'url'=>array('show','id'=>$model->id),
            'active'=>false,
            'visible'=>Yii::app()->user->checkAccess($this->id.'/show'),
        ),
        array(
            'url'=>array($this->action->id,'id'=>$model->id),
            'active'=>true,
        ),
    ),
)); ?>
<?php echo $this->renderPartial('_form', array(
    'model'=>$model,
    'update'=>true,
)); ?>