<?php MParams::setPageLabel(Yii::t('t',$me ? 'Edit my profile' : 'Edit profile')); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($model)); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('user','Some useful links will be added here soon.')); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        $me ?
        array(
            'label'=>Yii::t('t','My profile'),
            'url'=>CHtml::normalizeUrl(array('user/show')),
            'active'=>false
        ) :
        array(
            'label'=>Yii::t('t','Profile of member "{screenName}"',array('{screenName}'=>$screenName)),
            'url'=>CHtml::normalizeUrl(array('user/show','id'=>$_GET['id'])),
            'active'=>false
        ),
        array(
            'url'=>CHtml::normalizeUrl(array($this->getId().'/'.$this->getAction()->getId())),
            'active'=>true
        ),
    ),
)); ?>
<div class="w3-main-form-wrapper ui-widget-content ui-corner-all">

<?php echo _CHtml::beginForm('','post',array('class'=>'w3-main-form'))."\n"; ?>

<div class="w3-form-row w3-first">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($model,'screenName'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'screenName',array('class'=>'w3-input-text ui-widget-content ui-corner-all','maxlength'=>32))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($model,'language'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'language',MParams::getAvailableLanguages(),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($model,'cssTheme'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'cssTheme',MParams::getAvailableCssThemes(),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label">&nbsp;</div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::submitButton(Yii::t('t','Update',array(0)),array('class'=>'w3-input-button ui-button ui-state-default ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>

<?php echo _CHtml::endForm(); ?>

</div><!-- w3-main-form-wrapper -->

<?php Yii::app()->getClientScript()->registerScript('focusOnFirstInput',
"jQuery('.w3-content form.w3-main-form .w3-input-text:first').focus();"); ?>
<?php Yii::app()->getClientScript()->registerScript('focusOnFirstErrorInput',
"jQuery('.w3-content form.w3-main-form .ui-state-error:first').focus();"); ?>
<?php Yii::app()->getClientScript()->registerScript('w3FormButton',
"jQuery('.w3-form-row .w3-input-button').hover(
    function(){ jQuery(this).addClass('ui-state-hover'); },
    function(){ jQuery(this).removeClass('ui-state-hover'); }
)
.mousedown(function(){ jQuery(this).addClass('ui-state-active'); })
.mouseup(function(){ jQuery(this).removeClass('ui-state-active'); });"); ?>