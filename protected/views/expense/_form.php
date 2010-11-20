<div class="w3-main-form-box ui-widget-content ui-corner-all">

<?php echo _CHtml::beginForm('','post',array('class'=>'w3-main-form'))."\n"; ?>

<div class="w3-form-row w3-first">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'projectId'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'projectId',$model->getAttributeData('projectId'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php /*<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'companyId'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'companyId',$model->getAttributeData('companyId'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>*/ ?>
<?php if(Yii::app()->user->checkAccess(User::ADMINISTRATOR)): ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'managerId'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'managerId',$model->getAttributeData('managerId'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
    <br/><?php echo Yii::t('hint','Required: {authRoles}.',array(1,'{authRoles}'=>implode(', ',array(Yii::t('t',User::ADMINISTRATOR_T)))))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'expenseDate'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'expenseDate',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'amount'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'amount',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all','maxlength'=>15))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'billToCompany'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'billToCompany',$model->getAttributeData('billToCompany'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row w3-first">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'title'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'title',array('class'=>'w3-input-text w3-input-w200percents ui-widget-content ui-corner-all','maxlength'=>255))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
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

<?php MClientScript::registerScript('datepicker',array('selector'=>"input[type='text'][name='Expense[expenseDate]']")); ?>
<?php MClientScript::registerScript('focusOnFormFirstItem'); ?>
<?php MClientScript::registerScript('formButton'); ?>