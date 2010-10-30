<?php if(MParams::getHtmlDoctype()==='transitional'): ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php elseif(MParams::getHtmlDoctype()==='strict'): ?>
<?php echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n"; ?>
<?php endif; ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="<?php echo MParams::getLanguage(); ?>" /> 
<meta name="robots" content="all" />
<meta name="description" content="<?php echo MParams::getMetaDescription(); ?>" />
<meta name="keywords" content="<?php echo MParams::getMetaKeywordsAsString(); ?>" />
<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/static/images/favicon.ico" type="image/x-icon" />
<?php W3Init::jsCss(); ?>
<?php /*MLayout::setWrapInGridCssClass(false);
      if(!MLayout::getWrapInGridCssClass()):
      Yii::app()->getClientScript()->registerCss('enlargeSidebar',".container_16 .grid_4 {width: 240px;}");
      endif;*/ ?>
<title><?php echo $this->pageTitle; ?></title>
</head>

<body class="<?php echo MLayout::getBodytagCssClass(); ?>">
<div class="w3-document-container">

<div class="w3-header">
<div class="<?php echo MLayout::getContainerCssClass(); ?>">
<div class="<?php echo MLayout::getGridCssClass(); ?>">
<div class="w3-logo"><h1><?php echo CHtml::link(CHtml::encode(MParams::getHeaderTitle()),Yii::app()->homeUrl); ?></h1></div>
</div>
</div><!-- <?php echo MLayout::getContainerCssClass(); ?> -->
<div class="clear">&nbsp;</div>
<?php $this->widget('application.components.WMainMenu',array(
    'items'=>array(
        array('label'=>Yii::t('link','Home'), 'url'=>array('site/index'), 'visible'=>false, 'options'=>array('title'=>Yii::t('link','Main page'))),
        array('label'=>Yii::t('link','Contact'), 'url'=>array('site/contact'), 'visible'=>true, 'options'=>array('title'=>Yii::t('link','Contact us'))),
        array('label'=>Yii::t('link','Login'), 'url'=>Yii::app()->user->loginUrl, 'visible'=>Yii::app()->user->isGuest, 'options'=>array('title'=>Yii::t('link','Authenticate using my member account'))),
        array('label'=>Yii::t('link','Register'), 'url'=>array('user/register'), 'visible'=>Yii::app()->user->isGuest, 'options'=>array('title'=>Yii::t('link','Create a new member account'))),
        array('label'=>Yii::t('link','Members'), 'url'=>array('user/grid'), 'visible'=>Yii::app()->user->checkAccess('user/grid'), 'options'=>array('title'=>Yii::t('link','Browse members'))), // FIXME: replace 'grid' with $defaultAction
        array('label'=>Yii::t('link','My profile'), 'url'=>array('user/show'), 'visible'=>!Yii::app()->user->isGuest, 'options'=>array('title'=>Yii::t('link','View my profile'))),
        array('label'=>Yii::t('link','Wiki'), 'url'=>array('site/wiki'), 'visible'=>(Yii::app()->user->checkAccess(User::MANAGER) || Yii::app()->user->checkAccess(User::ADMINISTRATOR)), 'options'=>array('title'=>Yii::t('link','Application wiki'))),
        array('label'=>Yii::t('link','Logout'), 'url'=>array('user/logout'), 'visible'=>!Yii::app()->user->isGuest, 'options'=>array('title'=>Yii::t('link','Leave my member account'))),
    ),
)); ?>
</div><!-- w3-header -->


<div class="clear"><a name="top">&nbsp;</a></div>

<div class="w3-top">
<div class="<?php echo MLayout::getContainerCssClass(); ?>">
<div class="<?php echo MLayout::getGridCssClass(); ?>">
<?php $this->widget('application.components.WUserFlash',array('type'=>'topSummary')); ?>
</div><!-- <?php echo MLayout::getGridCssClass(); ?> -->
<div class="clear">&nbsp;</div>
</div><!-- <?php echo MLayout::getContainerCssClass(); ?> -->
</div><!-- w3-top -->


