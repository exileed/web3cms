<?php MParams::setPageLabel(Yii::t('page','View company payment number "{paymentNumber}"',array(
    '{paymentNumber}'=>empty($model->paymentNumber) ? $model->id : $model->paymentNumber
))); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','Edit company payment'),
            'url'=>array('update','id'=>$model->id),
            'icon'=>'pencil',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/update'),
        ),
        array(
            'text'=>Yii::t('link','List of company payments'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal',
            'visible'=>false,
        ),
        array(
            'text'=>Yii::t('link','Grid of company payments'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
        array(
            'text'=>Yii::t('link','Create a new company payment'),
            'url'=>array('create'),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/create'),
        ),
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Company payments'),
            'url'=>array($this->id.'/'.$this->defaultAction),
            'active'=>false,
            'visible'=>Yii::app()->user->checkAccess($this->id.'/'.$this->defaultAction),
        ),
        array(
            'url'=>array($this->action->id,'id'=>$model->id),
            'active'=>true,
        ),
    ),
)); ?>
<div class="w3-detail-box ui-widget-content ui-corner-all">

<div class="w3-detail-row w3-first">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('companyId')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->company->id) ? CHtml::link(CHtml::encode($model->company->title),array('company/show','id'=>$model->company->id)) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('paymentDate')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode(MDate::format($model->paymentDate,'full',null)); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('amount')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->amount); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('paymentMethod')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->getAttributeView('paymentMethod')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('paymentNumber')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->paymentNumber); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('createTime')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode(MDate::format($model->createTime,'full')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('updateTime')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode(MDate::format($model->updateTime,'full')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('title')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->title); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('id')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->id); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('content')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->content); ?></div>
  <div class="clear">&nbsp;</div>
</div>

</div>