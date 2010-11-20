<?php MParams::setPageLabel(Yii::t('page','Grid of expenses')); ?>
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
            'text'=>Yii::t('link','Add an expense'),
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
            'options'=>array('title'=>Yii::t('t','Company')),
        ),
        array(
            'dropDown'=>array('links'=>$allProject),
            'text'=>CHtml::encode($projectLinkText),
            'options'=>array('title'=>Yii::t('t','Project')),
        ),
        array(
            'dropDown'=>array('links'=>$allBillToCompany),
            'text'=>CHtml::encode($billToCompanyLinkText),
            'options'=>array('title'=>Yii::t('t','Bill to company')),
        ),
    ),
)); ?>
<?php $this->widget('application.components.WGrid',array(
    'columns'=>array(
        array('title'=>CHtml::encode($sort->resolveLabel('company'))),
        array('title'=>CHtml::encode($sort->resolveLabel('project'))),
        array('title'=>CHtml::encode($sort->resolveLabel('expenseDate'))),
        array('title'=>CHtml::encode($sort->resolveLabel('amount'))),
        array('title'=>CHtml::encode($sort->resolveLabel('billToCompany'))),
        array('title'=>CHtml::encode(Yii::t('t','Actions'))),
    ),
    'columnsModel'=>array(
        array('name'=>'company','width'=>225),
        array('name'=>'project','width'=>225),
        array('name'=>'expenseDate','width'=>75,'align'=>'right'),
        array('name'=>'amount','width'=>75,'align'=>'right'),
        array('name'=>'billToCompany','width'=>39,'align'=>'center'),
        array('name'=>'actions','width'=>59,'sortable'=>false),
    ),
    'pages'=>$pages,
    'rowNum'=>ExpenseController::GRID_PAGE_SIZE,
    'rows'=>$gridRows,
    'sColumns'=>array(
        array('title'=>$sort->link('company')),
        array('title'=>$sort->link('project')),
        array('title'=>$sort->link('expenseDate')),
        array('title'=>$sort->link('amount')),
        array('title'=>$sort->link('billToCompany')),
        array('title'=>Yii::t('t','Actions')),
    ),
    'sortname'=>'expenseDate',
    'sortorder'=>'desc',
    'url'=>Yii::app()->createUrl($this->id.'/gridData',$_GET),
)); ?>