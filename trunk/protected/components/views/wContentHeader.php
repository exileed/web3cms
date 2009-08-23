<?php if($displayBreadcrumbs): ?>
<ul class="w3-breadcrumbs">
<?php foreach($breadcrumbs as $n=>$item): ?>
<?php if(!$n && $item['active']): ?>
    <li class="w3-first active"><?php echo CHtml::link($item['label'],$item['url']); ?></li>
<?php elseif(!$n): ?>
    <li class="w3-first"><?php echo CHtml::link($item['label'].'<span>&rsaquo;&rsaquo;</span>',$item['url']); ?></li>
<?php elseif($item['active']): ?>
    <li class="active"><?php echo CHtml::link($item['label'],$item['url']); ?></li>
<?php else: ?>
    <li><?php echo CHtml::link($item['label'].'<span>&rsaquo;&rsaquo;</span>',$item['url']); ?></li>
<?php endif; ?>
<?php endforeach; ?>
</ul>

<?php endif; ?>
<?php if($displayLabel): ?>
<h1 class="w3-page-label"><?php echo $label; ?></h1>

<?php endif; ?>
<?php if($afterLabel): ?>
<div class="w3-after-page-label">&nbsp;</div>

<?php endif; ?>
