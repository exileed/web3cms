<div class="w3-pre-item-action-bar ui-widget">
  <ul>
<?php $n=0; ?>
<?php foreach($links as $link): ?>
<?php $class=''; ?>
<?php if($n===0): ?>
<?php $class.='w3-first '; ?>
<?php endif; ?>
<?php if($n+1===$c): ?>
<?php $class.='w3-last '; ?>
<?php endif; ?>
<?php if(isset($link['dropDown'])): ?>
    <li class="<?php echo $class; ?>ui-state-default ui-corner-all"><?php $this->widget('application.components.WDropDownLink',array(
    'parameters'=>$link['dropDown'],
    'options'=>$link['options'],
    'text'=>$link['text'],
    'url'=>$link['url'],
)); ?>
    </li>
<?php else: ?>
<?php if(isset($link['icon'])): ?>
<?php $link['text']='<span class="w3-inner-icon-left ui-icon ui-icon-'.$link['icon'].'"></span>'.$link['text']; ?>
<?php $link['options']['class']=(isset($link['options']['class'])?$link['options']['class'].' ':'').'w3-with-icon-left'; ?>
<?php endif; ?>
    <li class="<?php echo $class; ?>ui-state-default ui-corner-all"><?php echo CHtml::link($link['text'],$link['url'],$link['options']); ?></li>
<?php endif; ?>
<?php $n++; ?>
<?php endforeach; ?>
  </ul>
</div>
<div class="clear">&nbsp;</div>
<?php MClientScript::registerScript('actionButton'); /*button hover effects*/ ?>

