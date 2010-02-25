<div class="w3-user-flash-top-summary-box">
<?php foreach($success as $userFlash): ?>
  <div class="w3-top-item<?php echo MLayout::getNumberOfItemsTop()?'':' w3-first'; ?>">
    <div class="w3-user-flash-top-summary ui-widget ui-state-highlight ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-circle-check"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-top-item -->
<?php MLayout::incrementNumberOfItemsTop(); ?>
<?php endforeach; ?>
<?php foreach($info as $userFlash): ?>
  <div class="w3-top-item<?php echo MLayout::getNumberOfItemsTop()?'':' w3-first'; ?>">
    <div class="w3-user-flash-top-summary ui-widget ui-state-highlight ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-info"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-top-item -->
<?php MLayout::incrementNumberOfItemsTop(); ?>
<?php endforeach; ?>
<?php foreach($error as $userFlash): ?>
  <div class="w3-top-item<?php echo MLayout::getNumberOfItemsTop()?'':' w3-first'; ?>">
    <div class="w3-user-flash-top-summary ui-widget ui-state-error ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-alert"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-top-item -->
<?php MLayout::incrementNumberOfItemsTop(); ?>
<?php endforeach; ?>
</div><!-- w3-user-flash-top-summary-box -->
