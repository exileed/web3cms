<?php MParams::setPageLabel($me ? Yii::t('page','Edit my profile') : Yii::t('page','Edit member\'s profile')); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($model)); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($model->details)); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('feedback','Some useful links will be added here soon.')); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'label'=>Yii::t('link','Members'),
            'url'=>CHtml::normalizeUrl(array($this->id.'/')),
            'active'=>false
        ),
        $me ?
        array(
            'label'=>Yii::t('link','My profile'),
            'url'=>CHtml::normalizeUrl($idIsSpecified?array('show','id'=>$model->id):array('show')),
        ) :
        array(
            'label'=>Yii::t('link','"{screenName}" member',array('{screenName}'=>$model->screenName)),
            'url'=>CHtml::normalizeUrl(array('show','id'=>$model->id)),
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
            'text'=>$me ? Yii::t('link','Show my profile') : Yii::t('link','Show member'),
            'url'=>($me && !$idIsSpecified) ? array('show') : array('show','id'=>$model->id),
            'icon'=>'person'
        ),
        User::isAdministrator() ?
        array(
            'text'=>Yii::t('link','Create a new member'),
            'url'=>array('create'),
            'icon'=>'plus'
        ) : null,
    ),
)); ?>
<div class="w3-main-form-wrapper ui-widget-content ui-corner-all">

<?php echo _CHtml::beginForm('','post',array('class'=>'w3-main-form'))."\n"; ?>

<div class="w3-form-row w3-first">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'screenName'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'screenName',array('class'=>'w3-input-text ui-widget-content ui-corner-all','maxlength'=>32))."\n"; ?>
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
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model->details,'isEmailVisible'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model->details,'isEmailVisible',$model->details->getAttributeData('isEmailVisible'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label">&nbsp;</div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::submitButton(Yii::t('link','Save'),array('class'=>'w3-input-button ui-button ui-state-default ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>

<?php echo _CHtml::endForm(); ?>

</div><!-- w3-main-form-wrapper -->

<?php MClientScript::registerScript('focusOnFormFirstItem'); ?>
<?php MClientScript::registerScript('w3FormButton'); ?>