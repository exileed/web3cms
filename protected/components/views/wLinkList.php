<?php $class=''; ?>
<?php if($area==='sidebar1' || $area==='sidebar2'): ?>
<?php $class='w3-sidebar-item'.(MLayout::getNumberOfItemsSidebar($area)?'':' w3-first'); ?>
<?php endif; ?>
<div<?php echo $class===''?'':' class="'.$class.'"'; ?>>
  <div class="w3-link-list-box">
    <div class="ui-widget ui-widget-content ui-corner-all">
<?php if($displayTitlebar): ?>
      <div class="w3-link-list w3-titlebar ui-widget-header ui-corner-all">
        <div class="w3-link-list w3-titlebar-button-box">
          <div class="w3-link-list w3-titlebar-button">
            <a class="w3-link-list w3-titlebar-close" href="javascript:void(0)">
              <span class="ui-icon ui-icon-circle-triangle-n"></span>
            </a>
          </div><!-- w3-titlebar-button -->
        </div><!-- w3-titlebar-button-box -->
        <div class="w3-link-list w3-title"><?php echo $title; ?></div>
      </div><!-- w3-titlebar -->
<?php endif; ?>
      <div class="w3-link-list w3-effects-on">
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
<?php $link['text']='<span class="w3-inner-icon-left ui-icon ui-icon-'.(isset($link['icon'])?$link['icon']:'radio-on').'"></span>'.$link['text']; ?>
<?php $link['options']['class']=(isset($link['options']['class'])?$link['options']['class'].' ':'').'w3-with-icon-left'; ?>
            <li<?php echo $class==='' ? '' : ' class="'.$class.'"'; ?>><?php echo CHtml::link($link['text'],$link['url'],$link['options']); ?></li>
<?php $n++; ?>
<?php endforeach; ?>
        </ul>
      </div><!-- w3-link-list -->
    </div><!-- ui-widget -->
  </div><!-- w3-link-list-box -->
</div>
<?php if($displayTitlebar): ?>
<?php MClientScript::registerScript('linkListClose'); ?>
<?php endif; ?>
<?php MClientScript::registerScript('linkListHover'); ?>
