<?php if($displayBreadcrumbs): ?>
<ul class="w3-breadcrumbs">
<?php foreach($breadcrumbs as $n=>$item): ?>
<?php if(!$n && $item['active']): ?>
    <li class="first active"><?php echo CHtml::link($item['label'],$item['url']); ?></li>
<?php elseif(!$n): ?>
    <li class="first"><?php echo CHtml::link($item['label'].'<span>&rsaquo;&rsaquo;</span>',$item['url']); ?></li>
<?php elseif($item['active']): ?>
    <li class="active"><?php echo CHtml::link($item['label'],$item['url']); ?></li>
<?php else: ?>
    <li><?php echo CHtml::link($item['label'].'<span>&rsaquo;&rsaquo;</span>',$item['url']); ?></li>
<?php endif; ?>
<?php endforeach; ?>
</ul>

<?php endif; ?>
<?php if($displayLabel): ?>
<h1 class="w3-page-label"><?php echo $label===''?'&nbsp;':$label; ?></h1>

<?php endif; ?>
<?php if($afterLabel): ?>
<div class="w3-after-page-label">&nbsp;</div>

<?php endif; ?>
