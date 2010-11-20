<?php MParams::setPageLabel(Yii::t('page','View "{title}" time record',array('{title}'=>$model->title))); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','Edit time record'),
            'url'=>array('update','id'=>$model->id),
            'icon'=>'pencil',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/update',array('model'=>$model)),
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
            'url'=>array($this->action->id,'id'=>$model->id),
            'active'=>true,
        ),
    ),
)); ?>
<div class="w3-detail-box ui-widget-content ui-corner-all">

<div class="w3-detail-row w3-first">
  <div class="w3-detail-row-label"><?php echo CHtml::encode(Task::model()->getAttributeLabel('companyId')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->task->company->id) ? CHtml::link(CHtml::encode($model->task->company->title),array('company/show','id'=>$model->task->company->id)) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode(Task::model()->getAttributeLabel('projectId')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->task->project->id) ? CHtml::link(CHtml::encode($model->task->project->title),array('project/show','id'=>$model->task->project->id)) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('taskId')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->task->id) ? CHtml::link(CHtml::encode($model->task->title),array('task/show','id'=>$model->task->id)) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('managerId')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->manager->id) ? CHtml::link(CHtml::encode($model->manager->screenName),array('user/show','id'=>$model->manager->id)) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('consultantId')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->consultant->id) ? CHtml::link(CHtml::encode($model->consultant->screenName),array('user/show','id'=>$model->consultant->id)) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('timeDate')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode(MDate::format($model->timeDate,'full',null)); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('spentMinute')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->getAttributeView('spentMinute')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php if(Yii::app()->user->checkAccess(User::MANAGER) || Yii::app()->user->checkAccess(User::ADMINISTRATOR)): ?>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('billedMinute')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->getAttributeView('billedMinute')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('title')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->title); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('isConfirmed')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->getAttributeView('isConfirmed')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php if(Yii::app()->user->checkAccess(User::MANAGER) || Yii::app()->user->checkAccess(User::ADMINISTRATOR)): ?>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('confirmationTime')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode(MDate::format($model->confirmationTime,'full')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('invoiceId')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->invoiceId); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('invoiceAmount')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->invoiceAmount); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
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