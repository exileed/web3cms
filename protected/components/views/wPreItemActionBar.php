<div class="w3-pre-item-action-bar ui-widget">
  <ul>
<?php $n=0; ?>
<?php foreach($links as $link): ?>
<?php $liClass=''; ?>
<?php if($n===0): ?>
<?php $liClass.='w3-first '; ?>
<?php endif; ?>
<?php if($n+1===$c): ?>
<?php $liClass.='w3-last '; ?>
<?php endif; ?>
<?php if(isset($link['icon'])): ?>
<?php $link['text']='<span class="w3-inner-icon-left ui-icon ui-icon-'.$link['icon'].'"></span>'.$link['text']; ?>
<?php $link['options']['class']=(isset($link['options']['class'])?$link['options']['class'].' ':'').'w3-with-icon'; ?>
<?php endif; ?>
    <li class="<?php echo $liClass; ?>ui-state-default ui-corner-all"><?php echo CHtml::link($link['text'],$link['url'],$link['options']); ?></li>
<?php $n++; ?>
<?php endforeach; ?>
  </ul>
</div>
<div class="clear">&nbsp;</div>

