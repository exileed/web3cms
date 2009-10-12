<?php if($displayBreadcrumbs): ?>
<ul class="w3-breadcrumbs">
<?php $n=0; ?>
<?php foreach($breadcrumbs as $item): ?>
<?php $liClass=''; ?>
<?php if($n===0): ?>
<?php $liClass='w3-first'; ?>
<?php endif; ?>
<?php if($n+1===$c): ?>
<?php $liClass=($liClass===''?'':$liClass.' ').'w3-last'; ?>
<?php endif; ?>
<?php if($item['active']): ?>
<?php $liClass=($liClass===''?'':$liClass.' ').'w3-active'; ?>
<?php else: ?>
<?php $item['label'].='<span>&rsaquo;&rsaquo;</span>'; ?>
<?php endif; ?>
    <li<?php echo $liClass===''?'':' class="'.$liClass.'"'; ?>><?php echo CHtml::link($item['label'],$item['url']); ?></li>
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
