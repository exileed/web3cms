<div class="w3-main-menu">
  <ul>
<?php foreach($items as $n=>$item): ?>
    <li class="ui-state-default ui-corner-all<?php echo ($item['active']?' ui-state-active':'') . ($n?'':' w3-first') . ($n===count($items)-1?' w3-last':''); ?>"><?php echo CHtml::link($item['label'],$item['url'],$item['options']); ?></li>
<?php endforeach; ?>
  </ul>
</div><!-- w3-main-menu -->
