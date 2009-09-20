<h2>user List</h2>

<div class="actionBar">
[<?php echo CHtml::link('New user',array('create')); ?>]
[<?php echo CHtml::link('Manage user',array('admin')); ?>]
</div>

<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>

<?php foreach($models as $n=>$model): ?>
<div class="item">
<?php echo CHtml::encode($model->getAttributeLabel('id')); ?>:
<?php echo CHtml::link($model->id,array('show','id'=>$model->id)); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('username')); ?>:
<?php echo CHtml::encode($model->username); ?>
<br/>
<?php echo CHtml::encode($model->getAttributeLabel('password')); ?>:
<?php echo CHtml::encode($model->password); ?>
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
<?php echo CHtml::encode($model->getAttributeLabel('theme')); ?>:
<?php echo CHtml::encode($model->theme); ?>
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
<?php echo CHtml::encode($model->getAttributeLabel('createDate')); ?>:
<?php echo CHtml::encode($model->createDate); ?>
<br/>

</div>
<?php endforeach; ?>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>