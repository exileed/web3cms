<?php MParams::setPageLabel(Yii::t('page','Time report')); ?>
<?php MUserFlash::setSidebarInfo(Yii::t('hint','Required: {authRoles}.',
    array(4,'{authRoles}'=>implode(', ',array(Yii::t('t',User::CLIENT_T),Yii::t('t',User::CONSULTANT_T),Yii::t('t',User::MANAGER_T),Yii::t('t',User::ADMINISTRATOR_T))))
)); ?>
<?php MLinkList::set('sidebar',array(
    'links'=>array(
        array(
            'text'=>Yii::t('link','View as grid'),
            'url'=>array('grid'),
            'icon'=>'calculator',
            'visible'=>Yii::app()->user->checkAccess($this->id.'/grid'),
        ),
        array(
            'text'=>Yii::t('link','View as list'),
            'url'=>array('list'),
            'icon'=>'grip-solid-horizontal',
            'visible'=>false,
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
    //'afterLabel'=>false,
    'breadcrumbs'=>array(
        array(
            'text'=>Yii::t('link','Time'),
            'url'=>array($this->id.'/'.$this->defaultAction),
            'active'=>false,
            'visible'=>Yii::app()->user->checkAccess($this->id.'/'.$this->defaultAction),
        ),
        array(
            'url'=>array($this->action->id),
            'active'=>true,
        ),
    ),
)); ?>

<?php $cn=0; ?>
<?php foreach($data as $company): ?>
<?php if($cn>=1): ?>
<div class="w3-between-boxes-big">&nbsp;</div>
<hr/>
<?php endif; ?>
<?php if(isset($company['company'])): ?>
<h2 class="w3-regular">
  <span title="<?php echo Yii::t('t','Company'); ?>"><?php echo $company['company']->title!=='' ? CHtml::encode($company['company']->title) : $company['company']->id; ?></span>
  <span class="w3-position-absolute"><?php echo CHtml::link('',array('company/show','id'=>$company['company']->id),array('class'=>'ui-icon ui-icon-extlink','rel'=>'external','style'=>'margin-top: .3em;','title'=>Yii::t('link','Show company'))); ?></span>
</h2>
<?php else: ?>
<h2 class="w3-regular"><?php echo Yii::t('t','Unknown company'); ?></h2>
<?php endif; ?>
<?php $pn=0; ?>
<?php foreach($company['allProject'] as $project): ?>
<?php if($pn>=1): ?>
  <div class="w3-between-boxes-small">&nbsp;</div>
<?php endif; ?>
<?php if(isset($project['project'])): ?>
  <h3 class="w3-regular">
    <span title="<?php echo Yii::t('t','Project'); ?>"><?php echo $project['project']->title!=='' ? CHtml::encode($project['project']->title) : $project['project']->id; ?></span>
    <span class="w3-position-absolute"><?php echo CHtml::link('',array('project/show','id'=>$project['project']->id),array('class'=>'ui-icon ui-icon-extlink','rel'=>'external','style'=>'margin-top: .1em;','title'=>Yii::t('link','Show project'))); ?></span>
  </h3>
<?php else: ?>
  <h3 class="w3-regular"><?php echo Yii::t('t','Unknown project'); ?></h3>
<?php endif; ?>
<?php $tn=0; ?>
<?php foreach($project['allTask'] as $task): ?>
<?php if($tn>=1): ?>
  <div class="w3-between-boxes-small">&nbsp;</div>
<?php endif; ?>
<?php $this->widget('application.components.WGrid',array(
    'displayButtonClose'=>true,
    'importantRowsBottom'=>array(
        array(
            array(
                'align'=>'right',
                'colspan'=>3,
                'content'=>Yii::t('math','Total'),
            ),
            array(
                'align'=>'right',
                'content'=>Time::model()->getAttributeView('billedMinute',$task['total']['billedMinute']),
            ),
            array(
                'align'=>'right',
                'content'=>Time::model()->getAttributeView('spentMinute',$task['total']['spentMinute']),
            ),
        ),
        array(
            array(
                'align'=>'right',
                'colspan'=>3,
                'content'=>Yii::t('t','Amount'),
            ),
            array(
                'align'=>'right',
                'content'=>CHtml::encode(MCurrency::format($task['total']['billedAmount'])),
            ),
            array(
                'align'=>'right',
                'content'=>CHtml::encode(MCurrency::format($task['total']['spentAmount'])),
            ),
        ),
    ),
    'maxRow'=>count($task['gridRows']),
    'minRow'=>count($task['gridRows'])>=1 ? 1 : 0,
    'rows'=>$task['gridRows'],
    'sColumns'=>array(
        array('title'=>(Yii::app()->user->checkAccess(User::CLIENT) || Yii::app()->user->checkAccess(User::CONSULTANT))?Yii::t('t','Manager'):Yii::t('t','Consultant'),'width'=>100),
        array('title'=>Yii::t('t','Date'),'width'=>60),
        array('title'=>Yii::t('t','Note')),
        array('nowrap'=>true,'title'=>Yii::t('t','Billed'),'width'=>90),
        array('nowrap'=>true,'title'=>Yii::t('t','Spent'),'width'=>90),
    ),
    'sGridId'=>'timeGrid'.(isset($task['task']) ? $task['task']->id : microtime(true)*10000),
    'title'=>isset($task['task']) ? '<span title="'.Yii::t('t','Task').'">'.CHtml::encode($task['task']->title).'</span><span class="w3-position-absolute">'.CHtml::link('',array('task/show','id'=>$task['task']->id),array('class'=>'ui-icon ui-icon-extlink','rel'=>'external','title'=>Yii::t('link','Show task'))).'</span>' : Yii::t('t','Unknown task'),
    'totalRecords'=>count($task['gridRows']),
)); ?>
<?php $tn++; ?>
<?php endforeach; /*task*/ ?>
  <div class="w3-between-boxes-small">&nbsp;</div>
<?php $this->widget('application.components.WGrid',array(
    'displaySGrid'=>true,
    'displaySGridPager'=>false,
    'importantRowsBottom'=>array(
        array(
            array(
                'align'=>'right',
                'content'=>Yii::t('math','Total'),
            ),
            array(
                'align'=>'right',
                'content'=>Time::model()->getAttributeView('billedMinute',$project['total']['billedMinute']),
            ),
            array(
                'align'=>'right',
                'content'=>Time::model()->getAttributeView('spentMinute',$project['total']['spentMinute']),
            ),
        ),
        array(
            array(
                'align'=>'right',
                'content'=>Yii::t('t','Amount'),
            ),
            array(
                'align'=>'right',
                'content'=>CHtml::encode(MCurrency::format($project['total']['billedAmount'])),
            ),
            array(
                'align'=>'right',
                'content'=>CHtml::encode(MCurrency::format($project['total']['spentAmount'])),
            ),
        ),
    ),
    'sColumns'=>array(
        array('title'=>isset($project['project']) ? ($project['project']->title!=='' ? CHtml::encode($project['project']->title) : $project['project']->id) : Yii::t('t','Unknown project')),
        array('nowrap'=>true,'title'=>Yii::t('t','Billed'),'width'=>90),
        array('nowrap'=>true,'title'=>Yii::t('t','Spent'),'width'=>90),
    ),
    'sGridId'=>'projectTotal'.(isset($project['project']) ? $project['project']->id : microtime(true)*10000),
    'title'=>Yii::t('t','Project'),
)); ?>
<?php $pn++; ?>
<?php endforeach; /*project*/ ?>
  <div class="w3-between-boxes">&nbsp;</div>
<?php $this->widget('application.components.WGrid',array(
    'displaySGrid'=>true,
    'displaySGridPager'=>false,
    'importantRowsBottom'=>array(
        array(
            array(
                'align'=>'right',
                'content'=>Yii::t('math','Total'),
            ),
            array(
                'align'=>'right',
                'content'=>Time::model()->getAttributeView('billedMinute',$company['total']['billedMinute']),
            ),
            array(
                'align'=>'right',
                'content'=>Time::model()->getAttributeView('spentMinute',$company['total']['spentMinute']),
            ),
        ),
        array(
            array(
                'align'=>'right',
                'content'=>Yii::t('t','Amount'),
            ),
            array(
                'align'=>'right',
                'content'=>CHtml::encode(MCurrency::format($company['total']['billedAmount'])),
            ),
            array(
                'align'=>'right',
                'content'=>CHtml::encode(MCurrency::format($company['total']['spentAmount'])),
            ),
        ),
    ),
    'sColumns'=>array(
        array('title'=>isset($company['company']) ? ($company['company']->title!=='' ? CHtml::encode($company['company']->title) : $company['company']->id) : Yii::t('t','Unknown company')),
        array('nowrap'=>true,'title'=>Yii::t('t','Billed'),'width'=>90),
        array('nowrap'=>true,'title'=>Yii::t('t','Spent'),'width'=>90),
    ),
    'sGridId'=>'companyTotal'.(isset($company['company']) ? $company['company']->id : microtime(true)*10000),
    'title'=>Yii::t('t','Company'),
)); ?>
<?php $cn++; ?>
<?php endforeach; /*company*/ ?>
