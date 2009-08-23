<h2>Managing user</h2>

<div class="actionBar">
[<?php echo CHtml::link('user List',array('list')); ?>]
[<?php echo CHtml::link('New user',array('create')); ?>]
</div>

<table class="dataGrid">
  <tr>
    <th><?php echo $sort->link('id'); ?></th>
    <th><?php echo $sort->link('username'); ?></th>
    <th><?php echo $sort->link('password'); ?></th>
    <th><?php echo $sort->link('email'); ?></th>
    <th><?php echo $sort->link('screenName'); ?></th>
    <th><?php echo $sort->link('language'); ?></th>
    <th><?php echo $sort->link('theme'); ?></th>
    <th><?php echo $sort->link('accessType'); ?></th>
    <th><?php echo $sort->link('accessLevel'); ?></th>
    <th><?php echo $sort->link('isActive'); ?></th>
    <th><?php echo $sort->link('createdOn'); ?></th>
    <th>Actions</th>
  </tr>
<?php foreach($models as $n=>$model): ?>
  <tr class="<?php echo $n%2?'even':'odd';?>">
    <td><?php echo CHtml::link($model->id,array('show','id'=>$model->id)); ?></td>
    <td><?php echo CHtml::encode($model->username); ?></td>
    <td><?php echo CHtml::encode($model->password); ?></td>
    <td><?php echo CHtml::encode($model->email); ?></td>
    <td><?php echo CHtml::encode($model->screenName); ?></td>
    <td><?php echo CHtml::encode($model->language); ?></td>
    <td><?php echo CHtml::encode($model->theme); ?></td>
    <td><?php echo CHtml::encode($model->accessType); ?></td>
    <td><?php echo CHtml::encode($model->accessLevel); ?></td>
    <td><?php echo CHtml::encode($model->isActive); ?></td>
    <td><?php echo CHtml::encode($model->createdOn); ?></td>
    <td>
      <?php echo CHtml::link('Update',array('update','id'=>$model->id)); ?>
      <?php echo CHtml::linkButton('Delete',array(
          'submit'=>'',
          'params'=>array('command'=>'delete','id'=>$model->id),
          'confirm'=>"Are you sure to delete #{$model->id}?")); ?>
    </td>
  </tr>
<?php endforeach; ?>
</table>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>