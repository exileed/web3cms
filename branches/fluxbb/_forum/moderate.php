<?php
require SHELL_PATH . 'include/common.php';
// This particular function doesn't require forum-based moderator access. It can be used
// by all moderators and admins
if (isset($_GET['get_host'])) {
    if (!$_user['is_admmod'])
        message($lang_common['No permission']);
    // Is get_host an IP address or a post ID?
    if (@preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $_GET['get_host']) || @preg_match('/^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/', $_GET['get_host']))
        $ip = $_GET['get_host'];
    else {
        $get_host = intval($_GET['get_host']);
        if ($get_host < 1)
            message($lang_common['Bad request']);
        $db->setQuery('SELECT poster_ip FROM forum_posts WHERE id=' . $get_host) or error('Unable to fetch post IP address', __FILE__, __LINE__, $db->error());
        if (!$db->num_rows())
            message($lang_common['Bad request']);
        $ip = $db->result();
    }
    message('The IP address is: ' . $ip . '<br />The host name is: ' . @gethostbyaddr($ip) . '<br /><br />' . _CHtml::link('Show more users for this IP', array('forum/admin_users', 'show_users' => $ip)));
}
// All other functions require moderator/admin access
$fid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
if ($fid < 1)
    message($lang_common['Bad request']);
$db->setQuery('SELECT moderators FROM forum_forums WHERE id=' . $fid) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());
$moderators = $db->result();
$mods_array = ($moderators != '') ? unserialize($moderators) : array();
if ($_user['g_id'] != PUN_ADMIN && ($_user['g_moderator'] == '0' || !array_key_exists($_user['username'], $mods_array)))
    message($lang_common['No permission']);
// Get topic/forum tracking data
if (!$_user['is_guest'])
    $tracked_topics = get_tracked_topics();
