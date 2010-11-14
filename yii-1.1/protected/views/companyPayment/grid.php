<?php MParams::setPageLabel(Yii::t('page','Grid of company payments')); ?>
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
            'text'=>Yii::t('link','Add a company payment'),
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
            'dropDown'=>array('links'=>$allPaymentMethod),
            'text'=>CHtml::encode($paymentMethodLinkText),
            'options'=>array('title'=>Yii::t('t','Payment method'))
        ),
    ),
)); ?>
<?php $this->widget('application.components.WGrid',array(
    'columns'=>array(
        array('title'=>CHtml::encode($sort->resolveLabel('company'))),
        array('title'=>CHtml::encode($sort->resolveLabel('paymentDate'))),
        array('title'=>CHtml::encode($sort->resolveLabel('amount'))),
        array('title'=>CHtml::encode($sort->resolveLabel('paymentMethod'))),
        array('title'=>CHtml::encode($sort->resolveLabel('paymentNumber'))),
        array('title'=>CHtml::encode(Yii::t('t','Actions'))),
    ),
    'columnsModel'=>array(
        array('name'=>'company','width'=>315),
        array('name'=>'paymentDate','width'=>75,'align'=>'right'),
        array('name'=>'amount','width'=>75,'align'=>'right'),
        array('name'=>'paymentMethod','width'=>100),
        array('name'=>'paymentNumber','width'=>74,'align'=>'right'),
        array('name'=>'actions','width'=>59,'sortable'=>false),
    ),
    'pages'=>$pages,
    'rowNum'=>CompanyPaymentController::GRID_PAGE_SIZE,
    'rows'=>$gridRows,
    'sColumns'=>array(
        array('title'=>$sort->link('company')),
        array('title'=>$sort->link('paymentDate')),
        array('title'=>$sort->link('amount')),
        array('title'=>$sort->link('paymentMethod')),
        array('title'=>$sort->link('paymentNumber')),
        array('title'=>Yii::t('t','Actions')),
    ),
    'sortname'=>'paymentDate',
    'sortorder'=>'desc',
    'url'=>Yii::app()->createUrl($this->id.'/gridData',$_GET),
)); ?>
