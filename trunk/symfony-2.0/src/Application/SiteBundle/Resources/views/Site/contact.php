<?php $view->extend('SiteBundle::layout') ?>
<?php $view['slots']->set('pageLabel', 'Contact us') ?>
<div class="container_16">
<div class="grid_16">

<div class="w3-center-container">

<div class="grid_12 alpha">
<div class="w3-content">

<div class="w3-content-item w3-first">

<ul class="w3-breadcrumbs">
    <li class="w3-first"><a href="<?php echo $view['router']->generate('homepage') ?>">Home<span>&rsaquo;&rsaquo;</span></a></li>
    <li class="w3-last w3-active"><a href="<?php echo $view['router']->generate('contact') ?>">Contact us</a></li>
</ul>

<h1 class="w3-page-label">Contact us</h1>

<div class="w3-after-page-label">&nbsp;</div>

<div class="w3-main-form-box ui-widget-content ui-corner-all">

<?php /* echo $form->renderFormTag($view['router']->generate('contact')) */ ?>
<form class="w3-main-form" action="<?php echo $view['router']->generate('contact') ?>" method="post">

<div class="w3-form-row w3-first">
  <div class="w3-form-row-label"><label for="<?php echo $form['name']->getId() ?>">Name</label></div>
  <div class="w3-form-row-input">
    <?php echo $form['name']->render(array('class'=>'w3-input-text ui-widget-content ui-corner-all'.($form['name']->hasErrors() ? ' ui-state-error' : ''), 'maxlength'=>128))."\n" ?>
    <div class="w3-form-row-errors"><?php echo $form['name']->renderErrors() ?></div>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><label for="<?php echo $form['email']->getId() ?>">Email</label></div>
  <div class="w3-form-row-input">
    <?php echo $form['email']->render(array('class'=>'w3-input-text ui-widget-content ui-corner-all'.($form['email']->hasErrors() ? ' ui-state-error' : ''), 'maxlength'=>255))."\n" ?>
    <div class="w3-form-row-errors"><?php echo $form['email']->renderErrors() ?></div>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><label for="<?php echo $form['subject']->getId() ?>">Subject</label></div>
  <div class="w3-form-row-input">
    <?php echo $form['subject']->render(array('class'=>'w3-input-text w3-input-w200percents ui-widget-content ui-corner-all'.($form['subject']->hasErrors() ? ' ui-state-error' : ''), 'maxlength'=>128))."\n" ?>
    <div class="w3-form-row-errors"><?php echo $form['subject']->renderErrors() ?></div>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row">
  <div class="w3-form-row-label"><label for="<?php echo $form['content']->getId() ?>">Content</label></div>
  <div class="w3-form-row-input">
    <?php echo $form['content']->render(array('class'=>'w3-input-text w3-input-w200percents ui-widget-content ui-corner-all'.($form['content']->hasErrors() ? ' ui-state-error' : ''), 'rows'=>6, 'cols'=>50))."\n" ?>
    <div class="w3-form-row-errors"><?php echo $form['content']->renderErrors() ?></div>
  </div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-form-row w3-last">
  <div class="w3-form-row-label">&nbsp;</div>
  <div class="w3-form-row-input">
    <input class="w3-input-button ui-state-default ui-corner-all" type="submit" value="Submit" />
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
        If you have business inquiries or other questions, please fill out this form to contact us. Thank you. 
    </div>
  </div><!-- w3-sidebar-item -->
</div><!-- w3-user-flash-sidebar-summary-box -->
</div><!-- w3-sidebar2 -->
</div><!-- grid_4 omega -->

<div class="clear">&nbsp;</div>

</div><!-- w3-center-container -->
</div>
</div>