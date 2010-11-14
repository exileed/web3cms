<?php MParams::setPageLabel(Yii::t('page','List of time records')); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','View as grid'),
            'url'=>array('grid'),
            'icon'=>'calculator',
        ),
        array(
            'text'=>Yii::t('link','Add a time record'),
            'url'=>array('create'),
            'icon'=>'plus',
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
<?php echo CHtml::encode($model->getAttributeLabel('taskId')); ?>:
<?php echo CHtml::encode($model->taskId); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('managerId')); ?>:
<?php echo CHtml::encode($model->managerId); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('consultantId')); ?>:
<?php echo CHtml::encode($model->consultantId); ?>
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
<?php echo CHtml::encode($model->getAttributeLabel('timeDate')); ?>:
<?php echo CHtml::encode($model->timeDate); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('spentMinute')); ?>:
<?php echo CHtml::encode($model->spentMinute); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('billedMinute')); ?>:
<?php echo CHtml::encode($model->billedMinute); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('invoiceId')); ?>:
<?php echo CHtml::encode($model->invoiceId); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('invoiceAmount')); ?>:
<?php echo CHtml::encode($model->invoiceAmount); ?>
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