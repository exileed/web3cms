<?php MParams::setPageLabel(Yii::t('page','View "{title}" company',array('{title}'=>$model->title))); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','Edit company'),
            'url'=>array('update','id'=>$model->id),
            'icon'=>'pencil',
            'visible'=>$model->isOwner() || Yii::app()->user->checkAccess($this->id.'/update'), // FIXME: create updateOwn operation
        ),
        array(
            'text'=>Yii::t('link','Add a project'),
            'url'=>array('project/create','companyId'=>$model->id),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess('project/create'),
        ),
        array(
            'text'=>Yii::t('link','Add a company payment'),
            'url'=>array('companyPayment/create','companyId'=>$model->id),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess('companyPayment/create'),
        ),
        array(
            'text'=>Yii::t('link','Add an invoice'),
            'url'=>array('invoice/create','companyId'=>$model->id),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess('invoice/create'),
        ),
        array(
            'text'=>Yii::t('link','List of companies'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal',
            'visible'=>false,
        ),
        array(
            'text'=>Yii::t('link','Grid of companies'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
        array(
            'text'=>Yii::t('link','Create a new company'),
            'url'=>array('create'),
            'icon'=>'plus',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/create'),
        ),
    ),
)); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Companies'),
            'url'=>array($this->id.'/'.$this->defaultAction),
            'active'=>false,
            'visible'=>Yii::app()->user->checkAccess($this->id.'/'.$this->defaultAction),
        ),
        array(
            'url'=>array($this->action->id,'id'=>$model->id),
            'active'=>true,
        ),
    ),
)); ?>
<div class="w3-detail-box ui-widget-content ui-corner-all">

<div class="w3-detail-row w3-first">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('title')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->title); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('titleAbbr')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->titleAbbr); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('contactName')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->contactName); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('contactEmail')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::link(CHtml::encode($model->contactEmail),'mailto:'.$model->contactEmail); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode(User2Company::model()->getAttributeLabel('userId')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->allUser[0]->id) ? CHtml::link(CHtml::encode($model->allUser[0]->screenName),array('user/show','id'=>$model->allUser[0]->id)) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode(Location::model()->getAttributeLabel('address1')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->allLocation[0]->id) ? CHtml::encode($model->allLocation[0]->address1) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode(Location::model()->getAttributeLabel('address2')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->allLocation[0]->id) ? CHtml::encode($model->allLocation[0]->address2) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode(Location::model()->getAttributeLabel('city')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->allLocation[0]->id) ? CHtml::encode($model->allLocation[0]->city) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode(Location::model()->getAttributeLabel('state')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->allLocation[0]->id) ? CHtml::encode($model->allLocation[0]->state) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode(Location::model()->getAttributeLabel('zipcode')); ?></div>
  <div class="w3-detail-row-value"><?php echo isset($model->allLocation[0]->id) ? CHtml::encode($model->allLocation[0]->zipcode) : ''; ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('invoiceDueDay')); ?></div>
  <div class="w3-detail-row-value"><?php echo $model->invoiceDueDay===null ? '' : CHtml::encode($model->invoiceDueDay).' '.Yii::t('t','days',array((int)$model->invoiceDueDay)); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('isActive')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->getAttributeView('isActive')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('deactivationTime')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode(MDate::format($model->deactivationTime,'full')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('createTime')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode(MDate::format($model->createTime,'full')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('updateTime')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode(MDate::format($model->updateTime,'full')); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('id')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->id); ?></div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label"><?php echo CHtml::encode($model->getAttributeLabel('content')); ?></div>
  <div class="w3-detail-row-value"><?php echo CHtml::encode($model->content); ?></div>
  <div class="clear">&nbsp;</div>
</div>

</div>

<?php if($model->isOwner() || Yii::app()->user->checkAccess(User::MANAGER) || Yii::app()->user->checkAccess(User::ADMINISTRATOR)): ?>
<?php MUserFlash::setSidebarInfo(Yii::t('hint','Company transaction history is open for owner, manager and administrator only.')); ?>
<div class="w3-between-boxes">&nbsp;</div>

<?php $this->widget('application.components.WGrid',array(
    'displayButtonClose'=>true,
    'importantRowsBottom'=>array(
        array(
            array(
                'align'=>'right',
                'colspan'=>2,
                'content'=>Yii::t('math','Total'),
            ),
            array(
                'align'=>'right',
                'content'=>CHtml::encode(MCurrency::format($debit)),
            ),
            array(
                'align'=>'right',
                'content'=>CHtml::encode(MCurrency::format($credit)),
            ),
            array(
                'align'=>'right',
                'content'=>CHtml::encode(MCurrency::format($balance)),
            ),
        ),
    ),
    'maxRow'=>count($gridRows),
    'minRow'=>count($gridRows)>=1 ? 1 : 0,
    'rows'=>$gridRows,
    'sColumns'=>array(
        array('title'=>Yii::t('t','Date')),
        array('title'=>Yii::t('t','Description')),
        array('title'=>Yii::t('payment','Debit')),
        array('title'=>Yii::t('payment','Credit[accounting]')),
        array('title'=>Yii::t('payment','Balance')),
    ),
    'sGridId'=>'w3TransactionGrid',
    'title'=>CHtml::encode(Yii::t('t','Transaction history')),
    'totalRecords'=>count($gridRows),
)); ?>
<?php endif; ?>