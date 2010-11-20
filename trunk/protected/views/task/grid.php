<?php MParams::setPageLabel(Yii::t('page','Grid of tasks')); ?>
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
            'text'=>Yii::t('link','Add a task'),
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
            'dropDown'=>array('links'=>$allConsultant),
            'text'=>CHtml::encode($consultantLinkText),
            'options'=>array('title'=>Yii::t('t','Leader'))
        ),
        array(
            'dropDown'=>array('links'=>$allPriority),
            'text'=>CHtml::encode($priorityLinkText),
            'options'=>array('title'=>Yii::t('t','Priority'))
        ),
        array(
            'dropDown'=>array('links'=>$allState),
            'text'=>CHtml::encode($stateLinkText),
            'options'=>array('title'=>Yii::t('t','State[task]'))
        ),
    ),
)); ?>
<?php $this->widget('application.components.WGrid',array(
    'columns'=>array(
        array('title'=>CHtml::encode($sort->resolveLabel('company'))),
        array('title'=>CHtml::encode($sort->resolveLabel('project'))),
        array('title'=>CHtml::encode($sort->resolveLabel('priority'))),
        array('title'=>CHtml::encode($sort->resolveLabel('title'))),
        array('title'=>CHtml::encode($sort->resolveLabel('consultant'))),
        array('title'=>CHtml::encode($sort->resolveLabel('dueDate'))),
        array('title'=>CHtml::encode($sort->resolveLabel('status'))),
        array('title'=>CHtml::encode(Yii::t('t','Actions'))),
    ),
    'columnsModel'=>array(
        array('name'=>'company','width'=>105),
        array('name'=>'project','width'=>105),
        array('name'=>'priority','width'=>45),
        array('name'=>'title','width'=>160),
        array('name'=>'consultant','width'=>75),
        array('name'=>'dueDate','width'=>70,'align'=>'right'),
        array('name'=>'status','width'=>79),
        array('name'=>'actions','width'=>59,'sortable'=>false),
    ),
    'pages'=>$pages,
    'rowNum'=>TaskController::GRID_PAGE_SIZE,
    'rows'=>$gridRows,
    'sColumns'=>array(
        array('title'=>$sort->link('company')),
        array('title'=>$sort->link('project')),
        array('title'=>$sort->link('priority')),
        array('title'=>$sort->link('title')),
        array('title'=>$sort->link('consultant')),
        array('title'=>$sort->link('dueDate')),
        array('title'=>$sort->link('status')),
        array('title'=>Yii::t('t','Actions')),
    ),
    'sortname'=>'priority',
    'sortorder'=>'asc',
    'url'=>Yii::app()->createUrl($this->id.'/gridData',$_GET),
)); ?>
