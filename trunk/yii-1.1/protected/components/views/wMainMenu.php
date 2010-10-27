<?php /* inspired from http://jqueryui.com/demos/tabs/  'Theming' */ ?>
<?php if(MParams::getMainMenuFullWidth()): ?>
<div class="w3-main-menu-box ui-widget ui-widget-header ui-corner-all">
<div class="<?php echo MLayout::getContainerCssClass(); ?>">
<div class="<?php echo MLayout::getGridCssClass(); ?>">
<?php echo $this->render('wMainMenuContent',array('items'=>$items)); ?>
</div>
<div class="clear">&nbsp;</div>
</div>
</div><!-- w3-main-menu-box -->
<?php else: ?>
<div class="<?php echo MLayout::getContainerCssClass(); ?>">
<div class="<?php echo MLayout::getGridCssClass(); ?>">
<div class="w3-main-menu-box ui-widget ui-widget-header">
<?php echo $this->render('wMainMenuContent',array('items'=>$items)); ?>
<div class="clear">&nbsp;</div>
</div><!-- w3-main-menu-box -->
</div>
</div>
<div class="clear">&nbsp;</div>
<?php endif; ?>
