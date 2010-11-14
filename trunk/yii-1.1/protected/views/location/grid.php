<?php MParams::setPageLabel(Yii::t('page','Grid of locations')); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','View as list'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal',
            'visible'=>false,
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
<div class="w3-widget">
<table class="dataGrid">
  <thead>
  <tr>
    <th><?php echo $sort->link('title'); ?></th>
    <th><?php echo $sort->link('address1'); ?></th>
    <th><?php echo $sort->link('address2'); ?></th>
    <th><?php echo $sort->link('city'); ?></th>
    <th><?php echo $sort->link('state'); ?></th>
    <th><?php echo $sort->link('country'); ?></th>
    <th><?php echo $sort->link('zipcode'); ?></th>
    <th><?php echo $sort->link('latitude'); ?></th>
    <th><?php echo $sort->link('longitude'); ?></th>
    <th><?php echo $sort->link('createTime'); ?></th>
    <th><?php echo Yii::t('t','Actions'); ?></th>
  </tr>
  </thead>
  <tbody>
<?php foreach($models as $n=>$model): ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
    <td><?php echo CHtml::encode($model->title); ?></td>
    <td><?php echo CHtml::encode($model->address1); ?></td>
    <td><?php echo CHtml::encode($model->address2); ?></td>
    <td><?php echo CHtml::encode($model->city); ?></td>
    <td><?php echo CHtml::encode($model->state); ?></td>
    <td><?php echo CHtml::encode($model->country); ?></td>
    <td><?php echo CHtml::encode($model->zipcode); ?></td>
    <td><?php echo CHtml::encode($model->latitude); ?></td>
    <td><?php echo CHtml::encode($model->longitude); ?></td>
    <td><?php echo CHtml::encode($model->createTime); ?></td>
    <td>
      <?php echo CHtml::link(Yii::t('link','Show'),array('show','id'=>$model->id)); ?>
      <?php echo CHtml::link(Yii::t('link','Edit'),array('update','id'=>$model->id)); ?>
      <!-- <?php echo CHtml::linkButton('Delete',array(
            'submit'=>'',
            'params'=>array('command'=>'delete','id'=>$model->id),
            'confirm'=>"Are you sure to delete #{$model->id}?")); ?> -->
    </td>
  </tr>
<?php endforeach; ?>
  </tbody>
</table>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>
</div>