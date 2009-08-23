<?php MParams::setPageLabel(Yii::t('t','Profile of member "{screenName}"',array('{screenName}'=>$model->screenName))); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('t','Some useful links will be added here soon.')); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'url'=>CHtml::normalizeUrl(array($this->getId().'/'.$this->getAction()->getId())),
            'active'=>true
        ),
    ),
)); ?>
<?php if($me || $admin): ?>
<div class="w3-pre-grid-action-bar ui-widget">
  <ul>
    <li class="ui-state-default ui-corner-all w3-first w3-last"><?php echo CHtml::link('<span class="w3-inner-icon-left ui-icon ui-icon-pencil"></span>'.Yii::t('t',$me ? 'Edit my profile' : 'Edit profile'),$me ? array('user/update') : array('user/update','id'=>$model->id),array('class'=>'w3-with-icon')); ?></li>
  </ul>
</div>
<div class="clear">&nbsp;</div>
<?php Yii::app()->getClientScript()->registerScript('w3FormButton',
"jQuery('.w3-pre-grid-action-bar ul li a').hover(
    function(){ jQuery(this).parent().removeClass('ui-state-default').addClass('ui-state-hover'); }, 
    function(){ jQuery(this).parent().removeClass('ui-state-hover').addClass('ui-state-default'); } 
)
.mousedown(function(){ jQuery(this).parent().addClass('ui-state-active'); })
.mouseup(function(){ jQuery(this).parent().removeClass('ui-state-active'); });"); ?>

<?php endif; ?>
<div class="w3-data-grid ui-widget-content ui-corner-all">

<?php if($me || $admin): ?>
<div class="w3-grid-row w3-first">
  <div class="w3-grid-row-label"><?php echo CHtml::encode($model->getAttributeLabel('username')); ?></div>
  <div class="w3-grid-row-value"><?php echo CHtml::encode($model->username); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-grid-row">
  <div class="w3-grid-row-label"><?php echo CHtml::encode($model->getAttributeLabel('email')); ?></div>
  <div class="w3-grid-row-value"><?php echo CHtml::encode($model->email); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<div class="w3-grid-row<?php echo ($me || $admin) ? '' : ' w3-first'; ?>">
  <div class="w3-grid-row-label"><?php echo CHtml::encode($model->getAttributeLabel('screenName')); ?></div>
  <div class="w3-grid-row-value"><?php echo CHtml::encode($model->screenName); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-grid-row">
  <div class="w3-grid-row-label"><?php echo CHtml::encode($model->getAttributeLabel('language')); ?></div>
  <div class="w3-grid-row-value"><?php echo CHtml::encode($model->getAttributeView('language')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-grid-row">
  <div class="w3-grid-row-label"><?php echo CHtml::encode($model->getAttributeLabel('cssTheme')); ?></div>
  <div class="w3-grid-row-value"><?php echo CHtml::encode($model->getAttributeView('cssTheme')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php if($me || $admin): ?>
<div class="w3-grid-row">
  <div class="w3-grid-row-label"><?php echo CHtml::encode($model->getAttributeLabel('accessType')); ?></div>
  <div class="w3-grid-row-value"><?php echo CHtml::encode($model->getAttributeView('accessType')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-grid-row">
  <div class="w3-grid-row-label"><?php echo CHtml::encode($model->getAttributeLabel('accessLevel')); ?></div>
  <div class="w3-grid-row-value"><?php echo CHtml::encode($model->accessLevel); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-grid-row">
  <div class="w3-grid-row-label"><?php echo CHtml::encode($model->getAttributeLabel('isActive')); ?></div>
  <div class="w3-grid-row-value"><?php echo CHtml::encode($model->getAttributeView('isActive')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<div class="w3-grid-row">
  <div class="w3-grid-row-label"><?php echo CHtml::encode($model->getAttributeLabel('createdOn')); ?></div>
  <div class="w3-grid-row-value"><?php echo CHtml::encode($model->createdOn); ?></div>
  <div class="clear">&nbsp;</div>
</div>

</div>
