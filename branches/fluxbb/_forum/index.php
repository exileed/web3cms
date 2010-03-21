<?php

/*---

	Copyright (C) 2008-2009 FluxBB.org
	based on code copyright (C) 2002-2005 Rickard Andersson
	License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher

---*/

require SHELL_PATH . 'include/common.php';

if ($pun_user['g_read_board'] == '0')
    message($lang_common['No view']);
// Load the index.php language file
require SHELL_PATH . 'lang/' . $pun_user['language'] . '/index.php';
// Get list of forums and topics with new posts since last visit
if (!$pun_user['is_guest'])
{
    $db->setQuery('SELECT t.forum_id, t.id, t.last_post FROM ' . $db->tablePrefix . 'topics AS t INNER JOIN ' . $db->tablePrefix . 'forums AS f ON f.id=t.forum_id LEFT JOIN ' . $db->tablePrefix . 'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=' . $pun_user['g_id'] . ') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.last_post>' . $pun_user['last_visit'] . ' AND t.moved_to IS NULL') or error('Unable to fetch new topics', __FILE__, __LINE__, $db->error());

    $new_topics = array();
    while ($cur_topic = $db->fetch_assoc())
    $new_topics[$cur_topic['forum_id']][$cur_topic['id']] = $cur_topic['last_post'];

    $tracked_topics = get_tracked_topics();
}

$page_title = pun_htmlspecialchars($pun_config['o_board_title']);
define('PUN_ALLOW_INDEX', 1);
require SHELL_PATH . 'header.php';
// Print the categories and forums
$db->setQuery('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.forum_desc, f.redirect_url, f.moderators, f.num_topics, f.num_posts, f.last_post, f.last_post_id, f.last_poster FROM ' . $db->tablePrefix . 'categories AS c INNER JOIN ' . $db->tablePrefix . 'forums AS f ON c.id=f.cat_id LEFT JOIN ' . $db->tablePrefix . 'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=' . $pun_user['g_id'] . ') WHERE fp.read_forum IS NULL OR fp.read_forum=1 ORDER BY c.disp_position, c.id, f.disp_position', true) or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

