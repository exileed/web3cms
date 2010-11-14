<?php MParams::setPageLabel(Yii::t('page','Grid of companies')); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('hint','Required: {authRoles}.',
    array(3,'{authRoles}'=>implode(', ',array(Yii::t('t',User::CLIENT_T),Yii::t('t',User::MANAGER_T),Yii::t('t',User::ADMINISTRATOR_T))))
)); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','View as list'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal',
            'visible'=>false,
        ),
        array(
            'text'=>Yii::t('link','Add a company'),
            'url'=>array('create'),
            'icon'=>'plus',
        ),
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'afterLabel'=>false,
    'breadcrumbs'=>array(
        array(
            'url'=>array($this->action->id),
            'active'=>true,
        ),
    ),
)); ?>
<?php $this->widget('application.components.WPreItemActionBar',array(
    'links'=>array(
        array(
            'dropDown'=>array('links'=>$allState),
            'text'=>CHtml::encode($stateLinkText),
            'options'=>array('title'=>Yii::t('t','State[company]'))
        ),
    ),
)); ?>
<?php $this->widget('application.components.WGrid',array(
    'columns'=>array(
        array('title'=>CHtml::encode($sort->resolveLabel('title'))),
        array('title'=>CHtml::encode($sort->resolveLabel('titleAbbr'))),
        array('title'=>CHtml::encode($sort->resolveLabel('contactName'))),
        array('title'=>CHtml::encode($sort->resolveLabel('createTime'))),
        array('title'=>CHtml::encode($sort->resolveLabel('deactivationTime'))),
        array('title'=>CHtml::encode(Yii::t('t','Actions'))),
    ),
    'columnsModel'=>array(
        array('name'=>'title','width'=>290),
        array('name'=>'titleAbbr','width'=>50),
        array('name'=>'contactName','width'=>150),
        array('name'=>'createTime','width'=>75,'align'=>'right'),
        array('name'=>'deactivationTime','width'=>74,'align'=>'right'),
        array('name'=>'actions','width'=>59,'sortable'=>false),
    ),
    'pages'=>$pages,
    'rowNum'=>CompanyController::GRID_PAGE_SIZE,
    'rows'=>$gridRows,
    'sColumns'=>array(
        array('title'=>$sort->link('title')),
        array('title'=>$sort->link('titleAbbr')),
        array('title'=>$sort->link('contactName')),
        array('title'=>$sort->link('createTime')),
        array('title'=>$sort->link('deactivationTime')),
        array('title'=>Yii::t('t','Actions')),
    ),
    'sortname'=>'deactivationTime',
    'sortorder'=>'asc',
    'url'=>Yii::app()->createUrl($this->id.'/gridData',$_GET),
)); ?>
