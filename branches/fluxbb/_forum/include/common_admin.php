<?php

/*---

	Copyright (C) 2008-2009 FluxBB.org
	based on code copyright (C) 2002-2005 Rickard Andersson
	License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher

---*/
// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
    exit;
// Display the admin navigation menu
function generate_admin_menu($page = '')
{
    global $pun_config, $pun_user;

    $is_admin = $pun_user['g_id'] == PUN_ADMIN ? true : false;

    ?>
<div id="adminconsole" class="block2col">
	<div id="adminmenu" class="blockmenu">
		<h2><span><?php echo ($is_admin) ? 'Admin' : 'Moderator' ?> menu</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
					<li<?php if ($page == 'index') echo ' class="isactive"';?>><?php
    echo CHtml::link('Index', array('forum/admin_index'));

    ?></li>
<?php if ($is_admin): ?>
<li<?php if ($page == 'categories') echo ' class="isactive"'; ?>>
<?php echo CHtml::link('Categories', array('forum/admin_categories'));?></li>
<?php endif; ?><?php if ($is_admin): ?>
<li<?php if ($page == 'forums') echo ' class="isactive"'; ?>>
<?php echo CHtml::link('Forums', array('forum/admin_forums'));?></li>
<?php endif; ?>					<li<?php if ($page == 'users') echo ' class="isactive"'; ?>>
<?php echo CHtml::link('Users', array('forum/admin_users'));?></li>
<?php if ($is_admin): ?>					<li<?php if ($page == 'groups') echo ' class="isactive"'; ?>><?php echo CHtml::link('User groups', array('forum/admin_groups'));?></li>
<?php endif; ?><?php if ($is_admin): ?>					<li<?php if ($page == 'options') echo ' class="isactive"'; ?>><?php echo CHtml::link('Options', array('forum/admin_options'));?></li>
<?php endif; ?><?php if ($is_admin): ?>					<li<?php if ($page == 'permissions') echo ' class="isactive"'; ?>><?php echo CHtml::link('Permissions', array('forum/admin_permissions'));?></li>
<?php endif; ?>					<li<?php if ($page == 'censoring') echo ' class="isactive"'; ?>><?php echo CHtml::link('Censoring', array('forum/admin_censoring'));?></li>
<?php if ($is_admin): ?>					<li<?php if ($page == 'ranks') echo ' class="isactive"'; ?>><?php echo CHtml::link('Ranks', array('forum/admin_ranks'));?></li>
<?php endif; ?><?php if ($is_admin || $pun_user['g_mod_ban_users'] == '1'): ?><li<?php if ($page == 'bans') echo ' class="isactive"'; ?>>
<?php echo CHtml::link('Bans', array('forum/admin_bans'));?></li>
<?php endif; ?><?php if ($is_admin): ?>					<li<?php if ($page == 'prune') echo ' class="isactive"'; ?>><?php echo CHtml::link('Prune', array('forum/admin_prune'));?></li>
<?php endif; ?><?php if ($is_admin): ?>					<li<?php if ($page == 'maintenance') echo ' class="isactive"'; ?>><?php echo CHtml::link('Maintenance', array('forum/admin_maintenance'));?></li>
<?php endif; ?>					<li<?php if ($page == 'reports') echo ' class="isactive"'; ?>><?php echo CHtml::link('Reports', array('forum/admin_reports'));?></li>
				</ul>
			</div>
		</div>
<?php
        // See if there are any plugins
        $plugins = array();
        $d = dir(SHELL_PATH . 'plugins');
        while (($entry = $d->read()) !== false)
        {
            $prefix = substr($entry, 0, strpos($entry, '_'));
            $suffix = substr($entry, strlen($entry) - 4);

            if ($suffix == '.php' && ((!$is_admin && $prefix == 'AMP') || ($is_admin && ($prefix == 'AP' || $prefix == 'AMP'))))
                $plugins[] = array(substr($entry, strpos($entry, '_') + 1, - 4), $entry);
        }
        $d->close();
        // Did we find any plugins?
        if (!empty($plugins))
        {?>
		<h2 class="block2"><span>Plugins</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
<?php

            while (list(, $cur_plugin) = @each($plugins))
            echo "\t\t\t\t\t" . '<li' . (($page == $cur_plugin[1]) ? ' class="isactive"' : '') . '>' . CHtml::link(str_replace('_', ' ', $cur_plugin[0]), array('forum/admin_loader', 'plugin' => $cur_plugin[1])) . '</li>' . "\n";

            ?>
				</ul>
			</div>
		</div>
<?php

        }

        ?>
	</div>

<?php

    }
    // Delete topics from $forum_id that are "older than" $prune_date (if $prune_sticky is 1, sticky topics will also be deleted)
    function prune($forum_id, $prune_sticky, $prune_date)
    {
        global $db;

        $extra_sql = ($prune_date != - 1) ? ' AND last_post<' . $prune_date : '';

        if (!$prune_sticky)
            $extra_sql .= ' AND sticky=\'0\'';
        // Fetch topics to prune
        $db->setQuery('SELECT id FROM ' . $db->tablePrefix . 'topics WHERE forum_id=' . $forum_id . $extra_sql, true) or error('Unable to fetch topics', __FILE__, __LINE__, $db->error());

        $topic_ids = '';
        while ($row = $db->fetch_row())
        $topic_ids .= (($topic_ids != '') ? ',' : '') . $row[0];

        if ($topic_ids != '')
        {
            // Fetch posts to prune
            $db->setQuery('SELECT id FROM ' . $db->tablePrefix . 'posts WHERE topic_id IN(' . $topic_ids . ')', true) or error('Unable to fetch posts', __FILE__, __LINE__, $db->error());

            $post_ids = '';
            while ($row = $db->fetch_row())
            $post_ids .= (($post_ids != '') ? ',' : '') . $row[0];

            if ($post_ids != '')
            {
                // Delete topics
                $db->setQuery('DELETE FROM ' . $db->tablePrefix . 'topics WHERE id IN(' . $topic_ids . ')')->execute() or error('Unable to prune topics', __FILE__, __LINE__, $db->error());
                // Delete subscriptions
                $db->setQuery('DELETE FROM ' . $db->tablePrefix . 'subscriptions WHERE topic_id IN(' . $topic_ids . ')')->execute() or error('Unable to prune subscriptions', __FILE__, __LINE__, $db->error());
                // Delete posts
                $db->setQuery('DELETE FROM ' . $db->tablePrefix . 'posts WHERE id IN(' . $post_ids . ')')->execute() or error('Unable to prune posts', __FILE__, __LINE__, $db->error());
                // We removed a bunch of posts, so now we have to update the search index
                require_once SHELL_PATH . 'include/search_idx.php';
                strip_search_index($post_ids);
            }
        }
    }