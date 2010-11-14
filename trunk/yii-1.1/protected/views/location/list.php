<?php MParams::setPageLabel(Yii::t('page','List of locations')); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','View as grid'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
        array(
            'text'=>Yii::t('link','Add a location'),
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
<?php echo CHtml::encode($model->getAttributeLabel('title')); ?>:
<?php echo CHtml::encode($model->title); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('excerpt')); ?>:
<?php echo CHtml::encode($model->excerpt); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('content')); ?>:
<?php echo CHtml::encode($model->content); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('address1')); ?>:
<?php echo CHtml::encode($model->address1); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('address2')); ?>:
<?php echo CHtml::encode($model->address2); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('city')); ?>:
<?php echo CHtml::encode($model->city); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('state')); ?>:
<?php echo CHtml::encode($model->state); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('country')); ?>:
<?php echo CHtml::encode($model->country); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('zipcode')); ?>:
<?php echo CHtml::encode($model->zipcode); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('latitude')); ?>:
<?php echo CHtml::encode($model->latitude); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('longitude')); ?>:
<?php echo CHtml::encode($model->longitude); ?>
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