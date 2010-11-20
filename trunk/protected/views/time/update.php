<?php MParams::setPageLabel(Yii::t('page','Edit time record')); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($model)); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('hint','Required: {authRoles}.',
    $model->invoiceId>=1 ?
    array(1,'{authRoles}'=>implode(', ',array(Yii::t('t',User::ADMINISTRATOR_T)))) :
    array(2,'{authRoles}'=>implode(', ',array(Yii::t('t',User::MANAGER_T),Yii::t('t',User::ADMINISTRATOR_T))))
)); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','Show time record'),
            'url'=>array('show','id'=>$model->id),
            'icon'=>'zoomin',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/show'),
        ),
        array(
            'text'=>Yii::t('link','Delete time record'),
            'url'=>array('delete','id'=>$model->id),
            'icon'=>'trash',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/delete',array('model'=>$model)),
        ),
        array(
            'text'=>Yii::t('link','List of time records'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal',
            'visible'=>false,
        ),
        array(
            'text'=>Yii::t('link','Grid of time records'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
        array(
            'text'=>Yii::t('link','Create a new time record'),
            'url'=>array('create'),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/create'),
        ),
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Time records'),
            'url'=>array($this->id.'/'.$this->defaultAction),
            'active'=>false,
            'visible'=>Yii::app()->user->checkAccess($this->id.'/'.$this->defaultAction),
        ),
        array(
            'text'=>Yii::t('link','"{title}" time record',array('{title}'=>$model->title)),
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