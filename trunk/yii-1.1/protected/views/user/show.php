<?php MParams::setPageLabel($model->isMe ? Yii::t('page','View my profile') : Yii::t('page','View "{screenName}" member',array('{screenName}'=>$model->screenName))); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','Edit my profile'),
            'url'=>!$pkIsPassed ? array('update') : array('update','id'=>$model->id),
            'icon'=>'pencil',
            'visible'=>$model->isMe,
        ),
        array(
            'text'=>Yii::t('link','Change interface'),
            'url'=>!$pkIsPassed ? array('updateInterface') : array('updateInterface','id'=>$model->id),
            'visible'=>$model->isMe,
        ),
        array(
            'text'=>Yii::t('link','Edit member\'s profile'),
            'url'=>array('update','id'=>$model->id),
            'icon'=>'pencil',
            'visible'=>!$model->isMe && User::isAdministrator(),
        ),
        array(
            'text'=>Yii::t('link','Change interface'),
            'url'=>array('updateInterface','id'=>$model->id),
            'visible'=>!$model->isMe && User::isAdministrator(),
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
            'visible'=>User::isAdministrator(),
        ),
        array(
            'text'=>Yii::t('link','Create a new member'),
            'url'=>array('create'),
            'icon'=>'plus',
            'visible'=>User::isAdministrator(),
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
            'url'=>($model->isMe&&!$pkIsPassed) ?
                array($this->action->id) :
                array($this->action->id,'id'=>$model->id)
            ,
            'active'=>true,
        ),
    ),
)); ?>
<div class="w3-detail-box ui-widget-content ui-corner-all">

<?php if($model->isMe || User::isAdministrator()): ?>
<?php if(!$model->isMe): ?>
<div class="w3-detail-row<?php echo $this->var->isNotW3First ? '' : ' w3-first'; ?>">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('isActive')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->getAttributeView('isActive')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php $this->var->isNotW3First=true; ?>
<?php endif; ?>
<?php if($model->hasVirtualAttribute('username')): ?>
<div class="w3-detail-row<?php echo $this->var->isNotW3First ? '' : ' w3-first'; ?>">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('username')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->username); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php $this->var->isNotW3First=true; ?>
<?php endif; ?>
<div class="w3-detail-row<?php echo $this->var->isNotW3First ? '' : ' w3-first'; ?>">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('email')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->email); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php $this->var->isNotW3First=true; ?>
<?php elseif($model->details->isEmailVisible()): ?>
<div class="w3-detail-row<?php echo $this->var->isNotW3First ? '' : ' w3-first'; ?>">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('email')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->email); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php $this->var->isNotW3First=true; ?>
<?php endif; ?>
<div class="w3-detail-row<?php echo $this->var->isNotW3First ? '' : ' w3-first'; ?>">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('screenName')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->screenName); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->details->getAttributeLabel('initials')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->details->initials); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php if($model->isMe): ?>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('language')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->getAttributeView('language')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('User interface')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->getAttributeView('interface')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<?php if($model->isMe || User::isAdministrator()): ?>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('accessType')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->getAttributeView('accessType')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php if($model->hasVirtualAttribute('accessLevel')): ?>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('accessLevel')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->accessLevel); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->details->getAttributeLabel('occupation')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->details->occupation); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php if($model->hasVirtualAttribute('isEmailConfirmed')): ?>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->details->getAttributeLabel('isEmailConfirmed')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->details->getAttributeView('isEmailConfirmed')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<?php if($model->hasVirtualAttribute('isEmailVisible')): ?>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->details->getAttributeLabel('isEmailVisible')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->details->getAttributeView('isEmailVisible')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<?php if(!empty($model->details->deactivationTime)): ?>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->details->getAttributeLabel('deactivationTime')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode(MDate::format($model->details->deactivationTime,'full')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<?php endif; /*end if isMe || admin*/ ?>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('createTime')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode(MDate::format($model->createTime,'full')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php if(($model->isMe || User::isAdministrator())/* && $model->details->updateTime*/): ?>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->details->getAttributeLabel('updateTime')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode(MDate::format($model->details->updateTime,'full')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<?php if($model->hasVirtualAttribute('id') && ($model->isMe || User::isAdministrator())): ?>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('id')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->id); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>

</div>