<?php MParams::setPageLabel(Yii::t('page','List of tasks')); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','View as grid'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
        array(
            'text'=>Yii::t('link','Add a task'),
            'url'=>array('create'),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/create'),
        ),
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'url'=>array($this->action->id),
            'active'=>true,
        ),
    ),
)); ?>
<?php if($pages->getPageCount()>=2): ?>
<div style="padding: .5em 0 .9em 0;">
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>
</div>

<?php endif; ?>
<div class="w3-list">
<?php foreach($models as $n=>$model): ?>

<div class="w3-item<?php echo $n ? '' : ' w3-first'; ?> ui-widget-content ui-corner-all">
<?php echo CHtml::encode($model->getAttributeLabel('id')); ?>:
<?php echo CHtml::link($model->id,array('show','id'=>$model->id)); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('projectId')); ?>:
<?php echo isset($model->project->id) ? CHtml::link(CHtml::encode($model->project->title),array('project/show','id'=>$model->project->id)) : ''; ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('companyId')); ?>:
<?php echo isset($model->company->id) ? CHtml::link(CHtml::encode($model->company->title),array('company/show','id'=>$model->company->id)) : ''; ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('title')); ?>:
<?php echo CHtml::encode($model->title); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('excerpt')); ?>:
<?php echo CHtml::encode($model->excerpt); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('content')); ?>:
<?php echo CHtml::encode($model->content); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('isConfirmed')); ?>:
<?php echo CHtml::encode($model->isConfirmed); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('confirmationTime')); ?>:
<?php echo CHtml::encode($model->confirmationTime); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('priority')); ?>:
<?php echo CHtml::encode($model->priority); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('status')); ?>:
<?php echo CHtml::encode($model->status); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('estimateMinute')); ?>:
<?php echo CHtml::encode($model->estimateMinute); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('dueDate')); ?>:
<?php echo CHtml::encode($model->dueDate); ?>
<br/>
<!-- <?php echo CHtml::encode($model->getAttributeLabel('completePercent')); ?>:
<?php echo CHtml::encode($model->completePercent); ?>
<br/> -->
<?php echo CHtml::encode($model->getAttributeLabel('completeDate')); ?>:
<?php echo CHtml::encode($model->completeDate); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('hourlyRate')); ?>:
<?php echo CHtml::encode($model->hourlyRate); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('openDate')); ?>:
<?php echo CHtml::encode($model->openDate); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('closeDate')); ?>:
<?php echo CHtml::encode($model->closeDate); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('report')); ?>:
<?php echo CHtml::encode($model->report); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('reportMarkup')); ?>:
<?php echo CHtml::encode($model->reportMarkup); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('affectedPage')); ?>:
<?php echo CHtml::encode($model->affectedPage); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('documentationUrl')); ?>:
<?php echo CHtml::encode($model->documentationUrl); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('reportingEmail')); ?>:
<?php echo CHtml::encode($model->reportingEmail); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('createTime')); ?>:
<?php echo CHtml::encode($model->createTime); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('updateTime')); ?>:
<?php echo CHtml::encode($model->updateTime); ?>
</div>

<?php endforeach; ?>
</div>
<?php if($pages->getPageCount()>=2): ?>

<br/>
<?php endif; ?>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>