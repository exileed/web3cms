<?php MParams::setPageLabel(Yii::t('page','List of members')); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('feedback','Some useful links will be added here soon.')); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'url'=>CHtml::normalizeUrl(array($this->action->id)),
            'active'=>true
        )
    ),
)); ?>
<?php $this->widget('application.components.WPreItemActionBar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','View as grid'),
            'url'=>array('grid'),
            'icon'=>'calculator'
        ),
        User::isAdministrator() ?
        array(
            'text'=>Yii::t('link','Add a member'),
            'url'=>array('create'),
            'icon'=>'plus'
        ) : null,
    ),
)); ?>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>

<div class="w3-items-list">
<?php foreach($models as $n=>$model): ?>
<div class="w3-item<?php echo $n ? '' : ' w3-first'; ?> ui-widget-content ui-corner-all">
<?php echo CHtml::encode($model->getAttributeLabel('id')); ?>:
<?php echo CHtml::link($model->id,array('show','id'=>$model->id)); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('username')); ?>:
<?php echo CHtml::encode($model->username); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('email')); ?>:
<?php echo CHtml::encode($model->email); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('screenName')); ?>:
<?php echo CHtml::encode($model->screenName); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('language')); ?>:
<?php echo CHtml::encode($model->language); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('interface')); ?>:
<?php echo CHtml::encode($model->interface); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('accessType')); ?>:
<?php echo CHtml::encode($model->accessType); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('accessLevel')); ?>:
<?php echo CHtml::encode($model->accessLevel); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('isActive')); ?>:
<?php echo CHtml::encode($model->isActive); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('createTime')); ?>:
<?php echo CHtml::encode($model->createTime); ?>
<br/>

</div>
<?php endforeach; ?>
</div>

<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>