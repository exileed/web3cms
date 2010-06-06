<?php
$sectionName = (isset($section) ? $section->name : $models[0]->section->name);
$sectionId = (isset($section) ? $section->id : $models[0]->section->id);
$breadcrumbs = array(
                'text'=>Yii::t('link', $sectionName),
                'active'=>true,
    );
echo $this->renderPartial('_common',array('breadcrumbs'=>$breadcrumbs));
echo $this->renderPartial('_actionbar',array('sid'=>$sectionId));
if (!empty($models)):?>
<table class="w3-grid-box ui-widget-content ui-corner-all" width="100%">
    <tr class="w3-grid-columns-row w3-grid-titlebar ui-state-default">
            <td width="50%"><?php echo CHtml::encode($models[0]->getAttributeLabel('title'));?></td>
            <td><?php echo CHtml::encode($models[0]->getAttributeLabel('postedBy')); ?></td>
            <td><?php echo CHtml::encode($models[0]->getAttributeLabel('replyCount')); ?></td>
            <td><?php echo CHtml::encode($models[0]->getAttributeLabel('viewCount')); ?></td>
            <td><?php echo CHtml::encode('Last message'); ?></td>
            <?php if (User::isAdministrator()) :?><td><?php echo CHtml::encode('Edit');?></td><?php endif;?>
    </tr>
        <?php
        foreach ($models as $model):?>
    <tr class="w3-grid-row">
        <td><strong><?php echo CHtml::link(CHtml::encode($model->post[0]->title), array('forum/topic', 'id'=>$model->id, ''=>MStr::seoFormat($model->post[0]->title))).'</strong><br/>'.CHtml::encode(MStr::shorten($model->post[0]->shortContent,128)); ?></td>
        <td><?php echo CHtml::link(CHtml::encode($model->user->username),array('user/show', 'id'=>$model->user->id)); ?></td>
        <td><?php echo CHtml::encode($model->replyCount); ?></td>
        <td><?php echo CHtml::encode($model->viewCount); ?></td>
        <td><?php echo CHtml::encode(MDate::format($model->post[0]->postTime)); ?></td>
        <?php if (User::isAdministrator()) :?><td><?php echo CHtml::link('<span class="ui-icon ui-icon-pencil"></span>',array('update','id'=>$model->id),array(
                            'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-first ui-corner-all',
                            'title'=>Yii::t('link','Edit')
                        ));?></td><?php endif;?>
    </tr>
        <?php endforeach;?>
</table>
<br />
<?php echo $this->renderPartial('_actionbar',array('sid'=>$sectionId));?>
<?php endif;?>