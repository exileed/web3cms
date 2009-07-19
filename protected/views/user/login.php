<?php MParams::setPageLabel(Yii::t('w3','Login')); ?>
<?php MParams::setCssTheme('ui-lightness'); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($form)); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('w3','Hint: You may login with <tt>demo/demo</tt> or <tt>admin/admin</tt>.')); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'url'=>CHtml::normalizeUrl(array($this->getId().'/'.$this->getAction()->getId())),
            'active'=>true
        ),
    ),
)); ?>
<div class="w3-main-form-wrapper ui-widget-content ui-corner-all">

<?php echo _CHtml::beginForm('','post',array('class'=>'w3-main-form'))."\n"; ?>
<?php echo _CHtml::activeHiddenField($form,'loginWithField')."\n"; ?>

<div class="w3-form-row first">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($form,$form->getLoginWithField()); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($form,$form->getLoginWithField(),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($form,'password'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activePasswordField($form,'password',array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php if(Yii::app()->user->allowAutoLogin): ?>
<div class="w3-form-row">
  <div class="w3-form-row-label">&nbsp;</div>
  <div class="w3-form-row-input">
    <div class="w3-form-row-text">
      <?php echo _CHtml::activeCheckBox($form,'rememberMe')."\n"; ?>
      <?php echo _CHtml::activeLabel($form,'rememberMe')."\n"; ?>
    </div>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<div class="w3-form-row">
  <div class="w3-form-row-label">&nbsp;</div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::submitButton('Login',array('class'=>'w3-input-button ui-button ui-state-default ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>

<?php echo _CHtml::endForm(); ?>

</div><!-- w3-main-form-wrapper -->

<?php Yii::app()->getClientScript()->registerScript('w3-form-button',
"jQuery('.w3-form-row .w3-input-button').hover(
    function(){ jQuery(this).addClass('ui-state-hover'); },
    function(){ jQuery(this).removeClass('ui-state-hover'); }
)
.mousedown(function(){ jQuery(this).addClass('ui-state-active'); })
.mouseup(function(){ jQuery(this).removeClass('ui-state-active'); });"); ?>