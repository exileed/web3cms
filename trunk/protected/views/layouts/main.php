<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $this->pageTitle; ?></title>
</head>

<body>
<div id="page">

<div id="header">
<div id="logo"><?php echo CHtml::encode(Yii::app()->params['title']); ?></div>
<div id="mainmenu">
<?php $this->widget('application.components.MainMenu',array(
	'items'=>array(
		array('label'=>'Home', 'url'=>array('/site/index')),
		array('label'=>'Contact', 'url'=>array('/site/contact')),
		array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
		array('label'=>'Logout', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
	),
)); ?>
</div><!-- mainmenu -->
</div><!-- header -->

<div id="content">
<?php echo $content; ?>
</div><!-- content -->

<div id="footer">
Copyright &copy; 2009 by <?php echo Yii::app()->params['copyrightBy']; ?>.<br/>
All Rights Reserved.<br/>
<?php echo Yii::powered(); ?>
</div><!-- footer -->

</div><!-- page -->
</body>

</html>