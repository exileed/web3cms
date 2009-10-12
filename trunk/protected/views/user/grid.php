<?php MParams::setPageLabel(Yii::t('page','Grid of members')); ?>
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
            'text'=>Yii::t('link','View as list'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal'
        ),
        User::isAdministrator() ?
        array(
            'text'=>Yii::t('link','Add a member'),
            'url'=>array('create'),
            'icon'=>'plus'
        ) : null,
    ),
)); ?>
<div class="w3-widget">
<table class="dataGrid">
  <tr>
    <th><?php echo $sort->link('id'); ?></th>
    <th><?php echo $sort->link('username'); ?></th>
    <th><?php echo $sort->link('email'); ?></th>
    <th><?php echo $sort->link('screenName'); ?></th>
    <th><?php echo $sort->link('language'); ?></th>
    <th><?php echo $sort->link('interface'); ?></th>
    <th><?php echo $sort->link('accessType'); ?></th>
    <th><?php echo $sort->link('accessLevel'); ?></th>
    <th><?php echo $sort->link('isActive'); ?></th>
    <th><?php echo $sort->link('createTime'); ?></th>
    <th>Actions</th>
  </tr>
<?php foreach($models as $n=>$model): ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
    <td><?php echo CHtml::link($model->id,array('show','id'=>$model->id)); ?></td>
    <td><?php echo CHtml::encode($model->username); ?></td>
    <td><?php echo CHtml::encode($model->email); ?></td>
    <td><?php echo CHtml::encode($model->screenName); ?></td>
    <td><?php echo CHtml::encode($model->language); ?></td>
    <td><?php echo CHtml::encode($model->interface); ?></td>
    <td><?php echo CHtml::encode($model->accessType); ?></td>
    <td><?php echo CHtml::encode($model->accessLevel); ?></td>
    <td><?php echo CHtml::encode($model->isActive); ?></td>
    <td><?php echo CHtml::encode(MDate::format($model->createTime,'short')); ?></td>
    <td>
      <?php echo CHtml::link('Update',array('update','id'=>$model->id)); ?>
      <!-- <?php echo CHtml::linkButton('Delete',array(
          'submit'=>'',
          'params'=>array('command'=>'delete','id'=>$model->id),
          'confirm'=>"Are you sure to delete #{$model->id}?")); ?> -->
    </td>
  </tr>
<?php endforeach; ?>
</table>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>
</div>