<div class="<?php echo MLayout::getContainerCssClass(); ?>">
<div class="<?php echo MLayout::getGridCssClass(); ?>">
<div class="w3-center-container">
<?php if(MLayout::hasSidebar1()): ?>

<div class="<?php echo MLayout::getGridCssClassSidebar1(); ?>">
<div class="w3-sidebar w3-sidebar1">
<?php $this->widget('application.components.WUserFlash',array('type'=>'sidebarSummary','in'=>'sidebar1')); ?>
<?php $this->widget('application.components.WUserFlash',array('type'=>'sidebar1Summary')); ?>
<?php $this->widget('application.components.WLinkList',array('id'=>'sidebar','area'=>'sidebar1')); ?>
<?php $this->widget('application.components.WLinkList',array('id'=>'sidebar1')); ?>
<?php if(!MLayout::getNumberOfItemsSidebar1()): ?>
&nbsp;
<?php endif; ?>
</div><!-- w3-sidebar1 -->
</div><!-- <?php echo MLayout::getGridCssClassSidebar1(); ?> -->

<?php endif; ?>

<?php if(MLayout::hasContent()): ?>
<div class="<?php echo MLayout::getGridCssClassContent(); ?>">
<div class="w3-content">

<?php $this->widget('application.components.WUserFlash',array('type'=>'contentSummary')); ?>
<div class="w3-content-item<?php echo MLayout::getNumberOfItemsContent()?'':' w3-first'; ?>">

<?php echo $content; ?>

</div><!-- w3-content-item -->

</div><!-- w3-content -->
</div><!-- <?php echo MLayout::getGridCssClassContent(); ?> -->
<?php endif; ?>

<?php if(MLayout::hasSidebar2()): ?>

<div class="<?php echo MLayout::getGridCssClassSidebar2(); ?>">
<div class="w3-sidebar w3-sidebar2">
<?php $this->widget('application.components.WUserFlash',array('type'=>'sidebarSummary','in'=>'sidebar2')); ?>
<?php $this->widget('application.components.WUserFlash',array('type'=>'sidebar2Summary')); ?>
<?php $this->widget('application.components.WLinkList',array('id'=>'sidebar','area'=>'sidebar2')); ?>
<?php $this->widget('application.components.WLinkList',array('id'=>'sidebar2')); ?>
<?php if(!MLayout::getNumberOfItemsSidebar2()): ?>
&nbsp;
<?php endif; ?>
</div><!-- w3-sidebar2 -->
</div><!-- <?php echo MLayout::getGridCssClassSidebar2(); ?> -->
<?php endif; ?>

<div class="clear">&nbsp;</div>

</div><!-- w3-center-container -->
</div>
</div>


<div class="clear"><a name="bottom">&nbsp;</a></div>

<div class="w3-bottom">
<div class="<?php echo MLayout::getContainerCssClass(); ?>">
<div class="<?php echo MLayout::getGridCssClass(); ?>">
<?php /* for your own purposes and for using with jquery */ ?>
</div><!-- <?php echo MLayout::getGridCssClass(); ?> -->
<div class="clear">&nbsp;</div>
</div><!-- <?php echo MLayout::getContainerCssClass(); ?> -->
</div><!-- w3-bottom -->


<div class="<?php echo MLayout::getContainerCssClass(); ?>">
<div class="<?php echo MLayout::getGridCssClass(); ?>">
<div class="w3-footer-box">
<div class="w3-footer">
<?php echo Yii::t('t','{copyrightBy}. All Rights Reserved.',array('{copyrightBy}'=>MParams::getCopyrightBy()))."\n"; ?>
<?php echo Yii::t('t','Developed by {developedBy} based on {basedOn}.',array(
    '{developedBy}'=>CHtml::link('Web3CMS','http://www.web3cms.com/',array('rel'=>'external')),
    '{basedOn}'=>CHtml::link('Yii Framework','http://www.yiiframework.com/',array('rel'=>'external'))))."\n"; ?>
</div><!-- w3-footer -->
</div><!-- w3-footer-box -->
</div>
<div class="clear">&nbsp;</div>
</div>

</div><!-- w3-document-container -->
</body>

</html>