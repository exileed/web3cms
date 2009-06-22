<div class="w3-user-flash-sidebar2-summary-wrapper">
<?php foreach($success as $userFlash): ?>
  <div class="w3-sidebar2-item<?php echo MLayout::countSidebar2Item()?'':' first'; ?>">
    <div class="ui-widget ui-state-highlight ui-corner-all w3-user-flash-sidebar2-summary">
        <span class="ui-icon ui-icon-check" style="float: left; margin-right: .3em;"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-sidebar2-item -->
<?php MLayout::incrementSidebar2Item(); ?>
<?php endforeach; ?>
<?php foreach($info as $userFlash): ?>
  <div class="w3-sidebar2-item<?php echo MLayout::countSidebar2Item()?'':' first'; ?>">
    <div class="ui-widget ui-state-highlight ui-corner-all w3-user-flash-sidebar2-summary">
        <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-sidebar2-item -->
<?php MLayout::incrementSidebar2Item(); ?>
<?php endforeach; ?>
<?php foreach($error as $userFlash): ?>
  <div class="w3-sidebar2-item<?php echo MLayout::countSidebar2Item()?'':' first'; ?>">
    <div class="ui-widget ui-state-error ui-corner-all w3-user-flash-sidebar2-summary">
        <span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-sidebar2-item -->
<?php MLayout::incrementSidebar2Item(); ?>
<?php endforeach; ?>
</div><!-- w3-user-flash-sidebar2-summary-wrapper -->
