<?php MParams::setPageLabel(Yii::t('page','Edit company')); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($model)); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','Show company'),
            'url'=>array('show','id'=>$model->id),
            'icon'=>'zoomin',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/show'),
        ),
        array(
            'text'=>Yii::t('link','Add a project'),
            'url'=>array('project/create','companyId'=>$model->id),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess('project/create'),
        ),
        array(
            'text'=>Yii::t('link','Add a company payment'),
            'url'=>array('companyPayment/create','companyId'=>$model->id),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess('companyPayment/create'),
        ),
        array(
            'text'=>Yii::t('link','Add an invoice'),
            'url'=>array('invoice/create','companyId'=>$model->id),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess('invoice/create'),
        ),
        array(
            'text'=>Yii::t('link','List of companies'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal',
            'visible'=>false,
        ),
        array(
            'text'=>Yii::t('link','Grid of companies'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
        array(
            'text'=>Yii::t('link','Create a new company'),
            'url'=>array('create'),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/create'),
        ),
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Companies'),
            'url'=>array($this->id.'/'.$this->defaultAction),
            'active'=>false,
            'visible'=>Yii::app()->user->checkAccess($this->id.'/'.$this->defaultAction),
        ),
        array(
            'text'=>Yii::t('link','"{title}" company',array('{title}'=>$model->title)),
            'url'=>array('show','id'=>$model->id),
            'active'=>false,
            'visible'=>Yii::app()->user->checkAccess($this->id.'/show'),
        ),
        array(
            'url'=>CHtml::normalizeUrl(array($this->action->id,'id'=>$model->id)),
            'active'=>true,
        ),
    ),
)); ?>
<?php echo $this->renderPartial('_form', array(
    'model'=>$model,
    'update'=>true,
)); ?>