// Load the misc.php language file
require SHELL_PATH . 'lang/' . $_user['language'] . '/misc.php';
// All other topic moderation features require a topic ID in GET
if (isset($_GET['tid'])) {
    $tid = intval($_GET['tid']);
    if ($tid < 1)
        message($lang_common['Bad request']);
    // Fetch some info about the topic
    $db->setQuery('SELECT t.subject, t.num_replies, f.id AS forum_id, forum_name FROM forum_topics AS t INNER JOIN forum_forums AS f ON f.id=t.forum_id LEFT JOIN forum_subscriptions AS s ON (t.id=s.topic_id AND s.user_id=' . $_user['id'] . ') LEFT JOIN forum_forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=' . $_user['g_id'] . ') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id=' . $fid . ' AND t.id=' . $tid . ' AND t.moved_to IS NULL') or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());
    if (!$db->num_rows())
        message($lang_common['Bad request']);
    $cur_topic = $db->fetch_assoc();
    // Delete one or more posts
    if (isset($_POST['delete_posts']) || isset($_POST['delete_posts_comply'])) {
        $posts = isset($_POST['posts']) ? $_POST['posts'] : array();
        if (empty($posts))
            message($lang_misc['No posts selected']);
        if (isset($_POST['delete_posts_comply'])) {
            confirm_referrer('moderate.php');
            if (@preg_match('/[^0-9,]/', $posts))
                message($lang_common['Bad request']);
            // Verify that the post IDs are valid
            $db->setQuery('SELECT 1 FROM forum_posts WHERE id IN(' . $posts . ') AND topic_id=' . $tid) or error('Unable to check posts', __FILE__, __LINE__, $db->error());
            if ($db->num_rows() != substr_count($posts, ',') + 1)
                message($lang_common['Bad request']);
            // Delete the posts
            $db->setQuery('DELETE FROM forum_posts WHERE id IN(' . $posts . ')')->execute() or error('Unable to delete posts', __FILE__, __LINE__, $db->error());
            require SHELL_PATH . 'include/search_idx.php';
            strip_search_index($posts);
            // Get last_post, last_post_id, and last_poster for the topic after deletion
            $db->setQuery('SELECT id, poster, posted FROM forum_posts WHERE topic_id=' . $tid . ' ORDER BY id DESC LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
            $last_post = $db->fetch_assoc();
            // How many posts did we just delete?
            $num_posts_deleted = substr_count($posts, ',') + 1;
            // Update the topic
            $db->setQuery('UPDATE forum_topics SET last_post=' . $last_post['posted'] . ', last_post_id=' . $last_post['id'] . ', last_poster=\'' . $db->escape($last_post['poster']) . '\', num_replies=num_replies-' . $num_posts_deleted . ' WHERE id=' . $tid)->execute() or error('Unable to update topic', __FILE__, __LINE__, $db->error());
            update_forum($fid);
            redirect('viewtopic.php?id=' . $tid, $lang_misc['Delete posts redirect']);
        }
        $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_misc['Moderate'];
        require SHELL_PATH . 'header.php';
        ?>
<div class="blockform">
	<h2><span><?php echo $lang_misc['Delete posts'] ?></span></h2>
	<div class="box">
		<?php echo _CHtml::form(array('moderate', 'fid' => $fid, 'tid' => $tid), 'POST');?>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_misc['Confirm delete legend'] ?></legend>
					<div class="infldset">
						<input type="hidden" name="posts" value="<?php echo implode(',', array_map('intval', array_keys($posts))) ?>" />
						<p><?php echo $lang_misc['Delete posts comply'] ?></p>
					</div>
				</fieldset>
			</div>
			<p class="buttons"><input type="submit" name="delete_posts_comply" value="<?php echo $lang_misc['Delete'] ?>" />
			<?php echo _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></p>
		</form>
	</div>
</div>
<?php require SHELL_PATH . 'footer.php';
    }else if (isset($_POST['split_posts']) || isset($_POST['split_posts_comply'])) {
        $posts = isset($_POST['posts']) ? $_POST['posts'] : array();
        if (empty($posts))
            message($lang_misc['No posts selected']);
        if (isset($_POST['split_posts_comply'])) {
            confirm_referrer('moderate.php');
            if (@preg_match('/[^0-9,]/', $posts))
                message($lang_common['Bad request']);
            // How many posts did we just split off?
            $num_posts_splitted = substr_count($posts, ',') + 1;
            // Verify that the post IDs are valid
            $db->setQuery('SELECT 1 FROM forum_posts WHERE id IN(' . $posts . ') AND topic_id=' . $tid) or error('Unable to check posts', __FILE__, __LINE__, $db->error());
            if ($db->num_rows() != $num_posts_splitted)
                message($lang_common['Bad request']);
            // Load the post.php language file
            require SHELL_PATH . 'lang/' . $_user['language'] . '/post.php';
            // Check subject
            $new_subject = isset($_POST['new_subject']) ? _trim($_POST['new_subject']) : '';
            if ($new_subject == '')
                message($lang_post['No subject']);
            else if (_strlen($new_subject) > 70)
                message($lang_post['Too long subject']);
            // Get data from the new first post
            $db->setQuery('SELECT p.id, p.poster, p.posted FROM forum_posts AS p WHERE id IN(' . $posts . ') ORDER BY p.id ASC LIMIT 1') or error('Unable to get first post', __FILE__, __LINE__, $db->error());
            $first_post_data = $db->fetch_assoc();
            // Create the new topic
            $db->setQuery('INSERT INTO forum_topics (poster, subject, posted, first_post_id, forum_id) VALUES (\'' . $db->escape($first_post_data['poster']) . '\', \'' . $db->escape($new_subject) . '\', ' . $first_post_data['posted'] . ', ' . $first_post_data['id'] . ', ' . $fid . ')')->execute() or error('Unable to create new topic', __FILE__, __LINE__, $db->error());
            $new_tid = $db->insert_id();
            // Move the posts to the new topic
            $db->setQuery('UPDATE forum_posts SET topic_id=' . $new_tid . ' WHERE id IN(' . $posts . ')')->execute() or error('Unable to move posts into new topic', __FILE__, __LINE__, $db->error());
            // Get last_post, last_post_id, and last_poster from the topic and update it
            $db->setQuery('SELECT id, poster, posted FROM forum_posts WHERE topic_id=' . $tid . ' ORDER BY id DESC LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
            $last_post_data = $db->fetch_assoc();
            $db->setQuery('UPDATE forum_topics SET last_post=' . $last_post_data['posted'] . ', last_post_id=' . $last_post_data['id'] . ', last_poster=\'' . $db->escape($last_post_data['poster']) . '\', num_replies=num_replies-' . $num_posts_splitted . ' WHERE id=' . $tid)->execute() or error('Unable to update topic', __FILE__, __LINE__, $db->error());
            // Get last_post, last_post_id, and last_poster from the new topic and update it
            $db->setQuery('SELECT id, poster, posted FROM forum_posts WHERE topic_id=' . $new_tid . ' ORDER BY id DESC LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
            $last_post_data = $db->fetch_assoc();
            $db->setQuery('UPDATE forum_topics SET last_post=' . $last_post_data['posted'] . ', last_post_id=' . $last_post_data['id'] . ', last_poster=\'' . $db->escape($last_post_data['poster']) . '\', num_replies=' . ($num_posts_splitted - 1) . ' WHERE id=' . $new_tid)->execute() or error('Unable to update topic', __FILE__, __LINE__, $db->error());
            update_forum($fid);
            redirect('viewtopic.php?id=' . $new_tid, $lang_misc['Split posts redirect']);
        }
        $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_misc['Moderate'];
        $focus_element = array('subject', 'new_subject');
        require SHELL_PATH . 'header.php';
        ?>
<div class="blockform">
	<h2><span><?php echo $lang_misc['Split posts'] ?></span></h2>
	<div class="box">
		<?php echo _CHtml::form(array('moderate', 'fid' => $fid, 'tid' => $tid), 'POST', array('id' => 'subject'));?>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_misc['Confirm split legend'] ?></legend>
					<div class="infldset">
						<input type="hidden" name="posts" value="<?php echo implode(',', array_map('intval', array_keys($posts))) ?>" />
						<label><strong><?php echo $lang_misc['New subject'] ?></strong><br /><input type="text" name="new_subject" size="80" maxlength="70" /><br /></label>
						<p><?php echo $lang_misc['Split posts comply'] ?></p>
					</div>
				</fieldset>
			</div>
			<p class="buttons"><input type="submit" name="split_posts_comply" value="<?php echo $lang_misc['Split'] ?>" />
			<?php echo _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></p>
		</form>
	</div>
</div>
<?php require SHELL_PATH . 'footer.php';
    }
    // Show the moderate posts view
    // Load the viewtopic.php language file
    require SHELL_PATH . 'lang/' . $_user['language'] . '/topic.php';
    // Used to disable the Move and Delete buttons if there are no replies to this topic
    $button_status = ($cur_topic['num_replies'] == 0) ? ' disabled="disabled"' : '';
    // Determine the post offset (based on $_GET['p'])
    $num_pages = ceil(($cur_topic['num_replies'] + 1) / $_user['disp_posts']);
    $p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : intval($_GET['p']);
    $start_from = $_user['disp_posts'] * ($p - 1);
    // Generate paging links
    $paging_links = $lang_common['Pages'] . ': ' . paginate($num_pages, $p, 'moderate.php?fid=' . $fid . '&amp;tid=' . $tid);
    if ($_config['o_censoring'] == '1')
        $cur_topic['subject'] = censor_words($cur_topic['subject']);
    $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $cur_topic['subject'];
    require SHELL_PATH . 'header.php';
    ?>
<div class="linkst">
	<div class="inbox">
		<ul class="crumbs">
			<li><?php echo _CHtml::link($lang_common['Index'], array('forum/'));?></li>
			<li>&raquo;&nbsp;'
			<?php echo _CHtml::link(_CHtml::encode($cur_topic['forum_name']), array('forum/viewforum', 'id' => $fid));?>
			</li>
			<li>&raquo;&nbsp;<?php echo _CHtml::encode($cur_topic['subject']) ?></li>
		</ul>
		<p class="pagelink conl"><?php echo $paging_links ?></p>
		<div class="clearer"></div>
	</div>
</div><?php echo _CHtml::form(array('moderate', 'fid' => $fid, 'tid' => $tid), 'POST');
    require SHELL_PATH . 'include/parser.php';
    $post_count = 0; // Keep track of post numbers    // Retrieve the posts (and their respective poster)
    $db->setQuery('SELECT ud.title, ud.num_posts, g.g_id, g.g_user_title, p.id, p.poster, p.poster_id, p.message, p.hide_smilies, p.posted, p.edited, p.edited_by FROM forum_posts AS p INNER JOIN w3_user AS u ON u.id=p.poster_id INNER JOIN forum_groups AS g ON g.g_id=ud.forumGroupId WHERE p.topic_id=' . $tid . ' ORDER BY p.id LIMIT ' . $start_from . ',' . $_user['disp_posts'], true) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
    while ($cur_post = $db->fetch_assoc()) {
        $post_count++;
        // If the poster is a registered user
        if ($cur_post['poster_id'] > 1) {
            if ($_user['g_view_users'] == '1')
                $poster = _CHtml::link(_CHtml::encode($cur_post['poster']), array('forum/profile', 'id' => $cur_post['poster_id']));
            else
                $poster = _CHtml::encode($cur_post['poster']);
            // get_title() requires that an element 'username' be present in the array
            $cur_post['username'] = $cur_post['poster'];
            $user_title = get_title($cur_post);
            if ($_config['o_censoring'] == '1')
                $user_title = censor_words($user_title);
        }
        // If the poster is a guest (or a user that has been deleted)
        else {
            $poster = _CHtml::encode($cur_post['poster']);
            $user_title = $lang_topic['Guest'];
        }
        // Perform the main parsing of the message (BBCode, smilies, censor words etc)
        $cur_post['message'] = parse_message($cur_post['message'], $cur_post['hide_smilies']);
        ?><div id="p<?php echo $cur_post['id'] ?>" class="blockpost<?php echo ($post_count % 2 == 0) ? ' roweven' : ' rowodd' ?>">
	<h2><span><span class="conr">#<?php echo ($start_from + $post_count) ?></span> ' . _CHtml::link(MDate::format($cur_post['posted']), array('forum/viewtopic', 'pid' => $cur_post['id'] . '#p' . $cur_post['id']));?></span></h2>
	<div class="box">
		<div class="inbox">
			<div class="postbody">
				<div class="postleft">
					<dl>
						<dt><strong><?php echo $poster ?></strong></dt>
						<dd><strong><?php echo $user_title ?></strong></dd>
					</dl>
				</div>
				<div class="postright">
					<h3 class="nosize"><?php echo $lang_common['Message'] ?></h3>
					<div class="postmsg">
						<?php echo $cur_post['message'] . "\n" ?>
<?php if ($cur_post['edited'] != '') echo "\t\t\t\t\t\t" . '<p class="postedit"><em>' . $lang_topic['Last edit'] . ' ' . _CHtml::encode($cur_post['edited_by']) . ' (' . MDate::format($cur_post['edited']) . ')</em></p>' . "\n"; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="inbox">
			<div class="postfoot clearb">
				<div class="postfootright"><?php echo ($start_from + $post_count > 1) ? '<p class="multidelete"><label><strong>' . $lang_misc['Select'] . '</strong>&nbsp;&nbsp;<input type="checkbox" name="posts[' . $cur_post['id'] . ']" value="1" /></label></p>' : '<p>' . $lang_misc['Cannot delete first'] . '</p>' ?></div>
			</div>
		</div>
	</div>
</div><?php } ?>
<div class="postlinksb">
	<div class="inbox">
		<p class="pagelink conl"><?php echo $paging_links ?></p>
		<p class="conr modbuttons"><input type="submit" name="split_posts" value="<?php echo $lang_misc['Split'] ?>"<?php echo $button_status ?> /> <input type="submit" name="delete_posts" value="<?php echo $lang_misc['Delete'] ?>"<?php echo $button_status ?> /></p>
		<div class="clearer"></div>
	</div>
</div>
</form>
<?php require SHELL_PATH . 'footer.php';
}
// Move one or more topics
if (isset($_REQUEST['move_topics']) || isset($_POST['move_topics_to'])) {
    if (isset($_POST['move_topics_to'])) {
        confirm_referrer('moderate.php');
        if (@preg_match('/[^0-9,]/', $_POST['topics']))
            message($lang_common['Bad request']);
        $topics = explode(',', $_POST['topics']);
        $move_to_forum = isset($_POST['move_to_forum']) ? intval($_POST['move_to_forum']) : 0;
        if (empty($topics) || $move_to_forum < 1)
            message($lang_common['Bad request']);
        // Verify that the topic IDs are valid
        $db->setQuery('SELECT 1 FROM forum_topics WHERE id IN(' . implode(',', $topics) . ') AND forum_id=' . $fid) or error('Unable to check topics', __FILE__, __LINE__, $db->error());
        if ($db->num_rows() != count($topics))
            message($lang_common['Bad request']);
        // Delete any redirect topics if there are any (only if we moved/copied the topic back to where it was once moved from)
        $db->setQuery('DELETE FROM forum_topics WHERE forum_id=' . $move_to_forum . ' AND moved_to IN(' . implode(',', $topics) . ')')->execute() or error('Unable to delete redirect topics', __FILE__, __LINE__, $db->error());
        // Move the topic(s)
        $db->setQuery('UPDATE forum_topics SET forum_id=' . $move_to_forum . ' WHERE id IN(' . implode(',', $topics) . ')')->execute() or error('Unable to move topics', __FILE__, __LINE__, $db->error());
        // Should we create redirect topics?
        if (isset($_POST['with_redirect'])) {
            while (list(, $cur_topic) = @each($topics)) {
                // Fetch info for the redirect topic
                $db->setQuery('SELECT poster, subject, posted, last_post FROM forum_topics WHERE id=' . $cur_topic) or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());
                $moved_to = $db->fetch_assoc();
                // Create the redirect topic
                $db->setQuery('INSERT INTO forum_topics (poster, subject, posted, last_post, moved_to, forum_id) VALUES(\'' . $db->escape($moved_to['poster']) . '\', \'' . $db->escape($moved_to['subject']) . '\', ' . $moved_to['posted'] . ', ' . $moved_to['last_post'] . ', ' . $cur_topic . ', ' . $fid . ')')->execute() or error('Unable to create redirect topic', __FILE__, __LINE__, $db->error());
            }
        }
        update_forum($fid); // Update the forum FROM which the topic was moved
        update_forum($move_to_forum); // Update the forum TO which the topic was moved        $redirect_msg = (count($topics) > 1) ? $lang_misc['Move topics redirect'] : $lang_misc['Move topic redirect'];
        redirect('viewforum.php?id=' . $move_to_forum, $redirect_msg);
    }
    if (isset($_POST['move_topics'])) {
        $topics = isset($_POST['topics']) ? $_POST['topics'] : array();
        if (empty($topics))
            message($lang_misc['No topics selected']);
        $topics = implode(',', array_map('intval', array_keys($topics)));
        $action = 'multi';
    }else {
        $topics = intval($_GET['move_topics']);
        if ($topics < 1)
            message($lang_common['Bad request']);
        $action = 'single';
    }
    $db->setQuery('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name FROM forum_categories AS c INNER JOIN forum_forums AS f ON c.id=f.cat_id LEFT JOIN forum_forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=' . $_user['g_id'] . ') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.redirect_url IS NULL ORDER BY c.disp_position, c.id, f.disp_position') or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());
    if ($db->num_rows() < 2)
        message($lang_misc['Nowhere to move']);
    $page_title = _CHtml::encode($this->PageTitle) . ' / Moderate';
    require SHELL_PATH . 'header.php';
    ?>
<div class="blockform">
	<h2><span><?php echo ($action == 'single') ? $lang_misc['Move topic'] : $lang_misc['Move topics'] ?></span></h2>
	<div class="box">
		<?php echo _CHtml::form(array('moderate', 'fid' => $fid), 'POST');?>
			<div class="inform">
			<input type="hidden" name="topics" value="<?php echo $topics ?>" />
				<fieldset>
					<legend><?php echo $lang_misc['Move legend'] ?></legend>
					<div class="infldset">
						<label><?php echo $lang_misc['Move to'] ?>
						<br /><select name="move_to_forum">
<?php $cur_category = 0;
    while ($cur_forum = $db->fetch_assoc()) {
        if ($cur_forum['cid'] != $cur_category) { // A new category since last iteration?
                if ($cur_category)
                    echo "\t\t\t\t\t\t\t" . '</optgroup>' . "\n";
                echo "\t\t\t\t\t\t\t" . '<optgroup label="' . _CHtml::encode($cur_forum['cat_name']) . '">' . "\n";
                $cur_category = $cur_forum['cid'];
            }
            if ($cur_forum['fid'] != $fid)
                echo "\t\t\t\t\t\t\t\t" . '<option value="' . $cur_forum['fid'] . '">' . _CHtml::encode($cur_forum['forum_name']) . '</option>' . "\n";
        }
        ?>
							</optgroup>
						</select>
						<br /></label>
						<div class="rbox">
							<label><input type="checkbox" name="with_redirect" value="1"<?php if ($action == 'single') echo ' checked="checked"' ?> /><?php echo $lang_misc['Leave redirect'] ?><br /></label>
						</div>
					</div>
				</fieldset>
			</div>
			<p class="buttons"><input type="submit" name="move_topics_to" value="<?php echo $lang_misc['Move'] ?>" /> ' . _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1)');?></p>
		</form>
	</div>
