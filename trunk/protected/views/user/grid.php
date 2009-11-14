<?php MParams::setPageLabel(Yii::t('page','Grid of members')); ?>
<?php MListOfLinks::set('sidebar',array(
    'links'=>array(
        /*array(
            'text'=>Yii::t('link','View as list'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal'
        ),*/
        User::isAdministrator() ?
        array(
            'text'=>Yii::t('link','Add a member'),
            'url'=>array('create'),
            'icon'=>'plus'
        ) : null,
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'url'=>array($this->action->id),
            'active'=>true
        )
    ),
)); ?>
<?php $this->var->rows=array(); /*rows is an important parameter for the static grid*/ ?>
<?php foreach($models as $model): ?>
<?php $this->var->rows=array_merge($this->var->rows,array(array(
    array(
        'content'=>CHtml::encode($model->screenName),
    ),
    array(
        'content'=>CHtml::encode($model->details->occupation),
    ),
    array(
        'content'=>CHtml::encode($model->email),
    ),
    array(
        'align'=>'right',
        'content'=>CHtml::encode(MDate::format($model->createTime,'medium',null)),
        'title'=>CHtml::encode(MDate::format($model->createTime,'full')),
    ),
    array(
        'align'=>'right',
        'content'=>CHtml::encode(MDate::format($model->details->deactivationTime,'medium',null)),
        'title'=>CHtml::encode(MDate::format($model->details->deactivationTime,'full')),
    ),
    array(
        'content'=>CHtml::encode($model->getAttributeView('accessType')),
    ),
    array(
        'content'=>
            CHtml::link('<span class="ui-icon ui-icon-zoomin"></span>',array('show','id'=>$model->id),array(
                'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-first ui-corner-all',
                'title'=>Yii::t('link','Show')
            )).
            CHtml::link('<span class="ui-icon ui-icon-pencil"></span>',array('update','id'=>$model->id),array(
                'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-last ui-corner-all',
                'title'=>Yii::t('link','Edit')
            )),
    ),
))); ?>
<?php endforeach; ?>
<?php $this->widget('application.components.WItemsGrid',array(
    'columns'=>array(
        array('title'=>CHtml::encode(User::model()->getAttributeLabel('screenName'))),
        array('title'=>CHtml::encode(UserDetails::model()->getAttributeLabel('occupation'))),
        array('title'=>CHtml::encode(User::model()->getAttributeLabel('email'))),
        array('title'=>CHtml::encode(User::model()->getAttributeLabel('Registered'))),
        array('title'=>CHtml::encode(UserDetails::model()->getAttributeLabel('Deact'))),
        array('title'=>CHtml::encode(User::model()->getAttributeLabel('accessType'))),
        array('title'=>CHtml::encode(Yii::t('t','Actions'))),
    ),
    'columnsModel'=>array(
        array('name'=>'screenName','width'=>150),
        array('name'=>'occupation','width'=>120),
        array('name'=>'email','width'=>150),
        array('name'=>'createTime','width'=>70,'align'=>'right'),
        array('name'=>'deactivationTime','width'=>70,'align'=>'right'),
        array('name'=>'accessType','width'=>79),
        array('name'=>'actions','width'=>59,'sortable'=>false),
    ),
    'pages'=>$pages,
    'rowNum'=>UserController::GRID_PAGE_SIZE,
    'rows'=>$this->var->rows,
    'sColumns'=>array(
        array('title'=>$sort->link(User::model()->tableName().'.screenName')),
        array('title'=>$sort->link('UserUserDetails.occupation',$sort->resolveLabel('details.occupation'))),
        array('title'=>$sort->link(User::model()->tableName().'.email')),
        array('title'=>$sort->link(User::model()->tableName().'.createTime',$sort->resolveLabel('Registered'))),
        array('title'=>$sort->link('UserUserDetails.deactivationTime',$sort->resolveLabel('details.Deact'))),
        array('title'=>$sort->link(User::model()->tableName().'.accessLevel',$sort->resolveLabel('accessType'))),
        array('title'=>Yii::t('t','Actions')),
    ),
    'sortname'=>'screenName',
    'sortorder'=>'asc',
)); ?>