<?php
echo $this->renderPartial('_common');
$this->widget('application.components.WUserFlash');
if (!empty($models)):?>
<table class="w3-grid-box ui-widget-content ui-corner-all" width="100%">
    <tr class="w3-grid-columns-row w3-grid-titlebar ui-state-default">
        <td width="50%"><?php echo CHtml::encode($models[0]->getAttributeLabel('name'));?></td>
        <td><?php echo CHtml::encode($models[0]->getAttributeLabel('topicCount')); ?></td>
        <td><?php echo CHtml::encode($models[0]->getAttributeLabel('postCount')); ?></td>
        <td><?php echo CHtml::encode('Last post'); ?></td>
            <?php if (User::isAdministrator()) :?><td><?php echo CHtml::encode('Edit');?></td><?php endif;?>
    </tr>
        <?php foreach ($models as $model):?>
    <tr class="w3-grid-row">
        <td><?php echo CHtml::link('<strong>'.CHtml::encode($model->name).'</strong>', array('forum/section', 'sid'=>$model->id)).'<br />'.CHtml::encode(MStr::shorten($model->description,128)); ?></td>
        <td><?php echo CHtml::encode($model->topicCount); ?></td>
        <td><?php echo CHtml::encode($model->postCount); ?></td>
        <td><?php echo CHtml::encode(MDate::format(time())); ?></td>
                <?php if (User::isAdministrator()) :?><td><?php echo CHtml::link('<span class="ui-icon ui-icon-pencil"></span>',array('update','id'=>$model->id),array(
                        'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-first ui-corner-all',
                        'title'=>Yii::t('link','Edit')
                        ));?></td><?php endif;?>
    </tr>
        <?php endforeach;?>
</table>
<?php endif;?>