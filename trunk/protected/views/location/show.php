<?php MParams::setPageLabel(Yii::t('page','View "{title}" location',array('{title}'=>$model->title))); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','Edit location'),
            'url'=>array('update','id'=>$model->id),
            'icon'=>'pencil',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/update'),
        ),
        array(
            'text'=>Yii::t('link','List of locations'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal',
            'visible'=>false,
        ),
        array(
            'text'=>Yii::t('link','Grid of locations'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
        array(
            'text'=>Yii::t('link','Create a new location'),
            'url'=>array('create'),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/create'),
        ),
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Locations'),
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
<div class="w3-widget">
<table class="dataGrid">
<tr>
    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('title')); ?></th>
    <td><?php echo CHtml::encode($model->title); ?></td>
</tr>
<tr>
    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('excerpt')); ?></th>
    <td><?php echo CHtml::encode($model->excerpt); ?></td>
</tr>
<tr>
    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('content')); ?></th>
    <td><?php echo CHtml::encode($model->content); ?></td>
</tr>
<tr>
    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('address1')); ?></th>
    <td><?php echo CHtml::encode($model->address1); ?></td>
</tr>
<tr>
    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('address2')); ?></th>
    <td><?php echo CHtml::encode($model->address2); ?></td>
</tr>
<tr>
    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('city')); ?></th>
    <td><?php echo CHtml::encode($model->city); ?></td>
</tr>
<tr>
    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('state')); ?></th>
    <td><?php echo CHtml::encode($model->state); ?></td>
</tr>
<tr>
    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('country')); ?></th>
    <td><?php echo CHtml::encode($model->country); ?></td>
</tr>
<tr>
    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('zipcode')); ?></th>
    <td><?php echo CHtml::encode($model->zipcode); ?></td>
</tr>
<tr>
    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('latitude')); ?></th>
    <td><?php echo CHtml::encode($model->latitude); ?></td>
</tr>
<tr>
    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('longitude')); ?></th>
    <td><?php echo CHtml::encode($model->longitude); ?></td>
</tr>
<tr>
    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('createTime')); ?></th>
    <td><?php echo CHtml::encode($model->createTime); ?></td>
</tr>
<tr>
    <th class="label"><?php echo CHtml::encode($model->getAttributeLabel('updateTime')); ?></th>
    <td><?php echo CHtml::encode($model->updateTime); ?></td>
</tr>
</table>
</div>