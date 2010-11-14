<?php MParams::setPageLabel(Yii::t('page','Migration tool')); ?>
<?php MLayout::hideSidebars(); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'displayBreadcrumbs'=>false,
)); ?>
<?php if(!empty($message)): ?>
<?php MUserFlash::setTopSuccess($message); ?>
<?php else: ?>
<div class="w3-main-form-box ui-widget-content ui-corner-all">

<?php echo _CHtml::beginForm('','post',array('class'=>'w3-main-form'))."\n"; ?>

<div class="w3-form-row w3-first">
  <div class="w3-form-row-input w3-form-row-2columns w3-center">
    <?php echo _CHtml::submitButton(Yii::t('link','Migrate from our old Project Management System'),array('class'=>'w3-input-button ui-state-default ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>

<?php echo _CHtml::endForm(); ?>

</div><!-- w3-main-form-box -->

<?php MClientScript::registerScript('focusOnFormFirstItem'); ?>
<?php MClientScript::registerScript('formButton'); ?>
<?php endif; ?>