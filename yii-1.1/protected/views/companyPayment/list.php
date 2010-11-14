<?php MParams::setPageLabel(Yii::t('page','List of company payments')); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','View as grid'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
        array(
            'text'=>Yii::t('link','Add a company payment'),
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
<?php echo CHtml::encode($model->getAttributeLabel('companyId')); ?>:
<?php echo isset($model->company->id) ? CHtml::link(CHtml::encode($model->company->title),array('company/show','id'=>$model->company->id)) : ''; ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('paymentDate')); ?>:
<?php echo CHtml::encode(MDate::format($model->paymentDate,'full',null)); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('amount')); ?>:
<?php echo CHtml::encode($model->amount); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('paymentMethod')); ?>:
<?php echo CHtml::encode($model->getAttributeView('paymentMethod')); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('paymentNumber')); ?>:
<?php echo CHtml::encode($model->paymentNumber); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('content')); ?>:
<?php echo CHtml::encode($model->content); ?>
<br/>
<?php echo CHtml::link(Yii::t('link','Show'),array('show','id'=>$model->id))."\n"; ?>
<?php echo CHtml::link(Yii::t('link','Edit'),array('update','id'=>$model->id))."\n"; ?>
</div>

<?php endforeach; ?>
</div>
<?php if($pages->getPageCount()>=2): ?>

<br/>
<?php endif; ?>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>