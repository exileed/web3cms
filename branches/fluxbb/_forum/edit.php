<?php
require SHELL_PATH . 'include/common.php';
if ($_user['g_read_board'] == '0')
    message($lang_common['No view']);
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 1)
    message($lang_common['Bad request']);
// Fetch some info about the post, the topic and the forum
$db->setQuery('SELECT f.id AS fid, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics, t.id AS tid, t.subject, t.posted, t.first_post_id, t.closed, p.poster, p.poster_id, p.message, p.hide_smilies FROM forum_posts AS p INNER JOIN forum_topics AS t ON t.id=p.topic_id INNER JOIN forum_forums AS f ON f.id=t.forum_id LEFT JOIN forum_forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=' . $_user['g_id'] . ') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND p.id=' . $id) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows())
    message($lang_common['Bad request']);
$cur_post = $db->fetch_assoc();
// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_post['moderators'] != '') ? unserialize($cur_post['moderators']) : array();
$is_admmod = ($_user['g_id'] == PUN_ADMIN || ($_user['g_moderator'] == '1' && array_key_exists($_user['username'], $mods_array))) ? true : false;
$can_edit_subject = $id == $cur_post['first_post_id'];
// Do we have permission to edit this post?
if (($_user['g_edit_posts'] == '0' || $cur_post['poster_id'] != $_user['id'] || $cur_post['closed'] == '1') && !$is_admmod)
    message($lang_common['No permission']);
// Load the post.php/edit.php language file
require SHELL_PATH . 'lang/' . $_user['language'] . '/post.php';
// Start with a clean slate
$errors = array();
if (isset($_POST['form_sent'])) {
    if ($is_admmod)
        confirm_referrer('edit.php');
    // If it's a topic it must contain a subject
    if ($can_edit_subject) {
        $subject = _trim($_POST['req_subject']);
        if ($subject == '')
            $errors[] = $lang_post['No subject'];
        else if (_strlen($subject) > 70)
            $errors[] = $lang_post['Too long subject'];
        else if ($_config['p_subject_all_caps'] == '0' && is_all_uppercase($subject) && !$_user['is_admmod'])
            $errors[] = $lang_post['All caps subject'];
    }
    // Clean up message from POST
    $message = _linebreaks(_trim($_POST['req_message']));
    if ($message == '')
        $errors[] = $lang_post['No message'];
    else if (strlen($message) > 65535)
        $errors[] = $lang_post['Too long message'];
    else if ($_config['p_message_all_caps'] == '0' && is_all_uppercase($message) && !$_user['is_admmod'])
        $errors[] = $lang_post['All caps message'];
    // Validate BBCode syntax
    if ($_config['p_message_bbcode'] == '1') {
        require SHELL_PATH . 'include/parser.php';
        $message = preparse_bbcode($message, $errors);
    }
    $hide_smilies = isset($_POST['hide_smilies']) ? '1' : '0';
    // Did everything go according to plan?
    if (empty($errors) && !isset($_POST['preview'])) {
        $edited_sql = (!isset($_POST['silent']) || !$is_admmod) ? $edited_sql = ', edited=' . time() . ', edited_by=\'' . $db->escape($_user['username']) . '\'' : '';
        require SHELL_PATH . 'include/search_idx.php';
        if ($can_edit_subject) {
            // Update the topic and any redirect topics
            $db->setQuery('UPDATE forum_topics SET subject=\'' . $db->escape($subject) . '\' WHERE id=' . $cur_post['tid'] . ' OR moved_to=' . $cur_post['tid'])->execute() or error('Unable to update topic', __FILE__, __LINE__, $db->error());
            // We changed the subject, so we need to take that into account when we update the search words
            update_search_index('edit', $id, $message, $subject);
        }else
            update_search_index('edit', $id, $message);
        // Update the post
        $db->setQuery('UPDATE forum_posts SET message=\'' . $db->escape($message) . '\', hide_smilies=' . $hide_smilies . $edited_sql . ' WHERE id=' . $id)->execute() or error('Unable to update post', __FILE__, __LINE__, $db->error());
       	Yii::app()->request->redirect(Yii::app()->createUrl('forum/viewtopic', array('pid' => $id . '#p' . $id)));
    }
}
$required_fields = array('req_subject' => $lang_common['Subject'], 'req_message' => $lang_common['Message']);
$focus_element = array('edit', 'req_message');
require SHELL_PATH . 'header.php';
$cur_index = 1;
?>
<div class="linkst">
	<div class="inbox">
		<ul class="crumbs">
			<li><?php echo _CHtml::link($lang_common['Index'], array('forum/'));?></li>
			<li>&raquo;&nbsp;<?php
echo _CHtml::link(_CHtml::encode($cur_post['forum_name']), array('forum/viewforum', 'id' => $cur_post['fid']));