$cur_category = 0;
$cat_count = 0;
while ($cur_forum = $db->fetch_assoc())
{
    $moderators = '';

    if ($cur_forum['cid'] != $cur_category) // A new category since last iteration?
        {
            if ($cur_category != 0)
                echo "\t\t\t" . '</tbody>' . "\n\t\t\t" . '</table>' . "\n\t\t" . '</div>' . "\n\t" . '</div>' . "\n" . '</div>' . "\n\n";

            ++$cat_count;

            ?>
<div id="idx<?php echo $cat_count ?>" class="blocktable">
	<h2><span><?php echo pun_htmlspecialchars($cur_forum['cat_name']) ?></span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
			<thead>
				<tr>
					<th class="tcl" scope="col"><?php echo $lang_common['Forum'] ?></th>
					<th class="tc2" scope="col"><?php echo $lang_index['Topics'] ?></th>
					<th class="tc3" scope="col"><?php echo $lang_common['Posts'] ?></th>
					<th class="tcr" scope="col"><?php echo $lang_common['Last post'] ?></th>
				</tr>
			</thead>
			<tbody>
<?php

            $cur_category = $cur_forum['cid'];
        }

        $item_status = '';
        $icon_text = $lang_common['Normal icon'];
        $icon_type = 'icon';
        // Are there new posts since our last visit?
        if (!$pun_user['is_guest'] && $cur_forum['last_post'] > $pun_user['last_visit'] && (empty($tracked_topics['forums'][$cur_forum['fid']]) || $cur_forum['last_post'] > $tracked_topics['forums'][$cur_forum['fid']]))
        {
            // There are new posts in this forum, but have we read all of them already?
            foreach ($new_topics[$cur_forum['fid']] as $check_topic_id => $check_last_post)
            {
                if ((empty($tracked_topics['topics'][$check_topic_id]) || $tracked_topics['topics'][$check_topic_id] < $check_last_post) && (empty($tracked_topics['forums'][$cur_forum['fid']]) || $tracked_topics['forums'][$cur_forum['fid']] < $check_last_post))
                {
                    $item_status = 'inew';
                    $icon_text = $lang_common['New icon'];
                    $icon_type = 'icon inew';

                    break;
                }
            }
        }
        // Is this a redirect forum?
        if ($cur_forum['redirect_url'] != '')
        {
            $forum_field = '<h3>' . CHtml::link(pun_htmlspecialchars($cur_forum['forum_name']), pun_htmlspecialchars($cur_forum['redirect_url']), array('title' => $lang_index['Link to'] . pun_htmlspecialchars($cur_forum['redirect_url']))) . '</h3>';
            $num_topics = $num_posts = '&nbsp;';
            $item_status = 'iredirect';
            $icon_text = $lang_common['Redirect icon'];
            $icon_type = 'icon';
        }
        else
        {
            $forum_field = '<h3>' . CHtml::link(pun_htmlspecialchars($cur_forum['forum_name']), array('forum/viewforum', 'id' => $cur_forum['fid'])) . '</h3>';
            $num_topics = $cur_forum['num_topics'];
            $num_posts = $cur_forum['num_posts'];
        }

        if ($cur_forum['forum_desc'] != '')
            $forum_field .= "\n\t\t\t\t\t\t\t\t" . $cur_forum['forum_desc'];
        // If there is a last_post/last_poster
        if ($cur_forum['last_post'] != '')
            $last_post = CHtml::link(format_time($cur_forum['last_post']), array('forum/viewtopic', 'pid' => $cur_forum['last_post_id'] . '#p' . $cur_forum['last_post_id'])) . '<span class="byuser">' . $lang_common['by'] . ' ' . pun_htmlspecialchars($cur_forum['last_poster']) . '</span>';
        else
            $last_post = '&nbsp;';

        if ($cur_forum['moderators'] != '')
        {
            $mods_array = unserialize($cur_forum['moderators']);
            $moderators = array();

            while (list($mod_username, $mod_id) = @each($mods_array))
            {
                if ($pun_user['g_view_users'] == '1')
                    $moderators[] = CHtml::link(pun_htmlspecialchars($mod_username), array('forum/profile', 'id' => $mod_id));
                else
                    $moderators[] = pun_htmlspecialchars($mod_username);
            }

            $moderators = "\t\t\t\t\t\t\t\t" . '<p class="modlist"><em>(' . $lang_common['Moderated by'] . '</em> ' . implode(', ', $moderators) . ')</p>' . "\n";
        }

        ?>
				<tr<?php if ($item_status != '') echo ' class="' . $item_status . '"'; ?>>
					<td class="tcl">
						<div class="<?php echo $icon_type ?>"><div class="nosize"><?php echo $icon_text ?></div></div>
						<div class="tclcon">
							<div>
								<?php echo $forum_field . "\n" . $moderators ?>
							</div>
						</div>
					</td>
					<td class="tc2"><?php echo forum_number_format($num_topics) ?></td>
					<td class="tc3"><?php echo forum_number_format($num_posts) ?></td>
					<td class="tcr"><?php echo $last_post ?></td>
				</tr>
<?php

    }
    // Did we output any categories and forums?
    if ($cur_category > 0)
        echo "\t\t\t" . '</tbody>' . "\n\t\t\t" . '</table>' . "\n\t\t" . '</div>' . "\n\t" . '</div>' . "\n" . '</div>' . "\n\n";
    else
        echo '<div id="idx0" class="block"><div class="box"><div class="inbox"><p>' . $lang_index['Empty board'] . '</p></div></div></div>';
    // Collect some statistics from the database
    $db->setQuery('SELECT COUNT(id)-1 FROM ' . $db->tablePrefix . 'users WHERE group_id!=' . PUN_UNVERIFIED) or error('Unable to fetch total user count', __FILE__, __LINE__, $db->error());
    $stats['total_users'] = $db->result($result);

    $db->setQuery('SELECT id, username FROM ' . $db->tablePrefix . 'users WHERE group_id!=' . PUN_UNVERIFIED . ' ORDER BY registered DESC LIMIT 1') or error('Unable to fetch newest registered user', __FILE__, __LINE__, $db->error());
    $stats['last_user'] = $db->fetch_assoc();

    $db->setQuery('SELECT SUM(num_topics), SUM(num_posts) FROM ' . $db->tablePrefix . 'forums') or error('Unable to fetch topic/post count', __FILE__, __LINE__, $db->error());
    list($stats['total_topics'], $stats['total_posts']) = $db->fetch_row();

    if ($pun_user['g_view_users'] == '1')
        $stats['newest_user'] = CHtml::link(pun_htmlspecialchars($stats['last_user']['username']), array('forum/profile', 'id' => $stats['last_user']['id']));
    else
        $stats['newest_user'] = pun_htmlspecialchars($stats['last_user']['username']);

    ?>