</div>
<?php require SHELL_PATH . 'footer.php';
    }
    // Merge two or more topics
    else if (isset($_POST['merge_topics']) || isset($_POST['merge_topics_comply'])) {
        if (isset($_POST['merge_topics_comply'])) {
            confirm_referrer('moderate.php');
            if (@preg_match('/[^0-9,]/', $_POST['topics']))
                message($lang_common['Bad request']);
            $topics = explode(',', $_POST['topics']);
            if (count($topics) < 2)
                message($lang_misc['Not enough topics selected']);
            // Verify that the topic IDs are valid (moved topics can not be merged?)
            // $db->setQuery('SELECT 1 FROM '.$db->tablePrefix.'topics WHERE id IN('.implode(',', $topics).') AND moved_to IS NULL AND forum_id='.$fid) or error('Unable to check topics', __FILE__, __LINE__, $db->error());
            $db->setQuery('SELECT 1 FROM forum_topics WHERE id IN(' . implode(',', $topics) . ') AND forum_id=' . $fid) or error('Unable to check topics', __FILE__, __LINE__, $db->error());
            if ($db->num_rows() != count($topics))
                message($lang_common['Bad request']);
            // Fetch the topic that we're merging into
            $db->setQuery('SELECT MIN(t.id) FROM forum_topics AS t WHERE t.id IN(' . implode(',', $topics) . ')') or error('Unable to get topic', __FILE__, __LINE__, $db->error());
            $merge_to_tid = $db->result();
            // Make any redirect topics point to our new, merged topic
            $query = 'UPDATE forum_topics SET moved_to=' . $merge_to_tid . ' WHERE moved_to IN(' . implode(',', $topics) . ')';
            // Should we create redirect topics?
            if (isset($_POST['with_redirect']))
                $query .= ' OR (id IN(' . implode(',', $topics) . ') AND id != ' . $merge_to_tid . ')';
            $db->setQuery($query)->execute() or error('Unable to make redirection topics', __FILE__, __LINE__, $db->error());
            // Merge the posts into the topic
            $db->setQuery('UPDATE forum_posts SET topic_id=' . $merge_to_tid . ' WHERE topic_id IN(' . implode(',', $topics) . ')')->execute() or error('Unable to merge the posts into the topic', __FILE__, __LINE__, $db->error());
            // Delete any subscriptions
            $db->setQuery('DELETE FROM forum_subscriptions WHERE topic_id IN(' . implode(',', $topics) . ') AND topic_id != ' . $merge_to_tid)->execute() or error('Unable to delete subscriptions', __FILE__, __LINE__, $db->error());
            // Without redirection the old topics are removed
            if (!isset($_POST['with_redirect']))
                $db->setQuery('DELETE FROM forum_topics WHERE id IN(' . implode(',', $topics) . ') AND id != ' . $merge_to_tid)->execute() or error('Unable to delete old topics', __FILE__, __LINE__, $db->error());
            // Count number of replies in the topic
            $db->setQuery('SELECT COUNT(id) FROM forum_posts WHERE topic_id=' . $merge_to_tid) or error('Unable to fetch post count for topic', __FILE__, __LINE__, $db->error());
            $num_replies = $db->result($result, 0) - 1;
            // Get last_post, last_post_id and last_poster
            $db->setQuery('SELECT posted, id, poster FROM forum_posts WHERE topic_id=' . $merge_to_tid . ' ORDER BY id DESC LIMIT 1') or error('Unable to get last post info', __FILE__, __LINE__, $db->error());
            list($last_post, $last_post_id, $last_poster) = $db->fetch_row();
            // Update topic
            $db->setQuery('UPDATE forum_topics SET num_replies=' . $num_replies . ', last_post=' . $last_post . ', last_post_id=' . $last_post_id . ', last_poster=\'' . $db->escape($last_poster) . '\' WHERE id=' . $merge_to_tid)->execute() or error('Unable to update topic', __FILE__, __LINE__, $db->error());
            // Update the forum FROM which the topic was moved and redirect
            update_forum($fid);
            redirect('viewforum.php?id=' . $fid, $lang_misc['Merge topics redirect']);
        }
        $topics = isset($_POST['topics']) ? $_POST['topics'] : array();
        if (count($topics) < 2)
            message($lang_misc['Not enough topics selected']);
        $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_misc['Moderate'];
        require SHELL_PATH . 'header.php';
        ?>
<div class="blockform">
	<h2><?php echo $lang_misc['Merge topics'] ?></h2>
	<div class="box">
		<?php echo _CHtml::form(array('moderate', 'fid' => $fid), 'POST');?>
			<input type="hidden" name="topics" value="<?php echo implode(',', array_map('intval', array_keys($topics))) ?>" />
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_misc['Confirm merge legend'] ?></legend>
					<div class="infldset">
						<div class="rbox">
							<label><input type="checkbox" name="with_redirect" value="1" /><?php echo $lang_misc['Leave redirect'] ?><br /></label>
						</div>
					</div>
				</fieldset>
			</div>
			<p class="buttons"><input type="submit" name="merge_topics_comply" value="<?php echo $lang_misc['Merge'] ?>" /> ' . _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></p>
		</form>
	</div>
</div>
<?php require SHELL_PATH . 'footer.php';
    }
    // Delete one or more topics
    else if (isset($_POST['delete_topics']) || isset($_POST['delete_topics_comply'])) {
        $topics = isset($_POST['topics']) ? $_POST['topics'] : array();
        if (empty($topics))
            message($lang_misc['No topics selected']);
        if (isset($_POST['delete_topics_comply'])) {
            confirm_referrer('moderate.php');
            if (@preg_match('/[^0-9,]/', $topics))
                message($lang_common['Bad request']);
            require SHELL_PATH . 'include/search_idx.php';
            // Verify that the topic IDs are valid
            $db->setQuery('SELECT 1 FROM forum_topics WHERE id IN(' . $topics . ') AND forum_id=' . $fid) or error('Unable to check topics', __FILE__, __LINE__, $db->error());
            if ($db->num_rows() != substr_count($topics, ',') + 1)
                message($lang_common['Bad request']);
            // Delete the topics and any redirect topics
            $db->setQuery('DELETE FROM forum_topics WHERE id IN(' . $topics . ') OR moved_to IN(' . $topics . ')')->execute() or error('Unable to delete topic', __FILE__, __LINE__, $db->error());
            // Delete any subscriptions
            $db->setQuery('DELETE FROM forum_subscriptions WHERE topic_id IN(' . $topics . ')')->execute() or error('Unable to delete subscriptions', __FILE__, __LINE__, $db->error());
            // Create a list of the post IDs in this topic and then strip the search index
            $db->setQuery('SELECT id FROM forum_posts WHERE topic_id IN(' . $topics . ')') or error('Unable to fetch posts', __FILE__, __LINE__, $db->error());
            $post_ids = '';
            while ($row = $db->fetch_row())
            $post_ids .= ($post_ids != '') ? ',' . $row[0] : $row[0];
            // We have to check that we actually have a list of post IDs since we could be deleting just a redirect topic
            if ($post_ids != '')
                strip_search_index($post_ids);
            // Delete posts
            $db->setQuery('DELETE FROM forum_posts WHERE topic_id IN(' . $topics . ')')->execute() or error('Unable to delete posts', __FILE__, __LINE__, $db->error());
            update_forum($fid);
            redirect('viewforum.php?id=' . $fid, $lang_misc['Delete topics redirect']);
        }
        $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_misc['Moderate'];
        require SHELL_PATH . 'header.php';
        ?>
<div class="blockform">
	<h2><?php echo $lang_misc['Delete topics'] ?></h2>
	<div class="box">
		<?php echo _CHtml::form(array('moderate', 'fid' => $fid), 'POST');?>
			<input type="hidden" name="topics" value="<?php echo implode(',', array_map('intval', array_keys($topics))) ?>" />
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_misc['Confirm delete legend'] ?></legend>
					<div class="infldset">
						<p><?php echo $lang_misc['Delete topics comply'] ?></p>
					</div>
				</fieldset>
			</div>
			<p class="buttons"><input type="submit" name="delete_topics_comply" value="<?php echo $lang_misc['Delete'] ?>" />
			<?php echo _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></p>
		</form>
	</div>
</div>
<?php require SHELL_PATH . 'footer.php';
    }
    // Open or close one or more topics
    else if (isset($_REQUEST['open']) || isset($_REQUEST['close'])) {
        $action = (isset($_REQUEST['open'])) ? 0 : 1;
        // There could be an array of topic IDs in $_POST
        if (isset($_POST['open']) || isset($_POST['close'])) {
            confirm_referrer('moderate.php');
            $topics = isset($_POST['topics']) ? @array_map('intval', @array_keys($_POST['topics'])) : array();
            if (empty($topics))
                message($lang_misc['No topics selected']);
            $db->setQuery('UPDATE forum_topics SET closed=' . $action . ' WHERE id IN(' . implode(',', $topics) . ') AND forum_id=' . $fid)->execute() or error('Unable to close topics', __FILE__, __LINE__, $db->error());
            $redirect_msg = ($action) ? $lang_misc['Close topics redirect'] : $lang_misc['Open topics redirect'];
            redirect('moderate.php?fid=' . $fid, $redirect_msg);
        }
        // Or just one in $_GET
        else {
            confirm_referrer('viewtopic.php');
            $topic_id = ($action) ? intval($_GET['close']) : intval($_GET['open']);
            if ($topic_id < 1)
                message($lang_common['Bad request']);
            $db->setQuery('UPDATE forum_topics SET closed=' . $action . ' WHERE id=' . $topic_id . ' AND forum_id=' . $fid)->execute() or error('Unable to close topic', __FILE__, __LINE__, $db->error());
            $redirect_msg = ($action) ? $lang_misc['Close topic redirect'] : $lang_misc['Open topic redirect'];
            redirect('viewtopic.php?id=' . $topic_id, $redirect_msg);
        }
    }
    // Stick a topic
    else if (isset($_GET['stick'])) {
        confirm_referrer('viewtopic.php');
        $stick = intval($_GET['stick']);
        if ($stick < 1)
            message($lang_common['Bad request']);
        $db->setQuery('UPDATE forum_topics SET sticky=\'1\' WHERE id=' . $stick . ' AND forum_id=' . $fid)->execute() or error('Unable to stick topic', __FILE__, __LINE__, $db->error());
        redirect('viewtopic.php?id=' . $stick, $lang_misc['Stick topic redirect']);
    }
    // Unstick a topic
    else if (isset($_GET['unstick'])) {
        confirm_referrer('viewtopic.php');
        $unstick = intval($_GET['unstick']);
        if ($unstick < 1)
            message($lang_common['Bad request']);
        $db->setQuery('UPDATE forum_topics SET sticky=\'0\' WHERE id=' . $unstick . ' AND forum_id=' . $fid)->execute() or error('Unable to unstick topic', __FILE__, __LINE__, $db->error());
        redirect('viewtopic.php?id=' . $unstick, $lang_misc['Unstick topic redirect']);
    }
    // No specific forum moderation action was specified in the query string, so we'll display the moderator forum
    // Load the viewforum.php language file
    require SHELL_PATH . 'lang/' . $_user['language'] . '/forum.php';
    // Fetch some info about the forum
    $db->setQuery('SELECT f.forum_name, f.redirect_url, f.num_topics FROM forum_forums AS f LEFT JOIN forum_forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=' . $_user['g_id'] . ') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id=' . $fid) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());
    if (!$db->num_rows())
        message($lang_common['Bad request']);
    $cur_forum = $db->fetch_assoc();
    // Is this a redirect forum? In that case, abort!
    if ($cur_forum['redirect_url'] != '')
        message($lang_common['Bad request']);
    $page_title = _CHtml::encode($this->PageTitle) . ' / ' . _CHtml::encode($cur_forum['forum_name']);
    require SHELL_PATH . 'header.php';
    // Determine the topic offset (based on $_GET['p'])
    $num_pages = ceil($cur_forum['num_topics'] / $_user['disp_topics']);
    $p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : intval($_GET['p']);
    $start_from = $_user['disp_topics'] * ($p - 1);
    // Generate paging links
    $paging_links = $lang_common['Pages'] . ': ' . paginate($num_pages, $p, 'moderate.php?fid=' . $fid) ?>
