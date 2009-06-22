<?php $this->setPageLabel(Yii::t('w3','Login')); ?>
<?php MLayout::setCssTheme('ui-lightness'); ?>
<?php MUserFlash::setTopError(CHtml::errorSummary($form)); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('w3','Hint: You may login with <tt>demo/demo</tt> or <tt>admin/admin</tt>.')); ?>
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
    <?php echo CHtml::activeLabel($form,'username'); ?>
  </div>
  <div class="w3-form-row-input">
    <?php echo CHtml::activeTextField($form,'username',array('class'=>'w3-input-text ui-widget-content ui-corner-all')) ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label">
    <?php echo CHtml::activeLabel($form,'password'); ?>
  </div>
  <div class="w3-form-row-input">
    <?php echo CHtml::activePasswordField($form,'password',array('class'=>'w3-input-text ui-widget-content ui-corner-all')) ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label">&nbsp;</div>
  <div class="w3-form-row-input">
    <?php echo CHtml::activeCheckBox($form,'rememberMe'); ?>
    <?php echo CHtml::activeLabel($form,'rememberMe'); ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label">&nbsp;</div>
  <div class="w3-form-row-input">
    <?php echo CHtml::submitButton('Login',array('class'=>'w3-input-button ui-button ui-state-default ui-corner-all')); ?>
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