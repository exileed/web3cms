<?php if($c>=1): ?>
<?php $options['id']=rand(100000000,999999999); ?>
<?php $text='<span class="w3-inner-icon-box"><span class="w3-inner-icon-right ui-icon ui-icon-triangle-1-s"></span></span>'.
'<span id="drop-down-link-title-'.$options['id'].'">'.$text.'</span>'; ?>
<?php $options['class']=(isset($options['class'])?$options['class'].' ':'').'w3-with-icon-right'; ?>
<?php $options['class']=(isset($options['class'])?$options['class'].' ':'').'w3-border-1px-transparent'; /*border is necessary for menu to work in IE*/ ?>
<?php echo CHtml::link($text,$url,$options)."\n"; ?>
      <div class="w3-hidden">
      <ul>
<?php foreach($links as $link): ?>
<?php $class=''; ?>
<?php if($link['active']===true): ?>
<?php $link['text']='<span class="ui-icon ui-icon-check" style="float: right;"></span>'.$link['text']; ?>
<?php $link['options']['class']=(isset($link['options']['class'])?$link['options']['class'].' ':'').'w3-active'; ?>
<?php endif; ?>
        <li><?php echo CHtml::link($link['text'],$link['url'],$link['options']); ?></li>
<?php endforeach; ?>
      </ul>
      </div>
<?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->request->baseUrl.'/css/menu/fg.menu.css'); ?>
<?php Yii::app()->getClientScript()->registerScriptFile(Yii::app()->request->baseUrl.'/js/menu/fg.menu.new.js'); ?>
<?php Yii::app()->getClientScript()->registerScript('dropDownLink',"jQuery('#".$options['id']."').menu({ 
    content: jQuery('#".$options['id']."').next().html(),
    showSpeed: 400,
    callerOnState: ''
});"); ?>
<?php else: ?>
<?php echo CHtml::link($text,$url,$options)."\n"; ?>
<?php endif; ?>
