<div class="w3-user-flash-<?php echo $in; ?>-summary-wrapper">
<?php foreach($success as $userFlash): ?>
  <div class="w3-<?php echo $in; ?>-item<?php echo MLayout::getNumberOfItemsSidebar($in)?'':' w3-first'; ?>">
    <div class="w3-user-flash-<?php echo $in; ?>-summary ui-widget ui-state-highlight ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-check"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-<?php echo $in; ?>-item -->
<?php MLayout::incrementNumberOfItemsSidebar($in); ?>
<?php endforeach; ?>
<?php foreach($info as $userFlash): ?>
  <div class="w3-<?php echo $in; ?>-item<?php echo MLayout::getNumberOfItemsSidebar($in)?'':' w3-first'; ?>">
    <div class="w3-user-flash-<?php echo $in; ?>-summary ui-widget ui-state-highlight ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-info"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-<?php echo $in; ?>-item -->
<?php MLayout::incrementNumberOfItemsSidebar($in); ?>
<?php endforeach; ?>
<?php foreach($error as $userFlash): ?>
  <div class="w3-<?php echo $in; ?>-item<?php echo MLayout::getNumberOfItemsSidebar($in)?'':' w3-first'; ?>">
    <div class="w3-user-flash-<?php echo $in; ?>-summary ui-widget ui-state-error ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-alert"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-<?php echo $in; ?>-item -->
<?php MLayout::incrementNumberOfItemsSidebar($in); ?>
<?php endforeach; ?>
</div><!-- w3-user-flash-<?php echo $in; ?>-summary-wrapper -->
