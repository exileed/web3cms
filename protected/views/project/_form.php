<div class="w3-main-form-box ui-widget-content ui-corner-all">

<?php echo _CHtml::beginForm('','post',array('class'=>'w3-main-form'))."\n"; ?>

<div class="w3-form-row w3-first">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'title'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'title',array('class'=>'w3-input-text w3-input-w200percents ui-widget-content ui-corner-all','maxlength'=>255))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php /* start company2project */ ?>
<?php if(isset($model->allCompany2Project[0])): ?>
<?php if($model->allCompany2Project[0]->projectId!==null): ?>
<?php echo _CHtml::activeHiddenField($model->allCompany2Project[0],'projectId')."\n"; ?>
<?php endif; ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($model->allCompany2Project[0],'companyId'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model->allCompany2Project[0],'companyId',$model->allCompany2Project[0]->getAttributeData('companyId'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php endif; ?>
<?php /* end company2project */ ?>
<?php /* start manager2project */ ?>
<?php if(isset($model->allManager2Project[0])): ?>
<?php if($model->allManager2Project[0]->projectId!==null): ?>
<?php echo _CHtml::activeHiddenField($model->allManager2Project[0],'projectId')."\n"; ?>
<?php endif; ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabel($model->allManager2Project[0],'managerId'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model->allManager2Project[0],'userId',$model->allManager2Project[0]->getAttributeData('managerId'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php echo _CHtml::activeHiddenField($model->allManager2Project[0],'role')."\n"; ?>
<?php endif; ?>
<?php /* end manager2project */ ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'hourlyRate'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'hourlyRate',array('class'=>'w3-input-text w3-input-w25percents ui-widget-content ui-corner-all','maxlength'=>9))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'priority'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'priority',$model->getAttributeData('priority'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'openDate'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'openDate',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'closeDate'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'closeDate',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all'))."\n"; ?>
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

<?php MClientScript::registerScript('datepicker',array('selector'=>"input[type='text'][name='Project[openDate]']")); ?>
<?php MClientScript::registerScript('datepicker',array('selector'=>"input[type='text'][name='Project[closeDate]']")); ?>
<?php MClientScript::registerScript('focusOnFormFirstItem'); ?>
<?php MClientScript::registerScript('formButton'); ?>