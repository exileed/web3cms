<div class="w3-user-flash-sidebar-summary-box">
<?php foreach($success as $userFlash): ?>
  <div class="w3-sidebar-item<?php echo MLayout::getNumberOfItemsSidebar1()?'':' w3-first'; ?>">
    <div class="w3-user-flash-sidebar-summary ui-widget ui-state-highlight ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-circle-check"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-sidebar-item -->
<?php MLayout::incrementNumberOfItemsSidebar1(); ?>
<?php endforeach; ?>
<?php foreach($info as $userFlash): ?>
  <div class="w3-sidebar-item<?php echo MLayout::getNumberOfItemsSidebar1()?'':' w3-first'; ?>">
    <div class="w3-user-flash-sidebar-summary ui-widget ui-state-highlight ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-info"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-sidebar-item -->
<?php MLayout::incrementNumberOfItemsSidebar1(); ?>
<?php endforeach; ?>
<?php foreach($error as $userFlash): ?>
  <div class="w3-sidebar-item<?php echo MLayout::getNumberOfItemsSidebar1()?'':' w3-first'; ?>">
    <div class="w3-user-flash-sidebar-summary ui-widget ui-state-error ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-alert"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-sidebar-item -->
<?php MLayout::incrementNumberOfItemsSidebar1(); ?>
<?php endforeach; ?>
</div><!-- w3-user-flash-sidebar-summary-box -->