<div id="brdstats" class="block">
	<h2><span><?php echo $lang_index['Board info'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<dl class="conr">
				<dt><strong><?php echo $lang_index['Board stats'] ?></strong></dt>
				<dd><?php echo $lang_index['No of users'] . ': <strong>' . forum_number_format($stats['total_users']) ?></strong></dd>
				<dd><?php echo $lang_index['No of topics'] . ': <strong>' . forum_number_format($stats['total_topics']) ?></strong></dd>
				<dd><?php echo $lang_index['No of posts'] . ': <strong>' . forum_number_format($stats['total_posts']) ?></strong></dd>
			</dl>
			<dl class="conl">
				<dt><strong><?php echo $lang_index['User info'] ?></strong></dt>
				<dd><?php echo $lang_index['Newest user'] ?>: <?php echo $stats['newest_user'] ?></dd>
<?php

    if ($pun_config['o_users_online'] == '1')
    {
        // Fetch users online info and generate strings for output
        $num_guests = 0;
        $users = array();
        $db->setQuery('SELECT user_id, ident FROM ' . $db->tablePrefix . 'online WHERE idle=0 ORDER BY ident', true) or error('Unable to fetch online list', __FILE__, __LINE__, $db->error());

        while ($pun_user_online = $db->fetch_assoc())
        {
            if ($pun_user_online['user_id'] > 1)
            {
                if ($pun_user['g_view_users'] == '1')
                    $users[] = "\n\t\t\t\t" . '<dd>' . CHtml::link(pun_htmlspecialchars($pun_user_online['ident']), array('forum/profile', 'id' => $pun_user_online['user_id']));
                else
                    $users[] = "\n\t\t\t\t" . '<dd>' . pun_htmlspecialchars($pun_user_online['ident']);
            }
            else
                ++$num_guests;
        }

        $num_users = count($users);
        echo "\t\t\t\t" . '<dd>' . $lang_index['Users online'] . ': <strong>' . forum_number_format($num_users) . '</strong></dd>' . "\n\t\t\t\t" . '<dd>' . $lang_index['Guests online'] . ': <strong>' . forum_number_format($num_guests) . '</strong></dd>' . "\n\t\t\t" . '</dl>' . "\n";

        if ($num_users > 0)
            echo "\t\t\t" . '<dl id="onlinelist" class="clearb">' . "\n\t\t\t\t" . '<dt><strong>' . $lang_index['Online'] . ':&nbsp;</strong></dt>' . "\t\t\t\t" . implode(',</dd> ', $users) . '</dd>' . "\n\t\t\t" . '</dl>' . "\n";
        else
            echo "\t\t\t" . '<div class="clearer"></div>' . "\n";
    }
    else
        echo "\t\t" . '</dl>' . "\n\t\t\t" . '<div class="clearer"></div>' . "\n";

    ?>
		</div>
	</div>
</div>
<?php

    $footer_style = 'index';
    require SHELL_PATH . 'footer.php';