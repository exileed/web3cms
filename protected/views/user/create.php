<?php MParams::setPageLabel(Yii::t('page','Create a new member')); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($model)); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('feedback','Some useful links will be added here soon.')); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'label'=>Yii::t('link','Members'),
            'url'=>CHtml::normalizeUrl(array($this->id.'/')),
            'active'=>false
        ),
        array(
            'url'=>CHtml::normalizeUrl(array($this->action->id)),
            'active'=>true
        ),
    ),
)); ?>
<?php $this->widget('application.components.WPreItemActionBar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','List of members'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal'
        ),
        array(
            'text'=>Yii::t('link','Grid of members'),
            'url'=>array('grid'),
            'icon'=>'calculator'
        ),
    ),
)); ?>
<div class="w3-main-form-wrapper ui-widget-content ui-corner-all">

<?php echo _CHtml::beginForm('','post',array('class'=>'w3-main-form'))."\n"; ?>

<div class="w3-form-row w3-first">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'username'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'username',array('class'=>'w3-input-text ui-widget-content ui-corner-all','maxlength'=>32))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'screenName'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'screenName',array('class'=>'w3-input-text ui-widget-content ui-corner-all','maxlength'=>32))."\n"; ?>
    <div class="w3-form-row-text">
      <?php echo _CHtml::activeCheckBox($model,'screenNameSame')."\n"; ?>
      <?php echo _CHtml::activeLabelEx($model,'screenNameSame')."\n"; ?>
    </div>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'email'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'email',array('class'=>'w3-input-text ui-widget-content ui-corner-all','maxlength'=>255))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'password'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activePasswordField($model,'password',array('class'=>'w3-input-text ui-widget-content ui-corner-all','maxlength'=>64))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'language'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'language',$model->getAttributeData('language'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'interface'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'interface',$model->getAttributeData('interface'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label">&nbsp;</div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::submitButton(Yii::t('link','Create'),array('class'=>'w3-input-button ui-button ui-state-default ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>

<?php echo _CHtml::endForm(); ?>

</div><!-- w3-main-form-wrapper -->

<?php MClientScript::registerScript('autocompleteOff',array('selector'=>'.w3-content form.w3-main-form')); ?>
<?php MClientScript::registerScript('focusOnFormFirstItem'); ?>
<?php MClientScript::registerScript('screenNameSame'); ?>
<?php MClientScript::registerScript('w3FormButton'); ?>