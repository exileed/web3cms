<?php MParams::setPageLabel($model->isMe ? Yii::t('page','Edit my profile') : Yii::t('page','Edit member\'s profile')); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($model)); ?>
<?php MUserFlash::setTopError(_CHtml::errorSummary($model->details)); ?>
<?php if(Yii::app()->user->checkAccess(User::ADMINISTRATOR)): // FIXME: remove - deprecated ?>
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
            'text'=>Yii::t('link','Change interface'),
            'url'=>!$pkIsPassed ? array('updateInterface') : array('updateInterface','id'=>$model->id),
            'visible'=>$model->isMe,
        ),
        array(
            'text'=>Yii::t('link','Show member'),
            'url'=>array('show','id'=>$model->id),
            'icon'=>'person',
            'visible'=>!$model->isMe && Yii::app()->user->checkAccess($this->id.'/show'),
        ),
        array(
            'text'=>Yii::t('link','Change interface'),
            'url'=>array('updateInterface','id'=>$model->id),
            'visible'=>!$model->isMe && Yii::app()->user->checkAccess($this->id.'/updateInterface'),
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
            'url'=>array($this->id.'/'.$this->defaultAction),
            'active'=>false,
            'visible'=>Yii::app()->user->checkAccess($this->id.'/'.$this->defaultAction),
        ),
        array(
            'text'=>Yii::t('link','My profile'),
            'url'=>$pkIsPassed ? array('show','id'=>$model->id) : array('show'),
            'visible'=>$model->isMe,
        ),
        array(
            'text'=>Yii::t('link','"{screenName}" member',array('{screenName}'=>$model->screenName)),
            'url'=>array('show','id'=>$model->id),
            'visible'=>!$model->isMe && Yii::app()->user->checkAccess($this->id.'/show'),
        ),
        array(
            'url'=>($model->isMe&&!$pkIsPassed) ? array($this->action->id) : array($this->action->id,'id'=>$model->id),
            'active'=>true,
        ),
    ),
)); ?>
<div class="w3-main-form-box ui-widget-content ui-corner-all">

<?php echo _CHtml::beginForm('','post',array('class'=>'w3-main-form'))."\n"; ?>

<?php if(Yii::app()->user->checkAccess(User::ADMINISTRATOR)): ?>
<div class="w3-form-row w3-first">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'isActive'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'isActive',$model->getAttributeData('isActive'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
    <br/><?php echo Yii::t('hint','Required: {authRoles}.',array(1,'{authRoles}'=>implode(', ',array(Yii::t('t',User::ADMINISTRATOR_T)))))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php $this->var->isNotW3First=true; ?>
<?php endif; ?>
<div class="w3-form-row<?php echo $this->var->isNotW3First ? '' : ' w3-first'; ?>">
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
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model->details,'initials'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model->details,'initials',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all','maxlength'=>16))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php if(Yii::app()->user->checkAccess(User::ADMINISTRATOR)): ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'accessType'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'accessType',$model->getAttributeData('accessType'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
    <br/><?php echo Yii::t('hint','Required: {authRoles}.',array(1,'{authRoles}'=>implode(', ',array(Yii::t('t',User::ADMINISTRATOR_T)))))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'language'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'language',$model->getAttributeData('language'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
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
<?php if($model->hasVirtualAttribute('isEmailVisible')): ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model->details,'isEmailVisible'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model->details,'isEmailVisible',$model->details->getAttributeData('isEmailVisible'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<div class="w3-form-row">
  <div class="w3-form-row-label">&nbsp;</div>
  <div class="w3-form-row-input">
    <div class="w3-form-row-text">
      <?php echo Yii::t('hint','{saveButton} or {cancelLink}',array(
          '{saveButton}'=>_CHtml::submitButton(Yii::t('link','Save'),array('class'=>'w3-input-button ui-state-default ui-corner-all')),
          '{cancelLink}'=>CHtml::link(Yii::t('link','Cancel[form]'),($model->isMe && !$pkIsPassed) ? array('show') : array('show','id'=>$model->id)),
      ))."\n"; ?>
    </div>
  </div>
  <div class="clear">&nbsp;</div>
</div>

<?php echo _CHtml::endForm(); ?>

</div><!-- w3-main-form-box -->

<?php MClientScript::registerScript('focusOnFormFirstItem'); ?>
<?php MClientScript::registerScript('formButton'); ?>