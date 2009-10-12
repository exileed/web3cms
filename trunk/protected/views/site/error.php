<?php MParams::setPageLabel(Yii::t('page','Error {errorCode}',array('{errorCode}'=>$error['code']))); ?>
<?php MUserFlash::setTopError($error['message']); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('feedback','Some useful links will be added here soon.')); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'displayBreadcrumbs'=>false,
)); ?>