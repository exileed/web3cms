<?php if($displayBreadcrumbs): ?>
<ul class="w3-breadcrumbs">
<?php $n=0; ?>
<?php foreach($breadcrumbs as $link): ?>
<?php $class=''; ?>
<?php if($n===0): ?>
<?php $class='w3-first'; ?>
<?php endif; ?>
<?php if($n+1===$c): ?>
<?php $class=($class===''?'':$class.' ').'w3-last'; ?>
<?php else: ?>
<?php $link['text'].='<span>&rsaquo;&rsaquo;</span>'; ?>
<?php endif; ?>
<?php if($link['active']===true): ?>
<?php $class=($class===''?'':$class.' ').'w3-active'; ?>
<?php endif; ?>
    <li<?php echo $class===''?'':' class="'.$class.'"'; ?>><?php echo CHtml::link($link['text'],$link['url'],$link['options']); ?></li>
<?php $n++; ?>
<?php endforeach; ?>
</ul>

<?php endif; ?>
<?php if($displayLabel): ?>
<h1 class="w3-page-label"><?php echo $label; ?></h1>

<?php endif; ?>
<?php if($afterLabel): ?>
<div class="w3-after-page-label">&nbsp;</div>

<?php endif; ?>
