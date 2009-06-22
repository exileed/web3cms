<div class="w3-user-flash-content-summary-wrapper">
<?php foreach($success as $userFlash): ?>
  <div class="w3-content-item<?php echo MLayout::countContentItem()?'':' first'; ?>">
    <div class="ui-widget ui-state-highlight ui-corner-all w3-user-flash-content-summary">
        <span class="ui-icon ui-icon-check" style="float: left; margin-right: .3em;"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-content-item -->
<?php MLayout::incrementContentItem(); ?>
<?php endforeach; ?>
<?php foreach($info as $userFlash): ?>
  <div class="w3-content-item<?php echo MLayout::countContentItem()?'':' first'; ?>">
    <div class="ui-widget ui-state-highlight ui-corner-all w3-user-flash-content-summary">
        <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-content-item -->
<?php MLayout::incrementContentItem(); ?>
<?php endforeach; ?>
<?php foreach($error as $userFlash): ?>
  <div class="w3-content-item<?php echo MLayout::countContentItem()?'':' first'; ?>">
    <div class="ui-widget ui-state-error ui-corner-all w3-user-flash-content-summary">
        <span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
        <?php echo $userFlash; ?> 
    </div>
  </div><!-- w3-content-item -->
<?php MLayout::incrementContentItem(); ?>
<?php endforeach; ?>
</div><!-- w3-user-flash-content-summary-wrapper -->
