<?php MParams::setPageLabel(Yii::t('page','Grid of time records')); ?>
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
            'text'=>Yii::t('link','Time report'),
            'url'=>array('report'),
            //'icon'=>'grip-solid-horizontal',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/report'),
        ),
        array(
            'text'=>Yii::t('link','Add a time record'),
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
            'dropDown'=>array('links'=>$allProject),
            'text'=>CHtml::encode($projectLinkText),
            'options'=>array('title'=>Yii::t('t','Project'))
        ),
        array(
            'dropDown'=>array(
                'links'=>$allTask,
                'width'=>440,
            ),
            'text'=>CHtml::encode($taskLinkText),
            'options'=>array('title'=>Yii::t('t','Task'))
        ),
        array(
            'dropDown'=>array('links'=>$allManager),
            'text'=>CHtml::encode($managerLinkText),
            'options'=>array('title'=>Yii::t('t','Manager'))
        ),
        array(
            'dropDown'=>array('links'=>$allConsultant),
            'text'=>CHtml::encode($consultantLinkText),
            'options'=>array('title'=>Yii::t('t','Consultant'))
        ),
    ),
)); ?>
<?php $this->widget('application.components.WGrid',array(
    'columns'=>array(
        array('title'=>CHtml::encode($sort->resolveLabel('company'))),
        array('title'=>CHtml::encode($sort->resolveLabel('project'))),
        array('title'=>CHtml::encode($sort->resolveLabel('task'))),
        array('title'=>CHtml::encode($sort->resolveLabel('manager'))),
        array('title'=>CHtml::encode($sort->resolveLabel('consultant'))),
        array('title'=>CHtml::encode($sort->resolveLabel('timeDate'))),
        array('title'=>CHtml::encode($sort->resolveLabel('spentMinute'))),
        array('title'=>CHtml::encode($sort->resolveLabel('billedMinute'))),
        array('title'=>CHtml::encode($sort->resolveLabel('title'))),
        array('title'=>CHtml::encode(Yii::t('t','Actions'))),
    ),
    'columnsModel'=>array(
        array('name'=>'company','width'=>85),
        array('name'=>'project','width'=>85),
        array('name'=>'task','width'=>85),
        array('name'=>'manager','width'=>75),
        array('name'=>'consultant','width'=>75),
        array('name'=>'timeDate','width'=>70,'align'=>'right'),
        array('name'=>'spentMinute','width'=>35),
        array('name'=>'billedMinute','width'=>35),
        array('name'=>'title','width'=>94),
        array('name'=>'actions','width'=>59,'sortable'=>false),
    ),
    'pages'=>$pages,
    'rowNum'=>TimeController::GRID_PAGE_SIZE,
    'rows'=>$gridRows,
    'sColumns'=>array(
        array('title'=>$sort->link('company')),
        array('title'=>$sort->link('project')),
        array('title'=>$sort->link('task')),
        array('title'=>$sort->link('manager')),
        array('title'=>$sort->link('consultant')),
        array('title'=>$sort->link('timeDate')),
        array('title'=>$sort->link('spentMinute')),
        array('title'=>$sort->link('billedMinute')),
        array('title'=>$sort->link('title')),
        array('title'=>Yii::t('t','Actions')),
    ),
    'sortname'=>'timeDate',
    'sortorder'=>'desc',
    'url'=>Yii::app()->createUrl($this->id.'/gridData',$_GET),
)); ?>
