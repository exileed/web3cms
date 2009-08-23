<div class="w3-user-flash-sidebar1-summary-wrapper">
<?php foreach($success as $userFlash): ?>
  <div class="w3-sidebar1-item<?php echo MLayout::getNumberOfItemsSidebar1()?'':' w3-first'; ?>">
    <div class="w3-user-flash-sidebar1-summary ui-widget ui-state-highlight ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-check"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-sidebar1-item -->
<?php MLayout::incrementNumberOfItemsSidebar1(); ?>
<?php endforeach; ?>
<?php foreach($info as $userFlash): ?>
  <div class="w3-sidebar1-item<?php echo MLayout::getNumberOfItemsSidebar1()?'':' w3-first'; ?>">
    <div class="w3-user-flash-sidebar1-summary ui-widget ui-state-highlight ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-info"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-sidebar1-item -->
<?php MLayout::incrementNumberOfItemsSidebar1(); ?>
<?php endforeach; ?>
<?php foreach($error as $userFlash): ?>
  <div class="w3-sidebar1-item<?php echo MLayout::getNumberOfItemsSidebar1()?'':' w3-first'; ?>">
    <div class="w3-user-flash-sidebar1-summary ui-widget ui-state-error ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-alert"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-sidebar1-item -->
<?php MLayout::incrementNumberOfItemsSidebar1(); ?>
<?php endforeach; ?>
</div><!-- w3-user-flash-sidebar1-summary-wrapper -->
