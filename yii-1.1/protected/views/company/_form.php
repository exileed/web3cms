<div class="w3-main-form-box ui-widget-content ui-corner-all">

<?php echo _CHtml::beginForm('','post',array('class'=>'w3-main-form'))."\n"; ?>

<div class="w3-form-row w3-first">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'title'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'title',array('class'=>'w3-input-text w3-input-w200percents ui-widget-content ui-corner-all','maxlength'=>255))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'titleAbbr'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'titleAbbr',array('class'=>'w3-input-text w3-input-w25percents ui-widget-content ui-corner-all','maxlength'=>128))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'contactName'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'contactName',array('class'=>'w3-input-text ui-widget-content ui-corner-all','maxlength'=>255))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'contactEmail'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'contactEmail',array('class'=>'w3-input-text ui-widget-content ui-corner-all','maxlength'=>255))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php /* start user2company */ ?>
<?php if(isset($model->allUser2Company[0])): ?>
<?php if($model->allUser2Company[0]->companyId!==null): ?>
<?php echo _CHtml::activeHiddenField($model->allUser2Company[0],'companyId')."\n"; ?>
<?php endif; ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($model->allUser2Company[0],'userId'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model->allUser2Company[0],'userId',$model->allUser2Company[0]->getAttributeData('userId'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<?php /* end user2company */ ?>
<?php /* start location */ ?>
<?php if(isset($model->allLocation[0])): ?>
<?php if($model->allLocation[0]->id!==null): ?>
<?php echo _CHtml::activeHiddenField($model->allLocation[0],'id')."\n"; ?>
<?php endif; ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($model->allLocation[0],'address1'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model->allLocation[0],'address1',array('class'=>'w3-input-text w3-input-w200percents ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($model->allLocation[0],'address2'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model->allLocation[0],'address2',array('class'=>'w3-input-text w3-input-w200percents ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($model->allLocation[0],'city'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model->allLocation[0],'city',array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($model->allLocation[0],'state'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model->allLocation[0],'state',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($model->allLocation[0],'zipcode'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model->allLocation[0],'zipcode',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<?php /* end location */ ?>
<?php /* continue company */ ?>
<?php if(Yii::app()->user->checkAccess(User::MANAGER) || Yii::app()->user->checkAccess(User::ADMINISTRATOR)): ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'invoiceDueDay'); ?></div>
  <div class="w3-form-row-input">
    <div class="w3-form-row-text">
      <?php echo _CHtml::activeTextField($model,'invoiceDueDay',array('class'=>'w3-input-text w3-input-w25percents ui-widget-content ui-corner-all','maxlength'=>4))."\n"; ?>
      <?php echo Yii::t('t','(days)'); ?>
    </div>
    <?php echo Yii::t('hint','Required: {authRoles}.',array(2,'{authRoles}'=>implode(', ',array(Yii::t('t',User::MANAGER_T),Yii::t('t',User::ADMINISTRATOR_T)))))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'isActive'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'isActive',$model->getAttributeData('isActive'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
    <br/><?php echo Yii::t('hint','Required: {authRoles}.',array(2,'{authRoles}'=>implode(', ',array(Yii::t('t',User::MANAGER_T),Yii::t('t',User::ADMINISTRATOR_T)))))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'content'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextArea($model,'content',array('class'=>'w3-input-text w3-input-w200percents ui-widget-content ui-corner-all','rows'=>10,'cols'=>50))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row w3-last">
  <div class="w3-form-row-label">&nbsp;</div>
  <div class="w3-form-row-input">
    <div class="w3-form-row-text">
      <?php echo Yii::t('hint','{saveButton} or {cancelLink}',
      $update ?
          array(
              '{saveButton}'=>_CHtml::submitButton(Yii::t('link','Save'),array('class'=>'w3-input-button ui-state-default ui-corner-all')),
              '{cancelLink}'=>CHtml::link(Yii::t('link','Cancel[form]'),array('show','id'=>$model->id)),
          ) :
          array(
              '{saveButton}'=>_CHtml::submitButton(Yii::t('link','Create'),array('class'=>'w3-input-button ui-state-default ui-corner-all')),
              '{cancelLink}'=>CHtml::link(Yii::t('link','Cancel[form]'),array($this->id.'/')),
          )
      )."\n"; ?>
    </div>
  </div>
  <div class="clear">&nbsp;</div>
</div>

<?php echo _CHtml::endForm(); ?>

</div><!-- w3-main-form-box -->

<?php MClientScript::registerScript('focusOnFormFirstItem'); ?>
<?php MClientScript::registerScript('formButton'); ?>