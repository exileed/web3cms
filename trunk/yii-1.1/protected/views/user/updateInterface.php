<?php MParams::setPageLabel(Yii::t('page','Change interface')); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($model)); ?>
<?php if(Yii::app()->user->checkAccess(User::ADMINISTRATOR)): ?>
<?php MUserFlash::setSidebarInfo(Yii::t('hint','Required: {authRoles}.',
    array(1,'{authRoles}'=>implode(', ',array(Yii::t('t',User::ADMINISTRATOR_T))))
)); ?>
<?php endif; ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','Show my profile'),
            'url'=>!$pkIsPassed ? array('show') : array('show','id'=>$model->id),
            'icon'=>'person',
            'visible'=>$model->isMe,
        ),
        array(
            'text'=>Yii::t('link','Edit my profile'),
            'url'=>!$pkIsPassed ? array('update') : array('update','id'=>$model->id),
            'icon'=>'pencil',
            'visible'=>$model->isMe,
        ),
        array(
            'text'=>Yii::t('link','Show member'),
            'url'=>array('show','id'=>$model->id),
            'icon'=>'person',
            'visible'=>!$model->isMe && Yii::app()->user->checkAccess($this->id.'/show'),
        ),
        array(
            'text'=>Yii::t('link','Edit member\'s profile'),
            'url'=>array('update','id'=>$model->id),
            'icon'=>'pencil',
            'visible'=>!$model->isMe && Yii::app()->user->checkAccess($this->id.'/update'),
        ),
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
        array(
            'text'=>Yii::t('link','Create a new member'),
            'url'=>array('create'),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/create'),
        ),
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Members'),
            'url'=>array($this->id.'/'),
            'active'=>false,
        ),
        array(
            'text'=>Yii::t('link','My profile'),
            'url'=>$pkIsPassed ? array('show','id'=>$model->id) : array('show'),
            'visible'=>$model->isMe,
        ),
        array(
            'text'=>Yii::t('link','"{screenName}" member',array('{screenName}'=>$model->screenName)),
            'url'=>array('show','id'=>$model->id),
            'visible'=>!$model->isMe,
        ),
        array(
            'url'=>($model->isMe&&!$pkIsPassed) ? array($this->action->id) : array($this->action->id,'id'=>$model->id),
            'active'=>true,
        ),
    ),
)); ?>
<div class="w3-main-form-box ui-widget-content ui-corner-all">

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
    <div class="w3-form-row-text">
      <?php echo Yii::t('hint','{saveButton} or {cancelLink}',array(
          '{saveButton}'=>_CHtml::submitButton(Yii::t('link','Apply selected user interface'),array('class'=>'w3-input-button w3-button-big ui-state-default ui-corner-all')),
          '{cancelLink}'=>CHtml::link(Yii::t('link','Cancel[form]'),($model->isMe && !$pkIsPassed) ? array('show') : array('show','id'=>$model->id)),
      ))."\n"; ?>
    </div>
  </div>
  <div class="clear">&nbsp;</div>
</div>

<?php echo _CHtml::endForm(); ?>

</div><!-- w3-main-form-box -->

<?php Yii::app()->getClientScript()->registerScript('applyInterfaceOnTheFly1',
"function changeJqueryUIDynamically(radioButton){
    if(typeof(radioButton)=='object' && radioButton.length==1){
        var uiSelected = new String(radioButton.attr('value'));
        if(uiSelected.length)
        {
            var pathToCss = '".Yii::app()->request->baseUrl."/static/css/ui/'+uiSelected+'/jquery-ui-".MParams::jqueryUIVersion.".custom.css';
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