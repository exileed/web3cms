<?php MParams::setPageLabel(Yii::t('t','Change interface')); ?>
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
<div class="w3-pre-grid-action-bar ui-widget">
  <ul>
    <li class="ui-state-default ui-corner-all w3-first w3-last"><?php echo CHtml::link('<span class="w3-inner-icon-left ui-icon ui-icon-person"></span>'.Yii::t('t',$me ? 'My profile' : 'View profile'),$me ? array('user/show') : array('user/show','id'=>$model->id),array('class'=>'w3-with-icon')); ?></li>
  </ul>
</div>
<div class="clear">&nbsp;</div>

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
    <?php echo _CHtml::submitButton(Yii::t('t','Apply selected user interface'),array('class'=>'w3-input-button w3-button-big ui-button ui-state-default ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>

<?php echo _CHtml::endForm(); ?>

</div><!-- w3-main-form-wrapper -->

<?php Yii::app()->getClientScript()->registerScript('w3ActionButton',
"jQuery('.w3-pre-grid-action-bar ul li a').hover(
    function(){ jQuery(this).parent().removeClass('ui-state-default').addClass('ui-state-hover'); }, 
    function(){ jQuery(this).parent().removeClass('ui-state-hover').addClass('ui-state-default'); } 
)
.mousedown(function(){ jQuery(this).parent().addClass('ui-state-active'); })
.mouseup(function(){ jQuery(this).parent().removeClass('ui-state-active'); });"); ?>
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
<?php Yii::app()->getClientScript()->registerScript('w3FormButton',
"jQuery('.w3-form-row .w3-input-button').hover(
    function(){ jQuery(this).addClass('ui-state-hover'); },
    function(){ jQuery(this).removeClass('ui-state-hover'); }
)
.mousedown(function(){ jQuery(this).addClass('ui-state-active'); })
.mouseup(function(){ jQuery(this).removeClass('ui-state-active'); });"); ?>