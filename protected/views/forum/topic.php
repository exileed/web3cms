<?php
$sectionName = (isset($section) ? $section->name : $models[0]->section->name);
$sectionId = (isset($section) ? $section->id : $models[0]->section->id);
$breadcrumbs = array(
                'text'=>Yii::t('link', $sectionName),
                'url'=>array('forum/section','sid'=>$sectionId),
                'active'=>true,
    );
echo $this->renderPartial('_common',array('breadcrumbs'=>$breadcrumbs));
echo $this->renderPartial('_actionbar',array('tid'=>$tid,'sid'=>$models[0]->sectionId));
?>
<?php if (!empty($models)):?>
<table class="w3-grid-box ui-widget-content ui-corner-all" width="100%">
    <tr class="w3-grid-columns-row w3-grid-titlebar ui-state-default">
            <td width="25%"><?php echo CHtml::encode($models[0]->getAttributeLabel('postedBy')); ?></td>
            <td><?php echo CHtml::encode($models[0]->getAttributeLabel('content')); ?></td>
    </tr>
        <?php
        foreach ($models as $model):?>
    <tr class="w3-grid-row">
        <td valign="top" colspan="1" rowspan="2">
            <strong><?php echo CHtml::link(CHtml::encode($model->user->username), array('user/show', 'id'=>$model->user->id)); ?></strong><br />
        </td>
        <td valign="top" align="right">
            <?php
            echo $model->getAttributeLabel('postTime');
            echo '&nbsp;'.MDate::format($model->postTime);
            ?>
        </td>
    </tr>
    <tr>
        <td><?php echo CHtml::encode($model->content); ?></td>
    </tr>
        <?php endforeach;?>
</table>
<br />
<?php echo $this->renderPartial('_actionbar',array('tid'=>$tid,'sid'=>$models[0]->sectionId));?>
<?php endif;?>