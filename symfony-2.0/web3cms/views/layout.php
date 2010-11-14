<?php $view['stylesheets']->add('bundles/site/css/main.css') ?>
<?php $view['stylesheets']->add('bundles/site/css/960.css') ?>
<?php $view['stylesheets']->add('3rdparty/css/jqueryui/start/jquery-ui-1.8.6.custom.css') ?>
<?php if(file_exists(__DIR__.'/../../web/bundles/site/css/ui.css') && filesize(__DIR__.'/../../web/bundles/site/css/ui.css')!==0): ?>
<?php $view['stylesheets']->add('bundles/site/css/ui.css') ?>
<?php endif ?>
<?php $view['javascripts']->add('http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js') ?>
<?php $view['javascripts']->add('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js') ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="<?php $view['slots']->output('metaLanguage', 'en') ?>" /> 
<meta name="robots" content="<?php $view['slots']->output('metaRobots', 'all') ?>" />
<meta name="description" content="<?php $view['slots']->output('metaDescription', 'Web 2.0 Content Management System based on Symfony2 Framework. Web3CMS - Bringing Color to Powerful Symfony2 Application!') ?>" />
<meta name="keywords" content="<?php $view['slots']->output('metaKeywords', 'web3cms, cms, content management system, php, symfony2, jquery, ajax, web 2.0, jquery-ui, free, open source, mvc, framework') ?>" />
<link rel="shortcut icon" href="<?php echo $view['assets']->getUrl('bundles/site/images/favicon.ico') ?>" type="image/x-icon" />
<?php echo $view['stylesheets'] ?>
<?php echo $view['javascripts'] ?>
<script type="text/javascript">
/*<![CDATA[*/
// if CDN is not available, load js from the local server. in this case jQuery will be available in the next 'script' tag only and thereafter
if (typeof jQuery == 'undefined') {
    document.write('<script type="text/javascript" src="<?php echo $view['assets']->getUrl('3rdparty/js/jquery-1.4.3.min.js') ?>"><\/script>');
}
if (typeof jQuery.ui == 'undefined') {
    document.write('<script type="text/javascript" src="<?php echo $view['assets']->getUrl('3rdparty/js/jquery-ui-1.8.6.custom.min.js') ?>"><\/script>');
}
/*]]>*/
</script>
<title>
<?php if($view['slots']->has('pageLabel')): ?>
<?php echo strtr($view['slots']->get('pageTitleFormula', '{pageLabel} - {siteTitle}'),array(
    '{siteTitle}'=>$view['slots']->get('siteTitle', 'Web3CMS'),
    '{pageLabel}'=>$view['slots']->get('pageLabel')
)) ?>
<?php else: ?>
<?php $view['slots']->output('siteTitle', 'Web3CMS') ?>
<?php endif ?>
</title>
</head>

<body class="w3-doctype-transitional w3-layout-two-column w3-layout-content-sidebar2 w3-controller-user w3-controller-user-show w3-interface-le-frog">
<div class="w3-document-container">

<div class="w3-header">
<div class="container_16">
<div class="grid_16">
<div class="w3-logo"><h1><a href="<?php echo $view['router']->generate('homepage') ?>"><?php $view['slots']->output('headerTitle', 'My Web3CMS') ?></a></h1></div>
</div>
</div><!-- container_16 -->
<div class="clear">&nbsp;</div>
<div class="w3-main-menu-box ui-widget ui-widget-header ui-corner-all">
<div class="container_16">
<div class="grid_16">
<div class="w3-main-menu">
  <ul>
    <li class="ui-state-default ui-corner-all w3-first"><a title="Home page" href="<?php echo $view['router']->generate('homepage') ?>">Home</a></li>
    <li class="ui-state-default ui-corner-all ui-state-active"><a title="Contact us" href="<?php echo $view['router']->generate('contact') ?>">Contact</a></li>
    <li class="ui-state-default ui-corner-all w3-last"><a title="Tmp" href="./tmp">Tmp</a></li>
  </ul>
</div><!-- w3-main-menu -->
</div>
<div class="clear">&nbsp;</div>
</div>

</div><!-- w3-main-menu-box -->
</div><!-- w3-header -->


<div class="clear"><a name="top">&nbsp;</a></div>

<div class="w3-top">
<div class="container_16">
<div class="grid_16">
<?php if($view['session']->hasFlash('topSummary') && $view['session']->getFlash('topSummary') > ''): ?>
<div class="w3-user-flash-top-summary-box">
  <div class="w3-top-item w3-first">
    <div class="w3-user-flash-top-summary ui-widget ui-state-highlight ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-info"></span>
        <?php echo $view['session']->getFlash('topSummary') ?>
    </div>
  </div><!-- w3-top-item -->
</div><!-- w3-user-flash-top-summary-box -->
<?php elseif($view['session']->hasFlash('topError') && $view['session']->getFlash('topError') > ''): ?>
<div class="w3-user-flash-top-summary-box">
  <div class="w3-top-item w3-first">
    <div class="w3-user-flash-top-summary ui-widget ui-state-error ui-corner-all">
        <span class="w3-icon-left ui-icon ui-icon-alert"></span>
        <?php echo $view['session']->getFlash('topError') ?>
    </div>
  </div><!-- w3-top-item -->
</div><!-- w3-user-flash-top-summary-box -->
<?php endif; ?>
</div><!-- grid_16 -->
<div class="clear">&nbsp;</div>
</div><!-- container_16 -->
</div><!-- w3-top -->


<?php $view['slots']->output('_content') ?>


<div class="clear"><a name="bottom">&nbsp;</a></div>

<div class="w3-bottom">
<div class="container_16">
<div class="grid_16">
</div><!-- grid_16 -->

<div class="clear">&nbsp;</div>
</div><!-- container_16 -->
</div><!-- w3-bottom -->


<div class="container_16">
<div class="grid_16">
<div class="w3-footer-box">
<div class="w3-footer">
&copy; 2010 <?php $view['slots']->output('copyrightBy', 'My Company') ?>. All Rights Reserved.
Developed by <a rel="external" href="http://www.web3cms.com/">Web3CMS</a> based on <a rel="external" href="http://www.symfony-reloaded.org/">Symfony2 Framework</a>.
</div><!-- w3-footer -->

</div><!-- w3-footer-box -->
</div>
<div class="clear">&nbsp;</div>
</div>

</div><!-- w3-document-container -->
<script type="text/javascript">
/*<![CDATA[*/
jQuery(document).ready(function() {
jQuery("a[rel^='external']").attr({'target': '_blank'});
jQuery('.w3-header .w3-main-menu ul li a').hover( 
    function(){ jQuery(this).parent().removeClass('ui-state-default').addClass('ui-state-hover'); }, 
    function(){ jQuery(this).parent().removeClass('ui-state-hover').addClass('ui-state-default'); } 
);<?php $view['slots']->output('jQueryReady') ?>
});
/*]]>*/
</script>
</body>

</html>