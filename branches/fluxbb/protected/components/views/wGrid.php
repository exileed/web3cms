<?php if($displayGrid===true): ?>
<table id="<?php echo $gridId; ?>" class="w3-hidden"><tr><td></td></tr></table>
<div id="<?php echo $gridPagerId; ?>" class="w3-hidden"></div>
<?php endif; /*end if displayGrid*/ ?>
<?php if($displaySGrid===true): ?>
<div id="<?php echo $sGridWrapperId; ?>" class="w3-grid-box ui-widget ui-widget-content <?php echo $displaySGridPager===true?'ui-corner-all':'ui-corner-tl ui-corner-tr'; ?>">
<?php if($displaySTitlebar===true): ?>
  <div class="w3-grid-titlebar ui-widget-header ui-corner-tl ui-corner-tr ui-helper-clearfix">
    <div class="w3-grid-title"><?php echo $sTitle; ?></div>
<?php if($displayButtonClose===true): ?>
    <div class="w3-grid-titlebar-buttons">
      <a href="javascript:void(0)" class="w3-grid-titlebar-close"><span class="ui-icon ui-icon-circle-triangle-n"></span></a>
    </div>
<?php endif; /*end if displayButtonClose*/ ?>
  </div><!-- w3-grid-titlebar -->
<?php endif; /*end if displaySTitlebar*/ ?>
  <div id="<?php echo $sGridId; ?>">
    <table class="w3-grid" cellspacing="0" cellpadding="0" border="0" width="100%">
      <thead>
      <tr class="w3-grid-columns-row ui-state-default">
<?php if(count($sColumns)): ?>
<?php foreach($sColumns as $sColumn): ?>
        <th<?php echo ((isset($sColumn['nowrap']) && $sColumn['nowrap']===true) ? ' nowrap="nowrap"' : '') . ((isset($sColumn['width']) && $sColumn['width']!=='') ? ' width="'.$sColumn['width'].'"' : ''); ?>><?php echo $sColumn['title']; ?></th>
<?php endforeach; ?>
<?php else: ?>
        <th></th>
<?php endif; /*end if count sColumns*/ ?>
      </tr>
      </thead>
      <tbody>
<?php foreach($rows as $tr): ?>
      <tr class="w3-grid-row ui-widget-content">
<?php foreach($tr as $td): ?>
        <td<?php echo (isset($td['align'])?' align="'.$td['align'].'"':'').
            (isset($td['colspan'])?' colspan="'.$td['colspan'].'"':'').
            (isset($td['title'])?' title="'.$td['title'].'"':''); ?>><?php echo $td['content']; ?></td>
<?php endforeach; ?>
      </tr>
<?php endforeach; ?>
<?php foreach($importantRowsBottom as $tr): ?>
      <tr class="w3-grid-row ui-state-default">
<?php foreach($tr as $td): ?>
        <td<?php echo (isset($td['align'])?' align="'.$td['align'].'"':'').
            (isset($td['colspan'])?' colspan="'.$td['colspan'].'"':'').
            (isset($td['title'])?' title="'.$td['title'].'"':''); ?>><?php echo $td['content']; ?></td>
<?php endforeach; ?>
      </tr>
<?php endforeach; ?>
      </tbody>
    </table><!-- w3-grid -->
<?php if($displaySGridPager===true): ?>
    <div class="w3-grid-pager ui-state-default ui-corner-bl ui-corner-br">
      <div class="w3-right-column">
        <?php echo ($totalRecords>=1 ? Yii::t('hint','View {minRow} - {maxRow} of {totalRecords}',array(
            '{minRow}'=>$minRow,'{maxRow}'=>$maxRow,'{totalRecords}'=>$totalRecords)) :
            Yii::t('hint','No records to view')
        )."\n"; ?>
      </div>
    </div><!-- w3-grid-pager -->
<?php endif; /*end if displaySGridPager*/ ?>
  </div><!-- <?php echo $sGridId; ?> -->
