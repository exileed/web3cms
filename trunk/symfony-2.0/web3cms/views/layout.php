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
<title><?php $view['slots']->output('title', 'My Web3CMS') ?></title>
</head>

<body class="w3-doctype-transitional w3-layout-two-column w3-layout-content-sidebar2 w3-controller-user w3-controller-user-show w3-interface-le-frog">
<div class="w3-document-container">

<div class="w3-header">
<div class="container_16">
<div class="grid_16">
<div class="w3-logo"><h1><a href="<?php echo $view['assets']->getUrl('homepage') ?>">My Web3CMS</a></h1></div>
</div>
</div><!-- container_16 -->
<div class="clear">&nbsp;</div>
<div class="w3-main-menu-box ui-widget ui-widget-header ui-corner-all">
<div class="container_16">
<div class="grid_16">
<div class="w3-main-menu">
  <ul>
    <li class="ui-state-default ui-corner-all w3-first"><a title="Contact us" href="/my-project/trunk/yii-1.1/index.php/site/contact">Contact</a></li>
    <li class="ui-state-default ui-corner-all ui-state-active"><a title="View my profile" href="/my-project/trunk/yii-1.1/index.php/user/show">My profile</a></li>
    <li class="ui-state-default ui-corner-all w3-last"><a title="Leave my member account" href="/my-project/trunk/yii-1.1/index.php/user/logout">Logout</a></li>
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
</div><!-- grid_16 -->
<div class="clear">&nbsp;</div>
</div><!-- container_16 -->
</div><!-- w3-top -->


<div class="container_16">
<div class="grid_16">

<div class="w3-center-container">

<div class="grid_12 alpha">
<div class="w3-content">

<div class="w3-content-item w3-first">

<?php $view['slots']->output('_content') ?>

<ul class="w3-breadcrumbs">
    <li class="w3-first"><a href="/my-project/trunk/yii-1.1/index.php">Home<span>&rsaquo;&rsaquo;</span></a></li>
    <li class="w3-last w3-active"><a href="/my-project/trunk/yii-1.1/index.php/user/show">View my profile</a></li>
</ul>

<h1 class="w3-page-label">View my profile</h1>

<div class="w3-after-page-label">&nbsp;</div>

<div class="w3-detail-box ui-widget-content ui-corner-all">

<div class="w3-detail-row w3-first">
  <div class="w3-detail-row-label">Username</div>
  <div class="w3-detail-row-value">demo</div>
  <div class="clear">&nbsp;</div>
</div>
<div class="w3-detail-row">
  <div class="w3-detail-row-label">ID</div>
  <div class="w3-detail-row-value">53</div>
  <div class="clear">&nbsp;</div>
</div>

</div>
</div><!-- w3-content-item -->

</div><!-- w3-content -->
</div><!-- grid_12 alpha -->


<div class="grid_4 omega">
<div class="w3-sidebar w3-sidebar2">
<div class="w3-sidebar-item w3-first">
  <div class="w3-link-list-box">
    <div class="ui-widget ui-widget-content ui-corner-all">
      <div class="w3-link-list w3-titlebar ui-widget-header ui-corner-all">
        <div class="w3-link-list w3-titlebar-button-box">

          <div class="w3-link-list w3-titlebar-button">
            <a class="w3-link-list w3-titlebar-close" href="javascript:void(0)">
              <span class="ui-icon ui-icon-circle-triangle-n"></span>
            </a>
          </div><!-- w3-titlebar-button -->
        </div><!-- w3-titlebar-button-box -->
        <div class="w3-link-list w3-title">Related links</div>
      </div><!-- w3-titlebar -->

      <div class="w3-link-list w3-effects-on">
        <ul>
            <li class="w3-first "><a title="Edit my profile" class="w3-with-icon-left" href="/my-project/trunk/yii-1.1/index.php/user/update"><span class="w3-inner-icon-left ui-icon ui-icon-pencil"></span>Edit my profile</a></li>
            <li class="w3-last "><a title="Change interface" class="w3-with-icon-left" href="/my-project/trunk/yii-1.1/index.php/user/updateInterface"><span class="w3-inner-icon-left ui-icon ui-icon-radio-on"></span>Change interface</a></li>
        </ul>
      </div><!-- w3-link-list -->
    </div><!-- ui-widget -->
  </div><!-- w3-link-list-box -->

</div>
</div><!-- w3-sidebar2 -->
</div><!-- grid_4 omega -->

<div class="clear">&nbsp;</div>

</div><!-- w3-center-container -->
</div>
</div>


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
&copy; 2009 My Company. All Rights Reserved.
Developed by <a rel="external" href="http://www.web3cms.com/">Web3CMS</a> based on <a rel="external" href="http://www.symfony-reloaded.org/">Symfony2 Framework</a>.
</div><!-- w3-footer -->

</div><!-- w3-footer-box -->
</div>
<div class="clear">&nbsp;</div>
</div>

</div><!-- w3-document-container -->

</body>

</html>