<?php MParams::setPageLabel(Yii::t('page','View "{title}" project',array('{title}'=>$model->title))); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','Edit project'),
            'url'=>array('update','id'=>$model->id),
            'icon'=>'pencil',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/update'),
        ),
        array(
            'text'=>Yii::t('link','Add a task'),
            'url'=>array('task/create','projectId'=>$model->id),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess('task/create'),
        ),
        array(
            'text'=>Yii::t('link','Add an expense'),
            'url'=>array('expense/create','projectId'=>$model->id),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess('expense/create'),
        ),
        array(
            'text'=>Yii::t('link','List of projects'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal',
            'visible'=>false,
        ),
        array(
            'text'=>Yii::t('link','Grid of projects'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
        array(
            'text'=>Yii::t('link','Create a new project'),
            'url'=>array('create'),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/create'),
        ),
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Projects'),
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
  <div class="w3-detail-row-label"><?php echo CHtml::encode(Company2Project::model()->getAttributeLabel('companyId')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->allCompany[0]->id) ? CHtml::link(CHtml::encode($model->allCompany[0]->title),array('company/show','id'=>$model->allCompany[0]->id)) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode(User2Project::model()->getAttributeLabel('managerId')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->allManager[0]->id) ? CHtml::link(CHtml::encode($model->allManager[0]->screenName),array('user/show','id'=>$model->allManager[0]->id)) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('title')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->title); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('hourlyRate')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->hourlyRate); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('priority')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->getAttributeView('priority')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('openDate')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode(MDate::format($model->openDate,'full',null)); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('closeDate')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode(MDate::format($model->closeDate,'full',null)); ?></div>
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