</div><!-- w3-grid-box -->
<?php if($displayButtonClose===true): ?>
<?php MClientScript::registerScript('gridClose',array('selector'=>'#'.$sGridId)); ?>
<?php endif; ?>
<?php if($hasLinkIcon===true): ?>
<?php MClientScript::registerScript('linkIcon',array('box'=>'w3-ig')); ?>
<?php endif; ?>
<?php endif; /*end if displaySGrid*/ ?>
<?php if($displayGrid===true): ?>
<?php $colNames=''; ?>
<?php if(count($columns)): ?>
<?php $n=0; ?>
<?php foreach($columns as $row): ?>
<?php $colNames.=($n===0?'':',')."'".$row['title']."'"; ?>
<?php $n++; ?>
<?php endforeach; ?>
<?php else: ?>
<?php $colNames="''"; ?>
<?php endif; /*end if count columns*/ ?>
<?php $colModel=''; ?>
<?php if(count($columnsModel)): ?>
<?php $n=0; ?>
<?php foreach($columnsModel as $row): ?>
<?php $colModel.=($n===0?'':',')."
      {"; ?>
<?php $i=0; ?>
<?php foreach($row as $k=>$v): ?>
<?php $colModel.=($i===0?'':',').$k.':'.(is_string($v)?"'".$v."'":($v===true?'true':($v===false?'false':$v))); ?>
<?php $i++; ?>
<?php endforeach; ?>
<?php $colModel.="}"; ?>
<?php $n++; ?>
<?php endforeach; /*end foreach row*/ ?>
<?php else: ?>
<?php $colModel="
      {name:'colModel',width:99}"; ?>
<?php endif; /*end if count columnsModel*/ ?>
<?php Yii::app()->getClientScript()->registerScript('grid['.$gridId.']',"
jQuery('#".$gridId."').removeClass('w3-hidden');
jQuery('#".$gridPagerId."').removeClass('w3-hidden');
jQuery('#".$gridId."').jqGrid({
    url:'".$url."',
    datatype: '".$datatype."',
    mtype: '".$mtype."',
    colNames: [".$colNames."],
    colModel: [".$colModel."],
    pager: '#".$gridPagerId."',
    rowNum: ".$rowNum.",
    rowList: ".$rowList.",
    sortname: '".$sortname."',
    sortorder: '".$sortorder."',
    viewrecords: ".($viewrecords===false?'false':'true').",".($displayTitlebar ? "
    caption: '".$title."'," : '')."
    height: ".$height.",
    gridComplete: function(){".($displaySGrid===true ? "
        if(jQuery('#".$sGridWrapperId."').css('display') != 'none')
            jQuery('#".$sGridWrapperId."').hide();" : '')."
    },
    loadComplete: function(){".(($displaySGrid===true && $hasLinkIcon===true) ? '
        '.MClientScript::registerScript('linkIcon',array('box'=>'w3-ig'),true) . ($registerGridLinkIcon?'
        '.MClientScript::registerScript('gridLinkIcon',array('controllerId'=>$controllerId,'gridId'=>$gridId),true):'') : '')."
    }
});
jQuery('#".$gridId."').jqGrid('navGrid','#".$gridPagerId."',{edit:false,add:false,del:false});");
/* gridComplete is called after most of the grid changes
 * loadComplete is called only after data is loaded*/ ?>
<?php Yii::app()->getClientScript()->registerCssFile(Yii::app()->request->baseUrl.'/static/css/jqgrid/ui.jqgrid.css'); ?>
<?php Yii::app()->getClientScript()->registerScriptFile(Yii::app()->request->baseUrl.'/static/js/jqgrid/i18n/grid.locale-'.(MPath::jqGridLocaleExists(Yii::app()->language)?Yii::app()->language:'en').'.js',CClientScript::POS_BEGIN); ?>
<?php Yii::app()->getClientScript()->registerScriptFile(Yii::app()->request->baseUrl.'/static/js/jqgrid/jquery.jqGrid.min.js',CClientScript::POS_BEGIN); ?>
<?php endif; /*end if displayGrid*/ ?>
