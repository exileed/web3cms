<?php MParams::setPageLabel(Yii::t('page','Member not found')); ?>
<?php MUserFlash::setTopError(Yii::t('hint','The requested member does not exist.')); ?>
<?php $this->var->links=array(); ?>
<?php if(User::isAdministrator()): ?>
<?php /*$this->var->links=array_merge($this->var->links,array(array(
    'text'=>Yii::t('link','List of members'),
    'url'=>array('list'),
    'icon'=>'grip-solid-horizontal'
)));*/ ?>
<?php $this->var->links=array_merge($this->var->links,array(array(
    'text'=>Yii::t('link','Grid of members'),
    'url'=>array('grid'),
    'icon'=>'calculator'
))); ?>
<?php $this->var->links=array_merge($this->var->links,array(array(
    'text'=>Yii::t('link','Create a new member'),
    'url'=>array('create'),
    'icon'=>'plus'
))); ?>
<?php endif; ?>
<?php if(count($this->var->links)): ?>
<?php MListOfLinks::set('sidebar',array('links'=>$this->var->links)); ?>
<?php else: ?>
<?php MUserFlash::setSidebarInfo(Yii::t('hint','The requested member was deleted, inactivated or did not exist. Try to browse or search among all members.')); ?>
<?php endif; ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Members'),
            'url'=>array($this->id.'/'),
            'active'=>false
        ),
    ),
)); ?>