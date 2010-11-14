<?php MParams::setPageLabel(Yii::t('page','Grid of invoices')); ?>
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
            'text'=>Yii::t('link','Add an invoice'),
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
    ),
)); ?>
<?php $this->widget('application.components.WGrid',array(
    'columns'=>array(
        array('title'=>CHtml::encode($sort->resolveLabel('id'))),
        array('title'=>CHtml::encode($sort->resolveLabel('createTime'))),
        array('title'=>CHtml::encode($sort->resolveLabel('company'))),
        array('title'=>CHtml::encode($sort->resolveLabel('amountTotal'))),
        array('title'=>CHtml::encode(Yii::t('t','Actions'))),
    ),
    'columnsModel'=>array(
        array('name'=>'id','width'=>40,'align'=>'right'),
        array('name'=>'createTime','width'=>75,'align'=>'right'),
        array('name'=>'company','width'=>450),
        array('name'=>'amountTotal','width'=>74,'align'=>'right'),
        array('name'=>'actions','width'=>59,'sortable'=>false),
    ),
    'pages'=>$pages,
    'rowNum'=>InvoiceController::GRID_PAGE_SIZE,
    'rows'=>$gridRows,
    'sColumns'=>array(
        array('title'=>$sort->link('id')),
        array('title'=>$sort->link('createTime')),
        array('title'=>$sort->link('company')),
        array('title'=>$sort->link('amountTotal')),
        array('title'=>Yii::t('t','Actions')),
    ),
    'sortname'=>'createTime',
    'sortorder'=>'desc',
    'url'=>Yii::app()->createUrl($this->id.'/gridData',$_GET),
)); ?>
