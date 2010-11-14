<?php MParams::setPageLabel(Yii::t('page','Grid of projects')); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('hint','Required: {authRoles}.',
    array(4,'{authRoles}'=>implode(', ',array(Yii::t('t',User::CLIENT_T),Yii::t('t',User::CONSULTANT_T),Yii::t('t',User::MANAGER_T),Yii::t('t',User::ADMINISTRATOR_T))))
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
            'text'=>Yii::t('link','Add a project'),
            'url'=>array('create'),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/create'),
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
            'dropDown'=>array('links'=>$allCompany),
            'text'=>CHtml::encode($companyLinkText),
            'options'=>array('title'=>Yii::t('t','Company'))
        ),
        array(
            'dropDown'=>array('links'=>$allPriority),
            'text'=>CHtml::encode($priorityLinkText),
            'options'=>array('title'=>Yii::t('t','Priority'))
        ),
        array(
            'dropDown'=>array('links'=>$allState),
            'text'=>CHtml::encode($stateLinkText),
            'options'=>array('title'=>Yii::t('t','State[project]'))
        ),
    ),
)); ?>
<?php $this->widget('application.components.WGrid',array(
    'columns'=>array(
        array('title'=>CHtml::encode($sort->resolveLabel('company'))),
        array('title'=>CHtml::encode($sort->resolveLabel('title'))),
        array('title'=>CHtml::encode($sort->resolveLabel('hourlyRate'))),
        array('title'=>CHtml::encode($sort->resolveLabel('priority'))),
        array('title'=>CHtml::encode($sort->resolveLabel('openDate'))),
        array('title'=>CHtml::encode($sort->resolveLabel('closeDate'))),
        array('title'=>CHtml::encode(Yii::t('t','Actions'))),
    ),
    'columnsModel'=>array(
        array('name'=>'company','width'=>135),
        array('name'=>'title','width'=>260),
        array('name'=>'hourlyRate','width'=>45,'align'=>'right'),
        array('name'=>'priority','width'=>50),
        array('name'=>'openDate','width'=>75,'align'=>'right'),
        array('name'=>'closeDate','width'=>74,'align'=>'right'),
        array('name'=>'actions','width'=>59,'sortable'=>false),
    ),
    'pages'=>$pages,
    'rowNum'=>ProjectController::GRID_PAGE_SIZE,
    'rows'=>$gridRows,
    'sColumns'=>array(
        array('title'=>$sort->link('company')),
        array('title'=>$sort->link('title')),
        array('title'=>$sort->link('hourlyRate')),
        array('title'=>$sort->link('priority')),
        array('title'=>$sort->link('openDate')),
        array('title'=>$sort->link('closeDate')),
        array('title'=>Yii::t('t','Actions')),
    ),
    'sortname'=>'priority',
    'sortorder'=>'asc',
    'url'=>Yii::app()->createUrl($this->id.'/gridData',$_GET),
)); ?>