?></li>
			<li>&raquo;&nbsp;<?php echo _CHtml::encode($cur_post['subject']) ?></li>
		</ul>
	</div>
</div><?php
// If there are errors, we display them
if (!empty($errors)) {?>
<div id="posterror" class="block">
	<h2><span><?php echo $lang_post['Post errors'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<p><?php echo $lang_post['Post errors info'] ?></p>
			<ul>
<?php while (list(, $cur_error) = each($errors))
    echo "\t\t\t\t" . '<li><strong>' . $cur_error . '</strong></li>' . "\n";
    ?>
			</ul>
		</div>
	</div>
</div><?php }else if (isset($_POST['preview'])) {
    require_once SHELL_PATH . 'include/parser.php';
    $preview_message = parse_message($message, $hide_smilies);
    ?>
<div id="postpreview" class="blockpost">
	<h2><span><?php echo $lang_post['Post preview'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<div class="postbody">
				<div class="postright">
					<div class="postmsg">
						<?php echo $preview_message . "\n" ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><?php } ?>
<div class="blockform">
	<h2><span><?php echo $lang_post['Edit post'] ?></span></h2>
	<div class="box">
		<?php echo _CHtml::form(array('edit', 'id' => $id, 'action' => 'edit'), 'POST', array('id' => 'edit', 'onsubmit' => 'return process_form(this);'));?>
			<div class="inform">
				<fieldset>

					<legend><?php echo $lang_post['Edit post legend'] ?></legend>
					<input type="hidden" name="form_sent" value="1" />
					<div class="infldset txtarea">
<?php if ($can_edit_subject): ?>						<label><?php echo $lang_common['Subject'] ?><br />
						<input class="longinput" type="text" name="req_subject" size="80" maxlength="70" tabindex="<?php echo $cur_index++ ?>" value="<?php echo _CHtml::encode(isset($_POST['req_subject']) ? $_POST['req_subject'] : $cur_post['subject']) ?>" /><br /></label>
<?php endif; ?>						<label><?php echo $lang_common['Message'] ?><br />
						<textarea name="req_message" rows="20" cols="95" tabindex="<?php echo $cur_index++ ?>"><?php echo _CHtml::encode(isset($_POST['req_message']) ? $message : $cur_post['message']) ?></textarea><br /></label>
						<ul class="bblinks">
							<li>
							<?php echo _CHtml::link($lang_common['BBCode'], array('forum/help#bbcode'), array('onclick' => 'window.open(this.href); return false;'));?>: <?php echo ($_config['p_message_bbcode'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
							<li>
							<?php echo _CHtml::link($lang_common['img tag'], array('forum/help#img'), array('onclick' => 'window.open(this.href); return false;'));?>: <?php echo ($_config['p_message_img_tag'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
							<li><?php echo _CHtml::link($lang_common['Smilies'], array('forum/help#smilies'), array('onclick' => 'window.open(this.href); return false;'));?>: <?php echo ($_config['o_smilies'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
						</ul>
					</div>
				</fieldset>
<?php $checkboxes = array();
if ($_config['o_smilies'] == '1') {
    if (isset($_POST['hide_smilies']) || $cur_post['hide_smilies'] == '1')
        $checkboxes[] = '<label><input type="checkbox" name="hide_smilies" value="1" checked="checked" tabindex="' . ($cur_index++) . '" />&nbsp;' . $lang_post['Hide smilies'];
    else
        $checkboxes[] = '<label><input type="checkbox" name="hide_smilies" value="1" tabindex="' . ($cur_index++) . '" />&nbsp;' . $lang_post['Hide smilies'];
}
if ($is_admmod) {
    if ((isset($_POST['form_sent']) && isset($_POST['silent'])) || !isset($_POST['form_sent']))
        $checkboxes[] = '<label><input type="checkbox" name="silent" value="1" tabindex="' . ($cur_index++) . '" checked="checked" />&nbsp;' . $lang_post['Silent edit'];
    else
        $checkboxes[] = '<label><input type="checkbox" name="silent" value="1" tabindex="' . ($cur_index++) . '" />&nbsp;' . $lang_post['Silent edit'];
}
if (!empty($checkboxes)) {?>
			</div>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_common['Options'] ?></legend>
					<div class="infldset">
						<div class="rbox">
							<?php echo implode('</label>' . "\n\t\t\t\t\t\t\t", $checkboxes) . '</label>' . "\n" ?>
						</div>
					</div>
				</fieldset>
<?php } ?>
			</div>
			<p class="buttons"><input type="submit" name="submit" value="<?php echo $lang_common['Submit'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="s" /> <input type="submit" name="preview" value="<?php echo $lang_post['Preview'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="p" />
			<?php echo _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></p>
		</form>
	</div>
</div>
<?php require SHELL_PATH . 'footer.php';