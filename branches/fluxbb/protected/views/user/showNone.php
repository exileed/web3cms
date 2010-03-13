<?php MParams::setPageLabel(Yii::t('page','Member not found')); ?>
<?php MUserFlash::setTopError(Yii::t('hint','The requested member does not exist.')); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
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
<?php MUserFlash::setSidebarInfo(Yii::t('hint','The requested member was deleted, inactivated or did not exist. Try to browse or search among all members.')); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Members'),
            'url'=>array($this->id.'/'),
            'active'=>false
        ),
    ),
)); ?>