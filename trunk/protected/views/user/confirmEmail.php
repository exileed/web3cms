<?php MParams::setPageLabel(Yii::t('t','Confirm email address')); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($model)); ?>
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

<div class="w3-form-row w3-first">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($model,'email'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'email',array('class'=>'w3-input-text ui-widget-content ui-corner-all','maxlength'=>255))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($model,'emailConfirmationKey'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'emailConfirmationKey',array('class'=>'w3-input-text ui-widget-content ui-corner-all','maxlength'=>32))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php if(extension_loaded('gd')): ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($model,'verifyCode'); ?></div>
  <div class="w3-form-row-input">
    <div class="w3-form-row-text">
      <?php $this->widget('CCaptcha'); echo "\n"; ?>
      <?php echo _CHtml::activeTextField($model,'verifyCode',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all'))."\n"; ?>
      <br/><?php echo Yii::t('user','Please enter the letters as they are shown in the image above.')."\n"; ?>
      <br/><?php echo Yii::t('user','Letters are not case-sensitive.')."\n"; ?>
    </div>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<div class="w3-form-row">
  <div class="w3-form-row-label">&nbsp;</div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::submitButton(Yii::t('t','Confirm'),array('class'=>'w3-input-button ui-button ui-state-default ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>

<?php echo _CHtml::endForm(); ?>

</div><!-- w3-main-form-wrapper -->

<?php if(!$done): ?>
<?php Yii::app()->getClientScript()->registerScript('focusOnFirstInput',
"jQuery('.w3-content form.w3-main-form .w3-input-text:first').focus();"); ?>
<?php Yii::app()->getClientScript()->registerScript('focusOnFirstErrorInput',
"jQuery('.w3-content form.w3-main-form .ui-state-error:first').focus();"); ?>
<?php endif; ?>
<?php Yii::app()->getClientScript()->registerScript('w3FormButton',
"jQuery('.w3-form-row .w3-input-button').hover(
    function(){ jQuery(this).addClass('ui-state-hover'); },
    function(){ jQuery(this).removeClass('ui-state-hover'); }
)
.mousedown(function(){ jQuery(this).addClass('ui-state-active'); })
.mouseup(function(){ jQuery(this).removeClass('ui-state-active'); });"); ?>