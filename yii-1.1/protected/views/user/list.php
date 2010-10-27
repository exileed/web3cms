<?php MParams::setPageLabel(Yii::t('page','List of members')); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','View as grid'),
            'url'=>array('grid'),
            'icon'=>'calculator',
        ),
        array(
            'text'=>Yii::t('link','Add a member'),
            'url'=>array('create'),
            'icon'=>'plus',
            'visible'=>User::isAdministrator(),
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
<?php echo CHtml::encode($model->getAttributeLabel('username')); ?>:
<?php echo CHtml::encode($model->username); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('email')); ?>:
<?php echo CHtml::encode($model->email); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('screenName')); ?>:
<?php echo CHtml::encode($model->screenName); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('accessType')); ?>:
<?php echo CHtml::encode($model->getAttributeView('accessType')); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('isActive')); ?>:
<?php echo CHtml::encode($model->isActive); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('createTime')); ?>:
<?php echo CHtml::encode(MDate::format($model->createTime,'full')); ?>
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