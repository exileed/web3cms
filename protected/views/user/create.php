<h2>New user</h2>

<div class="actionBar">
[<?php echo CHtml::link('user List',array('list')); ?>]
[<?php echo CHtml::link('Manage user',array('admin')); ?>]
</div>

<?php echo $this->renderPartial('_form', array(
	'model'=>$model,
	'update'=>false,
)); ?>