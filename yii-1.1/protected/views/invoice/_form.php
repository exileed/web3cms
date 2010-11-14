<div class="w3-main-form-box ui-widget-content ui-corner-all">

<?php echo _CHtml::beginForm('','post',array('class'=>'w3-main-form'))."\n"; ?>

<div class="w3-form-row w3-first">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'companyId'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeDropDownList($model,'companyId',$model->getAttributeData('companyId'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<?php /*<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'invoiceDate'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'invoiceDate',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>*/ ?>
<?php /*<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'amountTotal'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'amountTotal',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all','maxlength'=>12))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>*/ ?>
<?php /*<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'billedMinute'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'billedMinute',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all','maxlength'=>6))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>*/ ?>
<?php /*<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'amountTime'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'amountTime',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all','maxlength'=>12))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>*/ ?>
<?php /*<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'amountExpense'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'amountExpense',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all','maxlength'=>12))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>*/ ?>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'startDate'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'startDate',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'endDate'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'endDate',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'dueDate'); ?></div>
  <div class="w3-form-row-input">
    <?php echo _CHtml::activeTextField($model,'dueDate',array('class'=>'w3-input-text w3-input-w50percents ui-widget-content ui-corner-all'))."\n"; ?>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
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

<?php MClientScript::registerScript('datepicker',array('selector'=>"input[type='text'][name='Invoice[invoiceDate]']")); ?>
<?php MClientScript::registerScript('datepicker',array('selector'=>"input[type='text'][name='Invoice[dueDate]']")); ?>
<?php MClientScript::registerScript('datepicker',array('selector'=>"input[type='text'][name='Invoice[startDate]']")); ?>
<?php MClientScript::registerScript('datepicker',array('selector'=>"input[type='text'][name='Invoice[endDate]']")); ?>
<?php MClientScript::registerScript('focusOnFormFirstItem'); ?>
<?php MClientScript::registerScript('formButton'); ?>