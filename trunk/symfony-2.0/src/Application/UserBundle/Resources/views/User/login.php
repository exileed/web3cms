<?php $view->extend('UserBundle::layout') ?>
<?php $view['slots']->set('pageLabel', 'Login') ?>
<div class="container_16">
<div class="grid_16">

<div class="w3-center-container">

<div class="grid_12 alpha">
<div class="w3-content">

<div class="w3-content-item w3-first">

<ul class="w3-breadcrumbs">
    <li class="w3-first"><a href="<?php echo $view['router']->generate('homepage') ?>">Home<span>&rsaquo;&rsaquo;</span></a></li>
    <li class="w3-last w3-active"><a href="<?php echo $view['router']->generate('login') ?>">Login</a></li>
</ul>

<h1 class="w3-page-label">Login</h1>

<div class="w3-after-page-label">&nbsp;</div>

<div class="w3-main-form-box ui-widget-content ui-corner-all">

<form class="w3-main-form" action="<?php echo $view['router']->generate('login') ?>" method="post">

<div class="w3-form-row w3-first">
  <div class="w3-form-row-label"><label for="<?php echo $form['username']->getId() ?>">Username</label></div>
  <div class="w3-form-row-input">
    <?php echo $form['username']->render(array('class'=>'w3-input-text ui-widget-content ui-corner-all'.($form['username']->hasErrors() ? ' ui-state-error' : ''), 'maxlength'=>128)) // 255 for email ?>
    <div class="w3-form-row-errors"><?php echo $form['username']->renderErrors() ?></div>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><label for="<?php echo $form['password']->getId() ?>">Password</label></div>
  <div class="w3-form-row-input">
    <?php echo $form['password']->render(array('class'=>'w3-input-text ui-widget-content ui-corner-all'.($form['password']->hasErrors() ? ' ui-state-error' : ''), 'maxlength'=>255)) ?>
    <div class="w3-form-row-errors"><?php echo $form['password']->renderErrors() ?></div>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><label for="<?php echo $form['rememberMe']->getId() ?>">&nbsp;</label></div>
  <div class="w3-form-row-input">
    <div class="w3-form-row-text">
      <?php echo $form['rememberMe']->render() ?> Remember my member account on this computer
    </div>
    <div class="w3-form-row-errors"><?php echo $form['rememberMe']->renderErrors() ?></div>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row w3-last">
  <div class="w3-form-row-label">&nbsp;</div>
  <div class="w3-form-row-input">
    <input class="w3-input-button ui-state-default ui-corner-all" type="submit" value="Log in" />
  </div>
  <div class="clear">&nbsp;</div>
</div>

</form>
</div><!-- w3-main-form-box -->
<?php $view['slots']->start('jQueryReady') ?>
jQuery(".w3-content form.w3-main-form .w3-input-text:first").focus();
jQuery(".w3-content form.w3-main-form .ui-state-error:first").focus();
jQuery(".w3-form-row .w3-input-button").hover(
    function(){ jQuery(this).addClass('ui-state-hover').removeClass('ui-state-default').removeClass('ui-state-active'); },
    function(){ jQuery(this).addClass('ui-state-default').removeClass('ui-state-hover').removeClass('ui-state-active'); }
)
.mousedown(function(){ jQuery(this).addClass('ui-state-active').removeClass('ui-state-default').removeClass('ui-state-hover'); })
.mouseup(function(){ jQuery(this).addClass('ui-state-default').removeClass('ui-state-active').removeClass('ui-state-hover'); });
<?php $view['slots']->stop() ?>


</div><!-- w3-content-item -->

</div><!-- w3-content -->
</div><!-- grid_12 alpha -->


<div class="grid_4 omega">

<div class="w3-sidebar w3-sidebar2">
<div class="w3-user-flash-sidebar-summary-box">
  <div class="w3-sidebar-item w3-first">
    <div class="w3-user-flash-sidebar-summary ui-widget ui-state-highlight ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-info"></span>
        Hint: You may login with <tt>demo/demo</tt> or <tt>admin/admin</tt>. 
    </div>
  </div><!-- w3-sidebar-item -->
</div><!-- w3-user-flash-sidebar-summary-box -->
</div><!-- w3-sidebar2 -->
</div><!-- grid_4 omega -->

<div class="clear">&nbsp;</div>

</div><!-- w3-center-container -->
</div>
</div>