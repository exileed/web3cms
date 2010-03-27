<?php
// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
    exit;
define('PUN_HEADER', 1);
// Load the template
if (defined('PUN_ADMIN_CONSOLE')) {
    if (file_exists(SHELL_PATH . 'style/' . $_user['style'] . '/admin.tpl'))
        $tpl_file = SHELL_PATH . 'style/' . $_user['style'] . '/admin.tpl';
    else
        $tpl_file = SHELL_PATH . 'include/template/admin.tpl';
}else if (defined('PUN_HELP')) {
    if (file_exists(SHELL_PATH . 'style/' . $_user['style'] . '/help.tpl'))
        $tpl_file = SHELL_PATH . 'style/' . $_user['style'] . '/help.tpl';
    else
        $tpl_file = SHELL_PATH . 'include/template/help.tpl';
}else {
    if (file_exists(SHELL_PATH . 'style/' . $_user['style'] . '/main.tpl'))
        $tpl_file = SHELL_PATH . 'style/' . $_user['style'] . '/main.tpl';
    else
        $tpl_file = SHELL_PATH . 'include/template/main.tpl';
}
$tpl_main = file_get_contents($tpl_file);
// START SUBST - <_include "*">
while (preg_match('#<_include "([^/\\\\]*?)\.(php[45]?|inc|html?|txt)">#', $tpl_main, $cur_include)) {
    if (!file_exists(SHELL_PATH . 'include/user/' . $cur_include[1] . '.' . $cur_include[2]))
        error('Unable to process user include ' . htmlspecialchars($cur_include[0]) . ' from template main.tpl. There is no such file in folder /include/user/');
    ob_start();
    include SHELL_PATH . 'include/user/' . $cur_include[1] . '.' . $cur_include[2];
    $tpl_temp = ob_get_contents();
    $tpl_main = str_replace($cur_include[0], $tpl_temp, $tpl_main);
    ob_end_clean();
}
// END SUBST - <_include "*">
// START SUBST - <_language>
$tpl_main = str_replace('<_language>', $lang_common['lang_identifier'], $tpl_main);
// END SUBST - <_language>
// START SUBST - <_content_direction>
$tpl_main = str_replace('<_content_direction>', $lang_common['lang_direction'], $tpl_main);
// END SUBST - <_content_direction>
// START SUBST - <_head>
ob_start();
// Is this a page that we want search index spiders to index?
if (!defined('PUN_ALLOW_INDEX'))
    echo Yii::app()->getClientScript()->registerMetaTag('NOINDEX, FOLLOW', 'ROBOTS');
// $this->pageTitle = $page_title . (isset($p) ? ' (' . sprintf($lang_common['Page'], forum_number_format($p)) . ')' : '');
?>
<link rel="stylesheet" type="text/css" href="<?php echo WEB_PATH;?>style/<?php echo $_user['style'] . '.css' ?>" />
<?php if (defined('PUN_ADMIN_CONSOLE'))
    echo '<link rel="stylesheet" type="text/css" href="style/imports/base_admin.css" />' . "\n";
if (isset($required_fields)) {
    // Output JavaScript to validate form (make sure required fields are filled out)

    ?>
<script type="text/javascript">
function process_form(the_form)
{
	var element_names = new Object()
<?php
    // Output a JavaScript array with localised field names
    while (list($elem_orig, $elem_trans) = @each($required_fields))
    echo "\t" . 'element_names["' . $elem_orig . '"] = "' . addslashes(str_replace('&nbsp;', ' ', $elem_trans)) . '"' . "\n";

    ?>

	if (document.all || document.getElementById)
	{
		for (i = 0; i < the_form.length; ++i)
		{
			var elem = the_form.elements[i]
			if (elem.name && elem.name.substring(0, 4) == "req_")
			{
				if (elem.type && (elem.type=="text" || elem.type=="textarea" || elem.type=="password" || elem.type=="file") && elem.value=='')
				{
					alert("\"" + element_names[elem.name] + "\" <?php echo $lang_common['required field'] ?>")
					elem.focus()
					return false
				}
			}
		}
	}

	return true
}
</script>
<?php

}
// JavaScript tricks for IE6 and older
echo '<!--[if lte IE 6]><script type="text/javascript" src="style/imports/minmax.js"></script><![endif]-->';
$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<_head>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <_head>
if (isset($focus_element)) {
    echo Yii::app()->getClientScript()->registerScript('focus', 'document.getElementById(\'' . $focus_element[0] . '\').' . $focus_element[1] . '.focus()');
}
// START SUBST - <_page>
$tpl_main = str_replace('<_page>', htmlspecialchars(basename($_SERVER['PHP_SELF'], '.php')), $tpl_main);
// END SUBST - <_page>
// START SUBST - <_navlinks>
$tpl_main = str_replace('<_navlinks>', '<div id="brdmenu" class="inbox">' . "\n\t\t\t" . generate_navlinks() . "\n\t\t" . '</div>', $tpl_main);
// END SUBST - <_navlinks>
// START SUBST - <_status>
if ($_user['is_guest'])
    $tpl_temp = '<div id="brdwelcome" class="inbox">' . "\n\t\t\t" . '<p>' . $lang_common['Not logged in'] . '</p>' . "\n\t\t" . '</div>';
