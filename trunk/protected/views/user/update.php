<h2>Update user <?php echo $model->id; ?></h2>

<div class="actionBar">
[<?php echo CHtml::link('user List',array('list')); ?>]
[<?php echo CHtml::link('New user',array('create')); ?>]
[<?php echo CHtml::link('Manage user',array('admin')); ?>]
</div>

<?php echo $this->renderPartial('_form', array(
	'model'=>$model,
	'update'=>true,
)); ?>