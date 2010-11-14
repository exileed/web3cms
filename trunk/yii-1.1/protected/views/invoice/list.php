<?php MParams::setPageLabel(Yii::t('page','List of invoices')); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','View as grid'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
        array(
            'text'=>Yii::t('link','Add an invoice'),
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
<?php echo CHtml::encode($model->getAttributeLabel('companyId')); ?>:
<?php echo CHtml::encode($model->companyId); ?>
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
<?php echo CHtml::encode($model->getAttributeLabel('invoiceDate')); ?>:
<?php echo CHtml::encode($model->invoiceDate); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('amountTotal')); ?>:
<?php echo CHtml::encode($model->amountTotal); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('dueDate')); ?>:
<?php echo CHtml::encode($model->dueDate); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('billedMinute')); ?>:
<?php echo CHtml::encode($model->billedMinute); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('amountTime')); ?>:
<?php echo CHtml::encode($model->amountTime); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('amountExpense')); ?>:
<?php echo CHtml::encode($model->amountExpense); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('startDate')); ?>:
<?php echo CHtml::encode($model->startDate); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('endDate')); ?>:
<?php echo CHtml::encode($model->endDate); ?>
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