else {
    $tpl_temp = '<div id="brdwelcome" class="inbox">' . "\n\t\t\t" . '<ul class="conl">' . "\n\t\t\t\t" . '<li>' . $lang_common['Logged in as'] . ' <strong>' . _CHtml::encode($_user['username']) . '</strong></li>' . "\n\t\t\t\t" . '<li>' . $lang_common['Last visit'] . ': ' . MDate::format($_user['last_visit']) . '</li>';
    if ($_user['is_admmod']) {
        $db->setQuery('SELECT COUNT(id) FROM forum_reports WHERE zapped IS NULL') or error('Unable to fetch reports info', __FILE__, __LINE__, $db->error());
        if ($db->result())
            $tpl_temp .= "\n\t\t\t\t" . '<li class="reportlink"><strong>' . _CHtml::link('There are new reports', array('forum/admin_reports')) . '</strong></li>';
        if ($_config['o_maintenance'] == '1')
            $tpl_temp .= "\n\t\t\t\t" . '<li class="maintenancelink"><strong>' . _CHtml::link('Maintenance mode is enabled!', array('forum/admin_options#maintenance')) . '</strong></li>';
    }
    if (in_array(basename($_SERVER['PHP_SELF']), array('index.php', 'search.php')))
        $tpl_temp .= "\n\t\t\t" . '</ul>' . "\n\t\t\t" . '<ul class="conr">' . ($_user['g_search'] == '1' ? "\n\t\t\t\t" . '<li>' . _CHtml::link($lang_common['Show new posts'], array('forum/search', 'action' => 'show_new')) . '</li>' : '') . "\n\t\t\t\t" . '<li>' . _CHtml::link($lang_common['Mark all as read'], array('forum/misc', 'action' => 'markread')) . '</li>' . "\n\t\t\t" . '</ul>' . "\n\t\t\t" . '<div class="clearer"></div>' . "\n\t\t" . '</div>';
    else if (basename($_SERVER['PHP_SELF']) == 'viewforum.php')
        $tpl_temp .= "\n\t\t\t" . '</ul>' . "\n\t\t\t" . '<ul class="conr">' . "\n\t\t\t\t" . '<li>' . _CHtml::link($lang_common['Mark forum read'], array('forum/misc', 'action' => 'markforumread', 'fid' => $id)) . '</li>' . "\n\t\t\t" . '</ul>' . "\n\t\t\t" . '<div class="clearer"></div>' . "\n\t\t" . '</div>';
    else
        $tpl_temp .= "\n\t\t\t" . '</ul>' . "\n\t\t\t" . '<div class="clearer"></div>' . "\n\t\t" . '</div>';
}
$tpl_main = str_replace('<_status>', $tpl_temp, $tpl_main);
// END SUBST - <_status>
// START SUBST - <_announcement>
if ($_config['o_announcement'] == '1') {
    ob_start();
    ?>
<div id="announce" class="block">
	<h2><span><?php echo $lang_common['Announcement'] ?></span></h2>
	<div class="box">
		<div id="announce-block" class="inbox">
			<div class="usercontent"><?php echo $_config['o_announcement_message'] ?></div>
		</div>
	</div>
</div>
<?php $tpl_temp = trim(ob_get_contents());
    $tpl_main = str_replace('<_announcement>', $tpl_temp, $tpl_main);
    ob_end_clean();
}else
    $tpl_main = str_replace('<_announcement>', '', $tpl_main);
// END SUBST - <_announcement>
// START SUBST - <_main>
ob_start();