<div class="linkst">
	<div class="inbox">
		<ul class="crumbs">
			<li><?php echo _CHtml::link($lang_common['Index'], array('forum/'));?></li>
			<li>&raquo;&nbsp;<?php echo _CHtml::encode($cur_forum['forum_name']) ?></li>
		</ul>
		<p class="pagelink conl"><?php echo $paging_links ?></p>
		<div class="clearer"></div>
	</div>
</div><?php echo _CHtml::form(array('moderate', 'fid' => $fid), 'POST');?>
<div id="vf" class="blocktable">
	<h2><span><?php echo _CHtml::encode($cur_forum['forum_name']) ?></span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
			<thead>
				<tr>
					<th class="tcl" scope="col"><?php echo $lang_common['Topic'] ?></th>
					<th class="tc2" scope="col"><?php echo $lang_common['Replies'] ?></th>
<?php if ($_config['o_topic_views'] == '1'): ?>					<th class="tc3" scope="col"><?php echo $lang_forum['Views'] ?></th>
<?php endif; ?>					<th class="tcr"><?php echo $lang_common['Last post'] ?></th>
					<th class="tcmod" scope="col"><?php echo $lang_misc['Select'] ?></th>
				</tr>
			</thead>
			<tbody>
<?php
    // Select topics
    $db->setQuery('SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, num_views, num_replies, closed, sticky, moved_to FROM forum_topics WHERE forum_id=' . $fid . ' ORDER BY sticky DESC, last_post DESC LIMIT ' . $start_from . ', ' . $_user['disp_topics']) or error('Unable to fetch topic list for forum', __FILE__, __LINE__, $db->error());
    // If there are topics in this forum
    if ($db->num_rows()) {
        $button_status = '';
        while ($cur_topic = $db->fetch_assoc()) {
            $icon_text = $lang_common['Normal icon'];
            $item_status = '';
            $icon_type = 'icon';
            if ($cur_topic['moved_to'] == null) {
                $last_post = _CHtml::link(MDate::format($cur_topic['last_post']), array('forum/viewtopic', 'pid' => $cur_topic['last_post_id'] . '#p' . $cur_topic['last_post_id'])) . $lang_common['by'] . ' ' . _CHtml::encode($cur_topic['last_poster']);
                $ghost_topic = false;
            }else {
                $last_post = '&nbsp;';
                $ghost_topic = true;
            }
            if ($_config['o_censoring'] == '1')
                $cur_topic['subject'] = censor_words($cur_topic['subject']);
            if ($cur_topic['moved_to'] != 0)
                $subject = $lang_forum['Moved'] . ': ' . _CHtml::link(_CHtml::encode($cur_topic['subject']), array('forum/viewtopic', 'id' => $cur_topic['moved_to'])) . '
            	<span class="byuser">' . $lang_common['by'] . ' ' . _CHtml::encode($cur_topic['poster']) . '</span>';
            else if ($cur_topic['closed'] == '0')
                $subject = _CHtml::link(_CHtml::encode($cur_topic['subject']), array('forum/viewtopic', 'id' => $cur_topic['id'])) . '<span>' . $lang_common['by'] . '&nbsp;' . _CHtml::encode($cur_topic['poster']) . '</span>';
            else {
                $subject = _CHtml::link(_CHtml::encode($cur_topic['subject']), array('forum/viewtopic', 'id' => $cur_topic['id'])) . '<span class="byuser">' . $lang_common['by'] . ' ' . _CHtml::encode($cur_topic['poster']) . '</span>';
                $icon_text = $lang_common['Closed icon'];
                $item_status = 'iclosed';
            }
            if (!$ghost_topic && $cur_topic['last_post'] > $_user['last_visit'] && (!isset($tracked_topics['topics'][$cur_topic['id']]) || $tracked_topics['topics'][$cur_topic['id']] < $cur_topic['last_post']) && (!isset($tracked_topics['forums'][$fid]) || $tracked_topics['forums'][$fid] < $cur_topic['last_post'])) {
                $icon_text .= ' ' . $lang_common['New icon'];
                $item_status .= ' inew';
                $icon_type = 'icon inew';
                $subject = '<strong>' . $subject . '</strong>';
                $subject_new_posts = '<span class="newtext">[ ' . _CHtml::link($lang_common['New posts'], array('forum/viewtopic', 'id' => $cur_topic['id'], 'action' => 'new'), array('title' => $lang_common['New posts info'])) . '</span>';
            }else
                $subject_new_posts = null;
            if ($cur_topic['sticky'] == '1') {
                $subject = '<span class="stickytext">' . $lang_forum['Sticky'] . ': </span>' . $subject;
                $item_status .= ' isticky';
                $icon_text .= ' ' . $lang_forum['Sticky'];
            }
            $num_pages_topic = ceil(($cur_topic['num_replies'] + 1) / $_user['disp_posts']);
            if ($num_pages_topic > 1)
                $subject_multipage = '<span class="pagestext">[ ' . paginate($num_pages_topic, - 1, 'viewtopic.php?id=' . $cur_topic['id']) . ' ]</span>';
            else
                $subject_multipage = null;
            // Should we show the "New posts" and/or the multipage links?
            if (!empty($subject_new_posts) || !empty($subject_multipage)) {
                $subject .= !empty($subject_new_posts) ? ' ' . $subject_new_posts : '';
                $subject .= !empty($subject_multipage) ? ' ' . $subject_multipage : '';
            }
            ?>
				<tr<?php if ($item_status != '') echo ' class="' . trim($item_status) . '"'; ?>>
					<td class="tcl">
						<div class="<?php echo $icon_type ?>"><div class="nosize"><?php echo trim($icon_text) ?></div></div>
						<div class="tclcon">
							<div>
								<?php echo $subject . "\n" ?>
							</div>
						</div>
					</td>
					<td class="tc2"><?php echo (!$ghost_topic) ? forum_number_format($cur_topic['num_replies']) : '&nbsp;' ?></td>
<?php if ($_config['o_topic_views'] == '1'): ?>					<td class="tc3"><?php echo (!$ghost_topic) ? forum_number_format($cur_topic['num_views']) : '&nbsp;' ?></td>
<?php endif; ?>					<td class="tcr"><?php echo $last_post ?></td>
					<td class="tcmod"><input type="checkbox" name="topics[<?php echo $cur_topic['id'] ?>]" value="1" /></td>
				</tr>
<?php }
    }else {
        $colspan = ($_config['o_topic_views'] == '1') ? 5 : 4;
        $button_status = ' disabled="disabled"';
        echo "\t\t\t\t\t" . '<tr><td class="tcl" colspan="' . $colspan . '">' . $lang_forum['Empty forum'] . '</td></tr>' . "\n";
    }
    ?>
			</tbody>
			</table>
		</div>
	</div>
</div><div class="linksb">
	<div class="inbox">
		<p class="pagelink conl"><?php echo $paging_links ?></p>
		<p class="conr modbuttons"><input type="submit" name="move_topics" value="<?php echo $lang_misc['Move'] ?>"<?php echo $button_status ?> /> <input type="submit" name="delete_topics" value="<?php echo $lang_misc['Delete'] ?>"<?php echo $button_status ?> /> <input type="submit" name="merge_topics" value="<?php echo $lang_misc['Merge'] ?>"<?php echo $button_status ?> /> <input type="submit" name="open" value="<?php echo $lang_misc['Open'] ?>"<?php echo $button_status ?> /> <input type="submit" name="close" value="<?php echo $lang_misc['Close'] ?>"<?php echo $button_status ?> /></p>
		<div class="clearer"></div>
	</div>
</div>
</form>
<?php require SHELL_PATH . 'footer.php';