<div class="yiiForm">

<p>
Fields with <span class="required">*</span> are required.
</p>

<?php echo CHtml::beginForm(); ?>

<?php echo CHtml::errorSummary($model); ?>

<div class="simple">
<?php echo CHtml::activeLabelEx($model,'username'); ?>
<?php echo CHtml::activeTextField($model,'username',array('size'=>60,'maxlength'=>128)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'password'); ?>
<?php echo CHtml::activePasswordField($model,'password',array('size'=>60,'maxlength'=>128)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'email'); ?>
<?php echo CHtml::activeTextField($model,'email',array('size'=>60,'maxlength'=>255)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'screenName'); ?>
<?php echo CHtml::activeTextField($model,'screenName',array('size'=>60,'maxlength'=>128)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'language'); ?>
<?php echo CHtml::activeTextField($model,'language',array('size'=>16,'maxlength'=>16)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'theme'); ?>
<?php echo CHtml::activeTextField($model,'theme',array('size'=>32,'maxlength'=>32)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'accessType'); ?>
<?php echo CHtml::activeTextField($model,'accessType',array('size'=>32,'maxlength'=>32)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'accessLevel'); ?>
<?php echo CHtml::activeCheckBox($model,'accessLevel'); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'isActive'); ?>
<?php echo CHtml::activeTextField($model,'isActive',array('size'=>0,'maxlength'=>0)); ?>
</div>
<div class="simple">
<?php echo CHtml::activeLabelEx($model,'createdOn'); ?>
<?php echo CHtml::activeTextField($model,'createdOn'); ?>
</div>

<div class="action">
<?php echo CHtml::submitButton($update ? 'Save' : 'Create'); ?>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- yiiForm -->