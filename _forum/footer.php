<?php
// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
    exit;
$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<_main>', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <_main>
// START SUBST - <_footer>
ob_start();
?>
<div id="brdfooter" class="block">
	<h2><span><?php echo $lang_common['Board footer'] ?></span></h2>
	<div class="box">
		<div class="inbox">
<?php
// If no footer style has been specified, we use the default (only copyright/debug info)
$footer_style = isset($footer_style) ? $footer_style : null;
if ($footer_style == 'index' || $footer_style == 'search') {
    if (!$_user['is_guest'] && $_user['g_search'] == '1') {
        echo "\t\t\t" . '<dl id="searchlinks" class="conl">' . "\n\t\t\t\t" . '<dt><strong>' . $lang_common['Search links'] . '</strong></dt>' . "\n\t\t\t\t" . '<dd>' . _CHtml::link($lang_common['Show recent posts'], array('forum/search', 'action' => 'show_24h')) . '</dd>' . "\n";
        echo "\t\t\t\t" . '<dd>' . _CHtml::link($lang_common['Show unanswered posts'], array('forum/search', 'action' => 'show_unanswered')) . '</dd>' . "\n";
        if ($_config['o_subscriptions'] == '1')
            echo "\t\t\t\t" . '<dd>' . _CHtml::link($lang_common['Show subscriptions'], array('forum/search', 'action' => 'show_subscriptions')) . '</dd>' . "\n";
        echo "\t\t\t\t" . '<dd>' . _CHtml::link($lang_common['Show your posts'], array('forum/search', 'action' => 'show_user', 'user_id' => $_user['id'])) . '</dd>' . "\n\t\t\t" . '</dl>' . "\n";
    }else {
        if ($_user['g_search'] == '1') {
            echo "\t\t\t" . '<dl id="searchlinks" class="conl">' . "\n\t\t\t\t" . '<dt><strong>' . $lang_common['Search links'] . '</strong></dt><dd>' . _CHtml::link($lang_common['Show recent posts'], array('forum/search', 'action' => 'show_24h')) . '</dd>' . "\n";
            echo "\t\t\t\t" . '<dd>' . _CHtml::link($lang_common['Show unanswered posts'], array('forum/search', 'action' => 'show_unanswered')) . '</dd>' . "\n\t\t\t" . '</dl>' . "\n";
        }
    }
}else if ($footer_style == 'viewforum' || $footer_style == 'viewtopic') {
    echo "\t\t\t" . '<div class="conl">' . "\n";
    // Display the "Jump to" drop list
    if ($_config['o_quickjump'] == '1') {
        // Load cached quick jump
        if (file_exists(FORUM_CACHE_DIR . 'cache_quickjump_' . $_user['g_id'] . '.php'))
            include FORUM_CACHE_DIR . 'cache_quickjump_' . $_user['g_id'] . '.php';
        if (!defined('PUN_QJ_LOADED')) {
            if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
                require SHELL_PATH . 'include/cache.php';
            generate_quickjump_cache($_user['g_id']);
            require FORUM_CACHE_DIR . 'cache_quickjump_' . $_user['g_id'] . '.php';
        }
    }
    if ($footer_style == 'viewforum' && $is_admmod)
        echo "\t\t\t" . '<p id="modcontrols">' . _CHtml::link($lang_common['Moderate forum'], array('forum/moderate', 'fid' => $forum_id, 'p' => $p)) . '</p>' . "\n";
    else if ($footer_style == 'viewtopic' && $is_admmod) {
        echo "\t\t\t" . '<dl id="modcontrols"><dt><strong>' . $lang_topic['Mod controls'] . '</strong></dt><dd>' . _CHtml::link($lang_common['Moderate topic'], array('forum/moderate', 'fid' => $forum_id, 'tid' => $id, 'p' => $p)) . '</dd>' . "\n";
        echo "\t\t\t" . '<dd>' . _CHtml::link($lang_common['Move topic'], array('forum/moderate', 'fid' => $forum_id, 'move_topics' => $id)) . '</dd>' . "\n";
        if ($cur_topic['closed'] == '1')
            echo "\t\t\t" . '<dd>' . _CHtml::link($lang_common['Open topic'], array('forum/moderate', 'fid' => $forum_id, 'open' => $id)) . '</dd>' . "\n";
        else
            echo "\t\t\t" . '<dd>' . _CHtml::link($lang_common['Close topic'], array('forum/moderate', 'fid' => $forum_id, 'close' => $id)) . '</dd>' . "\n";
        if ($cur_topic['sticky'] == '1')
            echo "\t\t\t" . '<dd>' . _CHtml::link($lang_common['Unstick topic'], array('forum/moderate', 'fid' => $forum_id, 'unstick' => $id)) . '</dd></dl>' . "\n";
        else
            echo "\t\t\t" . '<dd>' . _CHtml::link($lang_common['Stick topic'], array('forum/moderate', 'fid' => $forum_id, 'stick' => $id)) . '</dd></dl>' . "\n";
    }
    echo "\t\t\t" . '</div>' . "\n";
}
?>
			<ul class="conr">
				<li><?php printf($lang_common['Powered by'], _CHtml::link('Web3CMS', 'http://code.google.com/p/web3cms/') . (($_config['o_show_version'] == '1') ? ' ' . $_config['o_cur_version'] : '')) ?></li>
<?php
// Display debug info (if enabled/defined)
if (defined('PUN_DEBUG')) {
    // Calculate script generation time
    $time_diff = sprintf('%.3f', get_microtime() - $_start);
    echo "\t\t\t\t" . '<li>[ ' . sprintf($lang_common['Querytime'], $time_diff, $db->get_num_queries()) . ' ]</li>' . "\n";
}
?>
			</ul>
			<div class="clearer"></div>
		</div>
	</div>
</div>
<?php
// Display executed queries (if enabled)
if (defined('PUN_SHOW_QUERIES'))
    display_saved_queries();
$tpl_temp = trim(ob_get_contents());
$tpl_main = str_replace('<_footer>', $tpl_temp, $tpl_main);
ob_end_clean();
// Close the db connection (and free up any result data)
$db->close();
// Spit out the page
echo $tpl_main;
// END SUBST - <_footer>