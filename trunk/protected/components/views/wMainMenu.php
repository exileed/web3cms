<?php /* inspired from http://jqueryui.com/demos/tabs/  'Theming' */ ?>
<div class="w3-main-menu-wrapper ui-widget ui-widget-header ui-corner-all">
<div class="<?php echo MLayout::getContainerCssClass(); ?>">
<div class="<?php echo MLayout::getGridCssClass(); ?>">
<div class="w3-main-menu">
<ul>
<?php foreach($items as $n=>$item): ?>
    <li class="ui-state-default ui-corner-all<?php echo ($item['active']?' ui-state-active':'') . ($n?'':' first'); ?>"><?php echo CHtml::link($item['label'],$item['url']); ?></li>
<?php endforeach; ?>
</ul>
</div><!-- w3-main-menu -->
</div>
<div class="clear">&nbsp;</div>
</div>
</div><!-- w3-main-menu-wrapper -->
