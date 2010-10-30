<?php MParams::setPageLabel(Yii::t('page','Create a new member')); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($model)); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('hint','Required: {authRoles}.',
    array(1,'{authRoles}'=>implode(', ',array(Yii::t('t',User::ADMINISTRATOR_T))))
)); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','List of members'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal',
            'visible'=>false,
        ),
        array(
            'text'=>Yii::t('link','Grid of members'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Members'),
            'url'=>array($this->id.'/'.$this->defaultAction),
            'active'=>false,
            'visible'=>Yii::app()->user->checkAccess($this->id.'/'.$this->defaultAction),
        ),
        array(
            'url'=>array($this->action->id),
            'active'=>true,
        ),
    ),
)); ?>
<div class="w3-main-form-box ui-widget-content ui-corner-all">

<?php echo _CHtml::beginForm('','post',array('class'=>'w3-main-form'))."\n"; ?>

<div class="w3-form-row w3-first">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'isActive'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'isActive',$model->getAttributeData('isActive'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php if($model->hasVirtualAttribute('username')): ?>
<div class="w3-form-row">
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
<?php else: ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'email'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'email',array('class'=>'w3-input-text ui-widget-content ui-corner-all','maxlength'=>255))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'screenName'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'screenName',array('class'=>'w3-input-text ui-widget-content ui-corner-all','maxlength'=>32))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
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
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model->details,'initials'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model->details,'initials',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all','maxlength'=>16))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model->details,'occupation'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model->details,'occupation',array('class'=>'w3-input-text ui-widget-content ui-corner-all','maxlength'=>128))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label">&nbsp;</div>
  <div class="w3-form-row-input">
    <div class="w3-form-row-text">
      <?php echo Yii::t('hint','{saveButton} or {cancelLink}',array(
          '{saveButton}'=>_CHtml::submitButton(Yii::t('link','Create'),array('class'=>'w3-input-button ui-state-default ui-corner-all')),
          '{cancelLink}'=>CHtml::link(Yii::t('link','Cancel[form]'),array($this->id.'/')),
      ))."\n"; ?>
    </div>
  </div>
  <div class="clear">&nbsp;</div>
</div>

<?php echo _CHtml::endForm(); ?>

</div><!-- w3-main-form-box -->

<?php MClientScript::registerScript('autocompleteOff',array('selector'=>'.w3-content form.w3-main-form')); ?>
<?php MClientScript::registerScript('focusOnFormFirstItem'); ?>
<?php MClientScript::registerScript('formButton'); ?>
<?php if($model->hasVirtualAttribute('username')): ?>
<?php MClientScript::registerScript('screenNameSame'); ?>
<?php endif; ?>