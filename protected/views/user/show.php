<h2>View user <?php echo $model->id; ?></h2>

<div class="actionBar">
[<?php echo CHtml::link('user List',array('list')); ?>]
[<?php echo CHtml::link('New user',array('create')); ?>]
[<?php echo CHtml::link('Update user',array('update','id'=>$model->id)); ?>]
[<?php echo CHtml::linkButton('Delete user',array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure?')); ?>
]
[<?php echo CHtml::link('Manage user',array('admin')); ?>]
</div>

<table class="dataGrid">
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('username')); ?>
</th>
    <td><?php echo CHtml::encode($model->username); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('password')); ?>
</th>
    <td><?php echo CHtml::encode($model->password); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('email')); ?>
</th>
    <td><?php echo CHtml::encode($model->email); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('displayName')); ?>
</th>
    <td><?php echo CHtml::encode($model->displayName); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('language')); ?>
</th>
    <td><?php echo CHtml::encode($model->language); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('theme')); ?>
</th>
    <td><?php echo CHtml::encode($model->theme); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('accessType')); ?>
</th>
    <td><?php echo CHtml::encode($model->accessType); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('accessLevel')); ?>
</th>
    <td><?php echo CHtml::encode($model->accessLevel); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('isActive')); ?>
</th>
    <td><?php echo CHtml::encode($model->isActive); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('createdOn')); ?>
</th>
    <td><?php echo CHtml::encode($model->createdOn); ?>
</td>
</tr>
</table>
