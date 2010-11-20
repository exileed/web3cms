<?php MParams::setPageLabel(Yii::t('page','View expense number "{expenseNumber}"',array('{expenseNumber}'=>$model->id))); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','Edit expense'),
            'url'=>array('update','id'=>$model->id),
            'icon'=>'pencil',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/update',array('model'=>$model)),
        ),
        array(
            'text'=>Yii::t('link','Delete expense'),
            'url'=>array('delete','id'=>$model->id),
            'icon'=>'trash',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/delete',array('model'=>$model)),
        ),
        array(
            'text'=>Yii::t('link','List of expenses'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal',
            'visible'=>false,
        ),
        array(
            'text'=>Yii::t('link','Grid of expenses'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
        array(
            'text'=>Yii::t('link','Create a new expense'),
            'url'=>array('create'),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/create'),
        ),
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Expenses'),
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
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('projectId')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->project->id) ? CHtml::link(CHtml::encode($model->project->title),array('project/show','id'=>$model->project->id)) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('managerId')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->manager->id) ? CHtml::link(CHtml::encode($model->manager->screenName),array('user/show','id'=>$model->manager->id)) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('expenseDate')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode(MDate::format($model->expenseDate,'full',null)); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('amount')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->amount); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('billToCompany')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->getAttributeView('billToCompany')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('invoiceId')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->invoice->id) ? CHtml::link(CHtml::encode($model->invoice->id),array('invoice/show','id'=>$model->invoice->id)) : ''; ?></div>
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