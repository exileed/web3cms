<div class="w3-user-flash-<?php echo $in; ?>-summary-wrapper">
<?php foreach($success as $userFlash): ?>
  <div class="w3-<?php echo $in; ?>-item<?php echo MLayout::countSidebarItem($in)?'':' first'; ?>">
    <div class="ui-widget ui-state-highlight ui-corner-all w3-user-flash-<?php echo $in; ?>-summary">
        <span class="ui-icon ui-icon-check" style="float: left; margin-right: .3em;"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-<?php echo $in; ?>-item -->
<?php MLayout::incrementSidebarItem($in); ?>
<?php endforeach; ?>
<?php foreach($info as $userFlash): ?>
  <div class="w3-<?php echo $in; ?>-item<?php echo MLayout::countSidebarItem($in)?'':' first'; ?>">
    <div class="ui-widget ui-state-highlight ui-corner-all w3-user-flash-<?php echo $in; ?>-summary">
        <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-<?php echo $in; ?>-item -->
<?php MLayout::incrementSidebarItem($in); ?>
<?php endforeach; ?>
<?php foreach($error as $userFlash): ?>
  <div class="w3-<?php echo $in; ?>-item<?php echo MLayout::countSidebarItem($in)?'':' first'; ?>">
    <div class="ui-widget ui-state-error ui-corner-all w3-user-flash-<?php echo $in; ?>-summary">
        <span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-<?php echo $in; ?>-item -->
<?php MLayout::incrementSidebarItem($in); ?>
<?php endforeach; ?>
</div><!-- w3-user-flash-<?php echo $in; ?>-summary-wrapper -->
