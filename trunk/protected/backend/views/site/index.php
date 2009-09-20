<?php MLayout::hideSidebars(); ?>
<?php $this->widget('application.components.WContentHeader',array(
    'label'=>'Welcome, '.(Yii::app()->user->isGuest ? Yii::app()->user->name : Yii::app()->user->screenName).'!',
    'afterLabel'=>false,
    'displayBreadcrumbs'=>false,
)); ?>
<div class="w3-widget">

<p>
This is the homepage of <em><?php echo MParams::getHeaderTitle(); ?></em>.
</p>

</div>