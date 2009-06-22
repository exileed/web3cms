<?php $this->setPageLabel(Yii::t('w3','Contact Us')); ?>
<?php MUserFlash::setTopError(CHtml::errorSummary($contact)); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('w3','If you have business inquries or other questions, please fill out this form to contact us. Thank you.')); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'label'=>$this->getPageLabel(),
    'breadcrumbs'=>array(
        array(
            'label'=>$this->getPageLabel(),
            'url'=>CHtml::normalizeUrl(array($this->getId().'/'.$this->getAction()->getId())),
            'active'=>true
        ),
    ),
)); ?>
<div class="w3-main-form-wrapper ui-widget-content ui-corner-all">

<?php echo CHtml::beginForm('','post',array('class'=>'w3-main-form')); ?>

<div class="w3-form-row first">
  <div class="w3-form-row-label">
    <?php echo CHtml::activeLabel($contact,'name'); ?>
  </div>
  <div class="w3-form-row-input">
    <?php echo CHtml::activeTextField($contact,'name',array('class'=>'w3-input-text ui-widget-content ui-corner-all')); ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label">
    <?php echo CHtml::activeLabel($contact,'email'); ?>
  </div>
  <div class="w3-form-row-input">
    <?php echo CHtml::activeTextField($contact,'email',array('class'=>'w3-input-text ui-widget-content ui-corner-all')); ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label">
    <?php echo CHtml::activeLabel($contact,'subject'); ?>
  </div>
  <div class="w3-form-row-input">
    <?php echo CHtml::activeTextField($contact,'subject',array('class'=>'w3-input-text w3-input-w200percents ui-widget-content ui-corner-all','maxlength'=>128)); ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label">
    <?php echo CHtml::activeLabel($contact,'body'); ?>
  </div>
  <div class="w3-form-row-input">
    <?php echo CHtml::activeTextArea($contact,'body',array('class'=>'w3-input-text w3-input-w200percents ui-widget-content ui-corner-all','rows'=>6)); ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>

<?php if(extension_loaded('gd')): ?>
<div class="w3-form-row">
  <div class="w3-form-row-label">
    <?php echo CHtml::activeLabel($contact,'verifyCode'); ?>
  </div>
  <div class="w3-form-row-input">
    <?php $this->widget('CCaptcha'); ?>
    <?php echo CHtml::activeTextField($contact,'verifyCode',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all')); ?>
    <br/>Please enter the letters as they are shown in the image above.
    <br/>Letters are not case-sensitive.
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>

<div class="w3-form-row">
  <div class="w3-form-row-label">&nbsp;</div>
  <div class="w3-form-row-input">
    <?php echo CHtml::submitButton('Submit',array('class'=>'w3-input-button ui-button ui-state-default ui-corner-all')); ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- w3-main-form-wrapper -->

<?php Yii::app()->getClientScript()->registerScript('w3-form-button',
"jQuery('.w3-form-row .w3-input-button').hover(
    function(){ jQuery(this).addClass('ui-state-hover'); },
    function(){ jQuery(this).removeClass('ui-state-hover'); }
)
.mousedown(function(){ jQuery(this).addClass('ui-state-active'); })
.mouseup(function(){ jQuery(this).removeClass('ui-state-active'); });"); ?>