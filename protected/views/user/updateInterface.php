<?php MParams::setPageLabel(Yii::t('page','Change interface')); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($model)); ?>
<?php if(User::isAdministrator()): ?>
<?php MUserFlash::setSidebarInfo(Yii::t('hint','Required: {authRoles}.',
    array(1,'{authRoles}'=>implode(', ',array(Yii::t('t',User::ADMINISTRATOR_T))))
)); ?>
<?php endif; ?>
<?php MListOfLinks::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>$me ? Yii::t('link','Show my profile') : Yii::t('link','Show member'),
            'url'=>($me && !$idIsSpecified) ? array('show') : array('show','id'=>$model->id),
            'icon'=>'person'
        ),
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Members'),
            'url'=>array($this->id.'/'),
            'active'=>false
        ),
        $me ?
        array(
            'text'=>Yii::t('link','My profile'),
            'url'=>$idIsSpecified ? array('show','id'=>$model->id) : array('show'),
        ) :
        array(
            'text'=>Yii::t('link','"{screenName}" member',array('{screenName}'=>$model->screenName)),
            'url'=>array('show','id'=>$model->id),
        ),
        array(
            'url'=>array($this->action->id),
            'active'=>true
        ),
    ),
)); ?>
<div class="w3-main-form-wrapper ui-widget-content ui-corner-all">

<?php echo _CHtml::beginForm('','post',array('class'=>'w3-main-form'))."\n"; ?>

<div class="w3-form-row w3-first">
  <div class="w3-form-row-input">
    <div class="w3-form-row-text">
      <?php echo _CHtml::activeRadioButtonList($model,'interface',$model->getAttributeData('interface'),array('template'=>'<div style="float: left; height: 145px; text-align: center; width: 160px;">{jqueryUIScreenshot}<br/>{input}&nbsp;{label}</div>','separator'=>"\n"))."\n"; ?>
    </div>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-input w3-form-row-2columns w3-center">
    <?php echo _CHtml::submitButton(Yii::t('link','Apply selected user interface'),array('class'=>'w3-input-button w3-button-big ui-state-default ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>

<?php echo _CHtml::endForm(); ?>

</div><!-- w3-main-form-wrapper -->

<?php Yii::app()->getClientScript()->registerScript('applyInterfaceOnTheFly1',
"function changeJqueryUIDynamically(radioButton){
    if(typeof(radioButton)=='object' && radioButton.length==1){
        var uiSelected = new String(radioButton.attr('value'));
        if(uiSelected.length)
        {
            var pathToCss = '".Yii::app()->request->baseUrl."/css/ui/'+uiSelected+'/jquery-ui-".MParams::jqueryUIVersion.".custom.css';
            var dynamicUI = jQuery('head link#dynamicUIAppended');
            if(dynamicUI.length)
                dynamicUI.attr({href: pathToCss});
            else
                jQuery('head').append('<link rel=\"stylesheet\" type=\"text/css\" href=\"'+pathToCss+'\" id=\"dynamicUIAppended\" />');
        }
    }
}
jQuery('input[type=\"radio\"][name=\"User[interface]\"]').click(
    function(){
        if(jQuery(this).is(':checked')){
            changeJqueryUIDynamically(jQuery(this));
        }
    }
);"); ?>
<?php Yii::app()->getClientScript()->registerScript('applyInterfaceOnTheFly2',
"jQuery('input[type=\"radio\"][name=\"User[interface]\"]').parent().find('img').click(
    function(){
        var radioButton = jQuery(this).parent().find('input[type=\"radio\"][name=\"User[interface]\"]');
        if(radioButton.length){
            radioButton.attr({checked: 'checked'});
            changeJqueryUIDynamically(radioButton);
        }
    }
);"); ?>
<?php MClientScript::registerScript('formButton'); ?>