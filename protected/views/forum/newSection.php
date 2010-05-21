<?php
MParams::setPageLabel(Yii::t('page','Add a section'));
MUserFlash::setTopError(_CHtml::errorSummary($model));
MUserFlash::setSidebarInfo(Yii::t('hint','Required: {authRoles}.',    array(2,'{authRoles}'=>implode(', ',array(Yii::t('t',User::MANAGER_T),Yii::t('t',User::ADMINISTRATOR_T))))
));?>
<?php echo $this->renderPartial('_common'); ?>
<div class="w3-main-form-box ui-widget-content ui-corner-all">
    <?php echo _CHtml::beginForm('','post',array('class'=>'w3-main-form'))."\n"; ?>
    <div class="w3-form-row w3-first">
        <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'name'); ?></div>
        <div class="w3-form-row-input">
            <?php echo _CHtml::activeTextField($model,'name',array('class'=>'w3-input-text ui-widget-content ui-corner-all','maxlength'=>255))."\n"; ?>
        </div>
        <div class="clear">&nbsp;</div>
    </div>
    <div class="w3-form-row">
        <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'description'); ?></div>
        <div class="w3-form-row-input">
            <?php echo _CHtml::activeTextArea($model,'description',array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
        </div>
        <div class="clear">&nbsp;</div>
    </div>
    <div class="w3-form-row">
        <div class="w3-form-row-label"><?php echo _CHtml::activeLabelEx($model,'isActive'); ?></div>
        <div class="w3-form-row-input">
            <?php echo _CHtml::activeDropDownList($model,'isActive',array('isActive'),array('class'=>'w3-input-text ui-widget-content ui-corner-all'))."\n"; ?>
        </div>
        <div class="clear">&nbsp;</div>
    </div>
    <div class="w3-form-row">
        <div class="w3-form-row-label">&nbsp;</div>
        <div class="w3-form-row-input">
            <div class="w3-form-row-text">
                <?php echo Yii::t('hint','{saveButton} or {cancelLink}',array(
                        '{saveButton}'=>_CHtml::submitButton(Yii::t('link','Add'),array('class'=>'w3-input-button ui-state-default ui-corner-all')),
                        '{cancelLink}'=>CHtml::link(Yii::t('link','Cancel[form]'),array($this->id.'/')),
                        ))."\n"; ?>
            </div>
        </div>
        <div class="clear">&nbsp;</div>
    </div>
    <?php echo _CHtml::endForm(); ?>
</div><!-- w3-main-form-box -->