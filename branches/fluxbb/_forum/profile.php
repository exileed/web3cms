<?php
require SHELL_PATH . 'include/common.php';$action = isset($_GET['action']) ? $_GET['action'] : null;
$section = isset($_GET['section']) ? $_GET['section'] : null;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 2)
    message($lang_common['Bad request']);if ($action != 'change_pass' || !isset($_GET['key']))
{
    if ($_user['g_read_board'] == '0')
        message($lang_common['No view']);
    else if ($_user['g_view_users'] == '0' && ($_user['is_guest'] || $_user['id'] != $id))
        message($lang_common['No permission']);
}
// Load the profile.php/register.php language file
require SHELL_PATH . 'lang/' . $_user['language'] . '/prof_reg.php';
// Load the profile.php language file
require SHELL_PATH . 'lang/' . $_user['language'] . '/profile.php';if ($action == 'change_pass')
{
    if (isset($_GET['key']))
    {
        // If the user is already logged in we shouldn't be here :)
        if (!$_user['is_guest'])
        {
            Yii::app()->request->redirect(Yii::app()->createUrl('forum/'));
        }        $key = $_GET['key'];        $db->setQuery('SELECT activate_string, activate_key FROM forum_userprofiles WHERE id=' . $id) or error('Unable to fetch new password', __FILE__, __LINE__, $db->error());
        list($new_password_hash, $new_password_key) = $db->fetch_row();        if ($key == '' || $key != $new_password_key)
            message($lang_profile['Pass key bad'] . ' ' . _CHtml::link($_config['o_admin_email'], 'mailto:' . $_config['o_admin_email']));
        else
        {
            $db->setQuery('UPDATE forum_userprofiles SET password=\'' . $new_password_hash . '\', activate_string=NULL, activate_key=NULL WHERE id=' . $id)->execute() or error('Unable to update password', __FILE__, __LINE__, $db->error());            message($lang_profile['Pass updated'], true);
        }
    }
    // Make sure we are allowed to change this users password
    if ($_user['id'] != $id)
    {
        if (!$_user['is_admmod']) // A regular user trying to change another users password?
            message($lang_common['No permission']);
        else if ($_user['g_moderator'] == '1') // A moderator trying to change a users password?
            {
                $db->setQuery('SELECT u.group_id, g.g_moderator FROM forum_userprofiles AS u INNER JOIN forum_groups AS g ON (g.g_id=u.group_id) WHERE u.id=' . $id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
            if (!$db->num_rows())
                message($lang_common['Bad request']);            list($group_id, $is_moderator) = $db->fetch_row();            if ($_user['g_mod_edit_users'] == '0' || $_user['g_mod_change_passwords'] == '0' || $group_id == PUN_ADMIN || $is_moderator == '1')
                message($lang_common['No permission']);
        }
    }    if (isset($_POST['form_sent']))
    {
        if ($_user['is_admmod'])
            confirm_referrer('profile.php');        $old_password = isset($_POST['req_old_password']) ? trim($_POST['req_old_password']) : '';
        $new_password1 = trim($_POST['req_new_password1']);
        $new_password2 = trim($_POST['req_new_password2']);        if ($new_password1 != $new_password2)
            message($lang_prof_reg['Pass not match']);
        if (strlen($new_password1) < 4)
            message($lang_prof_reg['Pass too short']);        $db->setQuery('SELECT password FROM forum_userprofiles WHERE id=' . $id) or error('Unable to fetch password', __FILE__, __LINE__, $db->error());
        list($db_password_hash) = $db->fetch_row();        $authorized = false;        if (!empty($db_password_hash))
        {
            $sha1_in_db = (strlen($db_password_hash) == 40) ? true : false;
            $sha1_available = (function_exists('sha1') || function_exists('mhash')) ? true : false;            $old_password_hash = _hash($old_password); // This could result in either an SHA-1 or an MD5 hash
                if (($sha1_in_db && $sha1_available && $db_password_hash == $old_password_hash) ||
                    (!$sha1_in_db && $db_password_hash == md5($old_password)) || $_user['is_admmod'])
                $authorized = true;
        }        if (!$authorized)
            message($lang_profile['Wrong pass']);        $new_password_hash = _hash($new_password1);        $db->setQuery('UPDATE forum_userprofiles SET password=\'' . $new_password_hash . '\' WHERE id=' . $id)->execute() or error('Unable to update password', __FILE__, __LINE__, $db->error());        if ($_user['id'] == $id)
        {
            _setcookie($_user['id'], $new_password_hash, time() + $_config['o_timeout_visit']);
        }        redirect('profile.php?section=essentials&amp;id=' . $id, $lang_profile['Pass updated redirect']);
    }    $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_common['Profile'];
    $required_fields = array('req_old_password' => $lang_profile['Old pass'], 'req_new_password1' => $lang_profile['New pass'], 'req_new_password2' => $lang_profile['Confirm new pass']);
    $focus_element = array('change_pass', ((!$_user['is_admmod']) ? 'req_old_password' : 'req_new_password1'));
    require SHELL_PATH . 'header.php';    ?>
<div class="blockform">
	<h2><span><?php echo $lang_profile['Change pass'] ?></span></h2>
	<div class="box">
		<?php echo _CHtml::form(array('profile','action'=>'change_pass','id'=>$id), 'POST', array('id'=>'change_pass','onsubmit'=>'return process_form(this)'));?>
			<div class="inform">
				<input type="hidden" name="form_sent" value="1" />
				<fieldset>
					<legend><?php echo $lang_profile['Change pass legend'] ?></legend>
					<div class="infldset">
<?php if (!$_user['is_admmod']): ?>						<label><strong><?php echo $lang_profile['Old pass'] ?></strong><br />
						<input type="password" name="req_old_password" size="16" maxlength="16" /><br /></label>
<?php endif; ?>						<label class="conl"><strong><?php echo $lang_profile['New pass'] ?></strong><br />
						<input type="password" name="req_new_password1" size="16" maxlength="16" /><br /></label>
						<label class="conl"><strong><?php echo $lang_profile['Confirm new pass'] ?></strong><br />
						<input type="password" name="req_new_password2" size="16" maxlength="16" /><br /></label>
						<div class="clearb"></div>
					</div>
				</fieldset>
			</div>
			<p class="buttons"><input type="submit" name="update" value="<?php echo $lang_common['Submit'] . '" /> ' . _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></a></p>
		</form>
	</div>
</div>
<?php    require SHELL_PATH . 'footer.php';
}else if ($action == 'change_email')
{
    // Make sure we are allowed to change this users email
    if ($_user['id'] != $id)
    {
        if (!$_user['is_admmod']) // A regular user trying to change another users email?
            message($lang_common['No permission']);
        else if ($_user['g_moderator'] == '1') // A moderator trying to change a users email?
            {
                $db->setQuery('SELECT u.group_id, g.g_moderator FROM forum_userprofiles AS u INNER JOIN forum_groups AS g ON (g.g_id=u.group_id) WHERE u.id=' . $id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
            if (!$db->num_rows())
                message($lang_common['Bad request']);            list($group_id, $is_moderator) = $db->fetch_row();            if ($_user['g_mod_edit_users'] == '0' || $group_id == PUN_ADMIN || $is_moderator == '1')
                message($lang_common['No permission']);
        }
    }    if (isset($_GET['key']))
    {
        $key = $_GET['key'];        $db->setQuery('SELECT activate_string, activate_key FROM forum_userprofiles WHERE id=' . $id) or error('Unable to fetch activation data', __FILE__, __LINE__, $db->error());
        list($new_email, $new_email_key) = $db->fetch_row();        if ($key == '' || $key != $new_email_key)
            message($lang_profile['Email key bad'] . ' ' . _CHtml::link($_config['o_admin_email'], 'mailto:' . $_config['o_admin_email']));
        else
        {
            $db->setQuery('UPDATE forum_userprofiles SET email=activate_string, activate_string=NULL, activate_key=NULL WHERE id=' . $id)->execute() or error('Unable to update email address', __FILE__, __LINE__, $db->error());            message($lang_profile['Email updated'], true);
        }
    }
    else if (isset($_POST['form_sent']))
    {
        if (_hash($_POST['req_password']) !== $_user['password'])
            message($lang_profile['Wrong pass']);        require SHELL_PATH . 'include/email.php';
        // Validate the email address
        $new_email = strtolower(trim($_POST['req_new_email']));
        if (!is_valid_email($new_email))
            message($lang_common['Invalid email']);
        // Check if it's a banned email address
        if (is_banned_email($new_email))
        {
            if ($_config['p_allow_banned_email'] == '0')
                message($lang_prof_reg['Banned email']);
            else if ($_config['o_mailing_list'] != '')
            {
                $mail_subject = $lang_common['Banned email notification'];
                $mail_message = sprintf($lang_common['Banned email change message'], $_user['username'], $new_email) . "\n";
                $mail_message .= sprintf($lang_common['User profile'], $_config['o_web_path'] . '/profile.php?id=' . $id) . "\n";
                $mail_message .= "\n" . '--' . "\n" . $lang_common['Email signature'];                _mail($_config['o_mailing_list'], $mail_subject, $mail_message);
            }
        }
        // Check if someone else already has registered with that email address
        $db->setQuery('SELECT id, username FROM forum_userprofiles WHERE email=\'' . $db->escape($new_email) . '\'') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
        if ($db->num_rows())
        {
            if ($_config['p_allow_dupe_email'] == '0')
                message($lang_prof_reg['Dupe email']);
            else if ($_config['o_mailing_list'] != '')
            {
                while ($cur_dupe = $db->fetch_assoc())
                $dupe_list[] = $cur_dupe['username'];                $mail_subject = $lang_common['Duplicate email notification'];
                $mail_message = sprintf($lang_common['Duplicate email change message'], $_user['username'], implode(', ', $dupe_list)) . "\n";
                $mail_message .= sprintf($lang_common['User profile'], $_config['o_web_path'] . '/profile.php?id=' . $id) . "\n";
                $mail_message .= "\n" . '--' . "\n" . $lang_common['Email signature'];                _mail($_config['o_mailing_list'], $mail_subject, $mail_message);
            }
        }        $new_email_key = random_pass(8);        $db->setQuery('UPDATE forum_userprofiles SET activate_string=\'' . $db->escape($new_email) . '\', activate_key=\'' . $new_email_key . '\' WHERE id=' . $id)->execute() or error('Unable to update activation data', __FILE__, __LINE__, $db->error());
        // Load the "activate email" template
        $mail_tpl = trim(file_get_contents(SHELL_PATH . 'lang/' . $_user['language'] . '/mail_templates/activate_email.tpl'));
        // The first row contains the subject
        $first_crlf = strpos($mail_tpl, "\n");
        $mail_subject = trim(substr($mail_tpl, 8, $first_crlf - 8));
        $mail_message = trim(substr($mail_tpl, $first_crlf));        $mail_message = str_replace('<username>', $_user['username'], $mail_message);
        $mail_message = str_replace('<WEB_PATH>', $_config['o_web_path'], $mail_message);
        $mail_message = str_replace('<activation_url>', $_config['o_web_path'] . '/profile.php?action=change_email&id=' . $id . '&key=' . $new_email_key, $mail_message);
        $mail_message = str_replace('<board_mailer>', $this->PageTitle . ' ' . $lang_common['Mailer'], $mail_message);        _mail($new_email, $mail_subject, $mail_message);        message($lang_profile['Activate email sent'] . ' ' . _CHtml::link($_config['o_admin_email'], 'mailto:' . $_config['o_admin_email']), true);
    }    $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_common['Profile'];
    $required_fields = array('req_new_email' => $lang_profile['New email'], 'req_password' => $lang_common['Password']);
    $focus_element = array('change_email', 'req_new_email');
    require SHELL_PATH . 'header.php';    ?>
<div class="blockform">
	<h2><span><?php echo $lang_profile['Change email'] ?></span></h2>
	<div class="box">
		<?php echo _CHtml::form(array('profile','action'=>'change_email','id'=>$id), 'POST', array('id'=>'change_email','onsubmit'=>'return process_form(this)'));?>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_profile['Email legend'] ?></legend>
					<div class="infldset">
						<input type="hidden" name="form_sent" value="1" />
						<label><strong><?php echo $lang_profile['New email'] ?></strong><br /><input type="text" name="req_new_email" size="50" maxlength="50" /><br /></label>
						<label><strong><?php echo $lang_common['Password'] ?></strong><br /><input type="password" name="req_password" size="16" maxlength="16" /><br /></label>
						<p><?php echo $lang_profile['Email instructions'] ?></p>
					</div>
				</fieldset>
			</div>
			<p class="buttons"><input type="submit" name="new_email" value="<?php echo $lang_common['Submit'] . '" /> ' . _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></p>
		</form>
	</div>
</div>
<?php    require SHELL_PATH . 'footer.php';
}else if ($action == 'upload_avatar' || $action == 'upload_avatar2')
{
    if ($_config['o_avatars'] == '0')
        message($lang_profile['Avatars disabled']);    if ($_user['id'] != $id && !$_user['is_admmod'])
        message($lang_common['No permission']);    if (isset($_POST['form_sent']))
    {
        if (!isset($_FILES['req_file']))
            message($lang_profile['No file']);        $uploaded_file = $_FILES['req_file'];
        // Make sure the upload went smooth
        if (isset($uploaded_file['error']))
        {
            switch ($uploaded_file['error'])
            {
                case 1: // UPLOAD_ERR_INI_SIZE
                case 2: // UPLOAD_ERR_FORM_SIZE
                    message($lang_profile['Too large ini']);
                    break;                case 3: // UPLOAD_ERR_PARTIAL
                    message($lang_profile['Partial upload']);
                    break;                case 4: // UPLOAD_ERR_NO_FILE
                    message($lang_profile['No file']);
                    break;                case 6: // UPLOAD_ERR_NO_TMP_DIR
                    message($lang_profile['No tmp directory']);
                    break;                default:
                    // No error occured, but was something actually uploaded?
                    if ($uploaded_file['size'] == 0)
                        message($lang_profile['No file']);
                    break;
            }
        }        if (is_uploaded_file($uploaded_file['tmp_name']))
        {
            // Preliminary file check, adequate in most cases
            $allowed_types = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
            if (!in_array($uploaded_file['type'], $allowed_types))
                message($lang_profile['Bad type']);
            // Make sure the file isn't too big
            if ($uploaded_file['size'] > $_config['o_avatars_size'])
                message($lang_profile['Too large'] . ' ' . forum_number_format($_config['o_avatars_size']) . ' ' . $lang_profile['bytes'] . '.');
            // Move the file to the avatar directory. We do this before checking the width/height to circumvent open_basedir restrictions
            if (!@move_uploaded_file($uploaded_file['tmp_name'], $_config['o_avatars_dir'] . '/' . $id . '.tmp'))
                message($lang_profile['Move failed'] . ' ' . _CHtml::link($_config['o_admin_email'], 'mailto:' . $_config['o_admin_email']));            list($width, $height, $type,) = @getimagesize($_config['o_avatars_dir'] . '/' . $id . '.tmp');
            // Determine type
            $extension = null;
            if ($type == IMAGETYPE_GIF)
                $extension = '.gif';
            else if ($type == IMAGETYPE_JPEG)
                $extension = '.jpg';
            else if ($type == IMAGETYPE_PNG)
                $extension = '.png';
            else
            {
                // Invalid type
                @unlink($_config['o_avatars_dir'] . '/' . $id . '.tmp');
                message($lang_profile['Bad type']);
            }
            // Now check the width/height
            if (empty($width) || empty($height) || $width > $_config['o_avatars_width'] || $height > $_config['o_avatars_height'])
            {
                @unlink($_config['o_avatars_dir'] . '/' . $id . '.tmp');
                message($lang_profile['Too wide or high'] . ' ' . $_config['o_avatars_width'] . 'x' . $_config['o_avatars_height'] . ' ' . $lang_profile['pixels'] . '.');
            }
            // Delete any old avatars and put the new one in place
            delete_avatar($id);
            @rename($_config['o_avatars_dir'] . '/' . $id . '.tmp', $_config['o_avatars_dir'] . '/' . $id . $extension);
            @chmod($_config['o_avatars_dir'] . '/' . $id . $extension, 0644);
        }
        else
            message($lang_profile['Unknown failure']);        redirect('profile.php?section=personality&amp;id=' . $id, $lang_profile['Avatar upload redirect']);
    }    $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_common['Profile'];
    $required_fields = array('req_file' => $lang_profile['File']);
    $focus_element = array('upload_avatar', 'req_file');
    require SHELL_PATH . 'header.php';    ?>
<div class="blockform">
	<h2><span><?php echo $lang_profile['Upload avatar'] ?></span></h2>
	<div class="box">
		<?php echo _CHtml::form(array('profile','action'=>'upload_avatar2','id'=>$id), 'POST', array('id'=>'upload_avatar','enctype'=>'multipart/form-data','onsubmit'=>'return process_form(this)'));?>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_profile['Upload avatar legend'] ?></legend>
					<div class="infldset">
						<input type="hidden" name="form_sent" value="1" />
						<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $_config['o_avatars_size'] ?>" />
						<label><strong><?php echo $lang_profile['File'] ?></strong><br /><input name="req_file" type="file" size="40" /><br /></label>
						<p><?php echo $lang_profile['Avatar desc'] . ' ' . $_config['o_avatars_width'] . ' x ' . $_config['o_avatars_height'] . ' ' . $lang_profile['pixels'] . ' ' . $lang_common['and'] . ' ' . forum_number_format($_config['o_avatars_size']) . ' ' . $lang_profile['bytes'] . ' (' . forum_number_format(ceil($_config['o_avatars_size'] / 1024)) ?> KB).</p>
					</div>
				</fieldset>
			</div>
			<p class="buttons"><input type="submit" name="upload" value="<?php echo $lang_profile['Upload'] . '" /> ' . _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></p>
		</form>
	</div>
</div>
<?php    require SHELL_PATH . 'footer.php';
}else if ($action == 'delete_avatar')
{
    if ($_user['id'] != $id && !$_user['is_admmod'])
        message($lang_common['No permission']);    confirm_referrer('profile.php');    delete_avatar($id);    redirect('profile.php?section=personality&amp;id=' . $id, $lang_profile['Avatar deleted redirect']);
}else if (isset($_POST['update_group_membership']))
{
    if ($_user['g_id'] > PUN_ADMIN)
        message($lang_common['No permission']);    confirm_referrer('profile.php');    $new_group_id = intval($_POST['group_id']);    $db->setQuery('UPDATE forum_userprofiles SET group_id=' . $new_group_id . ' WHERE id=' . $id)->execute() or error('Unable to change user group', __FILE__, __LINE__, $db->error());    $db->setQuery('SELECT g_moderator FROM forum_groups WHERE g_id=' . $new_group_id) or error('Unable to fetch group', __FILE__, __LINE__, $db->error());
    $new_group_mod = $db->result();
    // If the user was a moderator or an administrator, we remove him/her from the moderator list in all forums as well
    if ($new_group_id != PUN_ADMIN && $new_group_mod != '1')
    {
        $db->setQuery('SELECT id, moderators FROM forum_forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());        while ($cur_forum = $db->fetch_assoc())
        {
            $cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();            if (in_array($id, $cur_moderators))
            {
                $username = array_search($id, $cur_moderators);
                unset($cur_moderators[$username]);
                $cur_moderators = (!empty($cur_moderators)) ? '\'' . $db->escape(serialize($cur_moderators)) . '\'' : 'NULL';                $db->setQuery('UPDATE forum_forums SET moderators=' . $cur_moderators . ' WHERE id=' . $cur_forum['id'])->execute() or error('Unable to update forum', __FILE__, __LINE__, $db->error());
            }
        }
    }    redirect('profile.php?section=admin&amp;id=' . $id, $lang_profile['Group membership redirect']);
}else if (isset($_POST['update_forums']))
{
    if ($_user['g_id'] > PUN_ADMIN)
        message($lang_common['No permission']);    confirm_referrer('profile.php');
    // Get the username of the user we are processing
    $db->setQuery('SELECT username FROM forum_userprofiles WHERE id=' . $id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
    $username = $db->result();    $moderator_in = (isset($_POST['moderator_in'])) ? array_keys($_POST['moderator_in']) : array();
    // Loop through all forums
    $db->setQuery('SELECT id, moderators FROM forum_forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());    while ($cur_forum = $db->fetch_assoc())
    {
        $cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();
        // If the user should have moderator access (and he/she doesn't already have it)
        if (in_array($cur_forum['id'], $moderator_in) && !in_array($id, $cur_moderators))
        {
            $cur_moderators[$username] = $id;
            ksort($cur_moderators);            $db->setQuery('UPDATE forum_forums SET moderators=\'' . $db->escape(serialize($cur_moderators)) . '\' WHERE id=' . $cur_forum['id'])->execute() or error('Unable to update forum', __FILE__, __LINE__, $db->error());
        }
        // If the user shouldn't have moderator access (and he/she already has it)
        else if (!in_array($cur_forum['id'], $moderator_in) && in_array($id, $cur_moderators))
        {
            unset($cur_moderators[$username]);
            $cur_moderators = (!empty($cur_moderators)) ? '\'' . $db->escape(serialize($cur_moderators)) . '\'' : 'NULL';            $db->setQuery('UPDATE forum_forums SET moderators=' . $cur_moderators . ' WHERE id=' . $cur_forum['id'])->execute() or error('Unable to update forum', __FILE__, __LINE__, $db->error());
        }
    }    redirect('profile.php?section=admin&amp;id=' . $id, $lang_profile['Update forums redirect']);
}else if (isset($_POST['ban']))
{
    if ($_user['g_id'] != PUN_ADMIN && ($_user['g_moderator'] != '1' || $_user['g_mod_ban_users'] == '0'))
        message($lang_common['No permission']);    redirect('admin_bans.php?add_ban=' . $id, $lang_profile['Ban redirect']);
}else if (isset($_POST['delete_user']) || isset($_POST['delete_user_comply']))
{
    if ($_user['g_id'] > PUN_ADMIN)
        message($lang_common['No permission']);    confirm_referrer('profile.php');
    // Get the username and group of the user we are deleting
    $db->setQuery('SELECT group_id, username FROM forum_userprofiles WHERE id=' . $id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
    list($group_id, $username) = $db->fetch_row();    if ($group_id == PUN_ADMIN)
        message('Administrators cannot be deleted. In order to delete this user, you must first move him/her to a different user group.');    if (isset($_POST['delete_user_comply']))
    {
        // If the user is a moderator or an administrator, we remove him/her from the moderator list in all forums as well
        $db->setQuery('SELECT g_moderator FROM forum_groups WHERE g_id=' . $group_id) or error('Unable to fetch group', __FILE__, __LINE__, $db->error());
        $group_mod = $db->result();        if ($group_id == PUN_ADMIN || $group_mod == '1')
        {
            $db->setQuery('SELECT id, moderators FROM forum_forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());            while ($cur_forum = $db->fetch_assoc())
            {
                $cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();                if (in_array($id, $cur_moderators))
                {
                    unset($cur_moderators[$username]);
                    $cur_moderators = (!empty($cur_moderators)) ? '\'' . $db->escape(serialize($cur_moderators)) . '\'' : 'NULL';
$db->setQuery('UPDATE forum_forums SET moderators=' . $cur_moderators . ' WHERE id=' . $cur_forum['id'])->execute() or error('Unable to update forum', __FILE__, __LINE__, $db->error());
                }
            }
        }
        // Delete any subscriptions
        $db->setQuery('DELETE FROM forum_subscriptions WHERE user_id=' . $id)->execute() or error('Unable to delete subscriptions', __FILE__, __LINE__, $db->error());
        // Remove him/her from the online list (if they happen to be logged in)
        $db->setQuery('DELETE FROM forum_online WHERE user_id=' . $id)->execute() or error('Unable to remove user from online list', __FILE__, __LINE__, $db->error());
        // Should we delete all posts made by this user?
        if (isset($_POST['delete_posts']))
        {
            require SHELL_PATH . 'include/search_idx.php';
            @set_time_limit(0);
            // Find all posts made by this user
            $db->setQuery('SELECT p.id, p.topic_id, t.forum_id FROM forum_posts AS p INNER JOIN forum_topics AS t ON t.id=p.topic_id INNER JOIN forum_forums AS f ON f.id=t.forum_id WHERE p.poster_id=' . $id) or error('Unable to fetch posts', __FILE__, __LINE__, $db->error());
            if ($db->num_rows())
            {
                while ($cur_post = $db->fetch_assoc())
                {
                    // Determine whether this post is the "topic post" or not
                    $db->setQuery('SELECT id FROM forum_posts WHERE topic_id=' . $cur_post['topic_id'] . ' ORDER BY posted LIMIT 1') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
if ($db->result() == $cur_post['id'])
                        delete_topic($cur_post['topic_id']);
                    else
                        delete_post($cur_post['id'], $cur_post['topic_id']);
update_forum($cur_post['forum_id']);
                }
            }
        }
        else
            // Set all his/her posts to guest
            $db->setQuery('UPDATE forum_posts SET poster_id=1 WHERE poster_id=' . $id)->execute() or error('Unable to update posts', __FILE__, __LINE__, $db->error());
        // Delete the user
        $db->setQuery('DELETE FROM forum_userprofiles WHERE id=' . $id)->execute() or error('Unable to delete user', __FILE__, __LINE__, $db->error());
        // Delete user avatar
        delete_avatar($id);        redirect('index.php', $lang_profile['User delete redirect']);
    }    $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_common['Profile'];
    require SHELL_PATH . 'header.php';    ?>
<div class="blockform">
	<h2><span><?php echo $lang_profile['Confirm delete user'] ?></span></h2>
	<div class="box">
		<?php echo _CHtml::form(array('profile','id'=>$id), 'POST', array('id'=>'confirm_del_user'));?>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_profile['Confirm delete legend'] ?></legend>
					<div class="infldset">
						<p><?php echo $lang_profile['Confirmation info'] . ' ' . _CHtml::encode($username) . '.' ?></p>
						<div class="rbox">
							<label><input type="checkbox" name="delete_posts" value="1" checked="checked" /><?php echo $lang_profile['Delete posts'] ?><br /></label>
						</div>
						<p class="warntext"><strong><?php echo $lang_profile['Delete warning'] ?></strong></p>
					</div>
				</fieldset>
			</div>
			<p class="buttons"><input type="submit" name="delete_user_comply" value="<?php echo $lang_profile['Delete'] . '" /> ' . _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></a></p>
		</form>
	</div>
</div>
<?php    require SHELL_PATH . 'footer.php';
}else if (isset($_POST['form_sent']))
{
    // Fetch the user group of the user we are editing
    $db->setQuery('SELECT u.group_id, g.g_moderator FROM forum_userprofiles AS u INNER JOIN forum_groups AS g ON (g.g_id=u.group_id) WHERE u.id=' . $id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
    if (!$db->num_rows())
        message($lang_common['Bad request']);    list($group_id, $is_moderator) = $db->fetch_row();    if ($_user['id'] != $id &&
        (!$_user['is_admmod'] ||
            ($_user['g_moderator'] == '1' && $_user['g_mod_edit_users'] == '0') ||
                ($_user['g_moderator'] == '1' && $is_moderator)))
        message($lang_common['No permission']);    if ($_user['is_admmod'])
        confirm_referrer('profile.php');
    // Extract allowed elements from $_POST['form']
    function extract_elements($allowed_elements)
    {
        $form = array();        while (list($key, $value) = @each($_POST['form']))
        {
            if (in_array($key, $allowed_elements))
                $form[$key] = $value;
        }        return $form;
    }    $username_updated = false;
    // Validate input depending on section
    switch ($section)
    {
        case 'essentials':
            {
                $form = extract_elements(array('timezone', 'dst'));                if ($_user['is_admmod'])
                {
                    $form['admin_note'] = trim($_POST['admin_note']);
                    // Are we allowed to change usernames?
                    if ($_user['g_id'] == PUN_ADMIN || ($_user['g_moderator'] == '1' && $_user['g_mod_rename_users'] == '1'))
                    {
                        $form['username'] = trim($_POST['req_username']);
                        $old_username = trim($_POST['old_username']);
    if (strlen($form['username']) < 2)
                            message($lang_prof_reg['Username too short']);
                        else if (_strlen($form['username']) > 25) // This usually doesn't happen since the form element only accepts 25 characters
                            message($lang_common['Bad request']);
                        else if (!strcasecmp($form['username'], 'Guest') || !strcasecmp($form['username'], $lang_common['Guest']))
                            message($lang_prof_reg['Username guest']);
                        else if (preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $form['username']) || preg_match('/((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))/', $form['username']))
                            message($lang_prof_reg['Username IP']);
                        else if (preg_match('/(?:\[\/?(?:b|u|i|h|colou?r|quote|code|img|url|email|list)\]|\[(?:code|quote|list)=)/i', $form['username']))
                            message($lang_prof_reg['Username BBCode']);
                        // Check that the username is not already registered
                        $db->setQuery('SELECT 1 FROM forum_userprofiles WHERE username=\'' . $db->escape($form['username']) . '\' AND id!=' . $id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
                        if ($db->num_rows())
                            message($lang_profile['Dupe username']);
    if ($form['username'] != $old_username)
                            $username_updated = true;
                    }
                    // We only allow administrators to update the post count
                    if ($_user['g_id'] == PUN_ADMIN)
                        $form['num_posts'] = intval($_POST['num_posts']);
                }                if ($_config['o_regs_verify'] == '0' || $_user['is_admmod'])
                {
                    require SHELL_PATH . 'include/email.php';
                    // Validate the email address
                    $form['email'] = strtolower(trim($_POST['req_email']));
                    if (!is_valid_email($form['email']))
                        message($lang_common['Invalid email']);
                }
                // Make sure we got a valid language string
                if (isset($form['language']))
                {
                    $form['language'] = preg_replace('#[\.\\\/]#', '', $form['language']);
                    if (!file_exists(SHELL_PATH . 'lang/' . $form['language'] . '/common.php'))
                        message($lang_common['Bad request']);
                }                $form['time_format'] = (isset($form['time_format'])) ? intval($form['time_format']) : 0;
                $form['date_format'] = (isset($form['date_format'])) ? intval($form['date_format']) : 0;                if (!isset($form['dst']) || $form['dst'] != '1') $form['dst'] = '0';                break;
            }        case 'personal':
            {
                $form = extract_elements(array('realname', 'url', 'location'));                if ($_user['g_id'] == PUN_ADMIN)
                    $form['title'] = trim($_POST['title']);
                else if ($_user['g_set_title'] == '1')
                {
                    $form['title'] = trim($_POST['title']);
if ($form['title'] != '')
                    {
                        // A list of words that the title may not contain
                        // If the language is English, there will be some duplicates, but it's not the end of the world
                        $forbidden = array('Member', 'Moderator', 'Administrator', 'Banned', 'Guest', $lang_common['Member'], $lang_common['Moderator'], $lang_common['Administrator'], $lang_common['Banned'], $lang_common['Guest']);
    if (in_array($form['title'], $forbidden))
                            message($lang_profile['Forbidden title']);
                    }
                }
                // Add http:// if the URL doesn't contain it already
                if ($form['url'] != '' && strpos(strtolower($form['url']), 'http://') !== 0)
                    $form['url'] = 'http://' . $form['url'];                break;
            }        case 'messaging':
            {
                $form = extract_elements(array('jabber', 'icq', 'msn', 'aim', 'yahoo'));
                // If the ICQ UIN contains anything other than digits it's invalid
                if ($form['icq'] != '' && @preg_match('/[^0-9]/', $form['icq']))
                    message($lang_prof_reg['Bad ICQ']);                break;
            }        case 'personality':
            {
                $form = array();
                // Clean up signature from POST
                if ($_config['o_signatures'] == '1')
                {
                    $form['signature'] = _linebreaks(trim($_POST['signature']));
                    // Validate signature
                    if (_strlen($form['signature']) > $_config['p_sig_length'])
                        message($lang_prof_reg['Sig too long'] . ' ' . $_config['p_sig_length'] . ' ' . $lang_prof_reg['characters'] . '.');
                    else if (substr_count($form['signature'], "\n") > ($_config['p_sig_lines'] - 1))
                        message($lang_prof_reg['Sig too many lines'] . ' ' . $_config['p_sig_lines'] . ' ' . $lang_prof_reg['lines'] . '.');
                    else if ($form['signature'] && $_config['p_sig_all_caps'] == '0' && is_all_uppercase($form['signature']) && !$_user['is_admmod'])
                        $form['signature'] = utf8_ucwords(utf8_strtolower($form['signature']));
                    // Validate BBCode syntax
                    if ($_config['p_sig_bbcode'] == '1')
                    {
                        require SHELL_PATH . 'include/parser.php';
    $errors = array();
    $form['signature'] = preparse_bbcode($form['signature'], $errors, true);
    if (count($errors) > 0)
                            message('<ul><li>' . implode('</li><li>', $errors) . '</li></ul>');
                    }
                }                break;
            }        case 'display':
            {
                $form = extract_elements(array('disp_topics', 'disp_posts', 'show_smilies', 'show_img', 'show_img_sig', 'show_avatars', 'show_sig', 'style'));                if ($form['disp_topics'] != '' && intval($form['disp_topics']) < 3) $form['disp_topics'] = 3;
                if ($form['disp_topics'] != '' && intval($form['disp_topics']) > 75) $form['disp_topics'] = 75;
                if ($form['disp_posts'] != '' && intval($form['disp_posts']) < 3) $form['disp_posts'] = 3;
                if ($form['disp_posts'] != '' && intval($form['disp_posts']) > 75) $form['disp_posts'] = 75;                if (!isset($form['show_smilies']) || $form['show_smilies'] != '1') $form['show_smilies'] = '0';
                if (!isset($form['show_img']) || $form['show_img'] != '1') $form['show_img'] = '0';
                if (!isset($form['show_img_sig']) || $form['show_img_sig'] != '1') $form['show_img_sig'] = '0';
                if (!isset($form['show_avatars']) || $form['show_avatars'] != '1') $form['show_avatars'] = '0';
                if (!isset($form['show_sig']) || $form['show_sig'] != '1') $form['show_sig'] = '0';                break;
            }        case 'privacy':
            {
                $form = extract_elements(array('email_setting', 'notify_with_post', 'auto_notify'));                $form['email_setting'] = intval($form['email_setting']);
                if ($form['email_setting'] < 0 || $form['email_setting'] > 2) $form['email_setting'] = $_config['o_default_email_setting'];                if (!isset($form['notify_with_post']) || $form['notify_with_post'] != '1') $form['notify_with_post'] = '0';
                if (!isset($form['auto_notify']) || $form['auto_notify'] != '1') $form['auto_notify'] = '0';                break;
            }        default:
            message($lang_common['Bad request']);
    }
    // Single quotes around non-empty values and NULL for empty values
    $temp = array();
    while (list($key, $input) = @each($form))
    {
        $value = ($input !== '') ? '\'' . $db->escape($input) . '\'' : 'NULL';        $temp[] = $key . '=' . $value;
    }    if (empty($temp))
        message($lang_common['Bad request']);    $db->setQuery('UPDATE forum_userprofiles SET ' . implode(',', $temp) . ' WHERE id=' . $id)->execute() or error('Unable to update profile', __FILE__, __LINE__, $db->error());
    // If we changed the username we have to update some stuff
    if ($username_updated)
    {
        $db->setQuery('UPDATE forum_posts SET poster=\'' . $db->escape($form['username']) . '\' WHERE poster_id=' . $id)->execute() or error('Unable to update posts', __FILE__, __LINE__, $db->error());
        $db->setQuery('UPDATE forum_posts SET edited_by=\'' . $db->escape($form['username']) . '\' WHERE edited_by=\'' . $db->escape($old_username) . '\'')->execute() or error('Unable to update posts', __FILE__, __LINE__, $db->error());
        $db->setQuery('UPDATE forum_topics SET poster=\'' . $db->escape($form['username']) . '\' WHERE poster=\'' . $db->escape($old_username) . '\'')->execute() or error('Unable to update topics', __FILE__, __LINE__, $db->error());
        $db->setQuery('UPDATE forum_topics SET last_poster=\'' . $db->escape($form['username']) . '\' WHERE last_poster=\'' . $db->escape($old_username) . '\'')->execute() or error('Unable to update topics', __FILE__, __LINE__, $db->error());
        $db->setQuery('UPDATE forum_forums SET last_poster=\'' . $db->escape($form['username']) . '\' WHERE last_poster=\'' . $db->escape($old_username) . '\'')->execute() or error('Unable to update forums', __FILE__, __LINE__, $db->error());
        $db->setQuery('UPDATE forum_online SET ident=\'' . $db->escape($form['username']) . '\' WHERE ident=\'' . $db->escape($old_username) . '\'')->execute() or error('Unable to update online list', __FILE__, __LINE__, $db->error());
        // If the user is a moderator or an administrator we have to update the moderator lists
        $db->setQuery('SELECT group_id FROM forum_userprofiles WHERE id=' . $id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
        $group_id = $db->result();        $db->setQuery('SELECT g_moderator FROM forum_groups WHERE g_id=' . $group_id) or error('Unable to fetch group', __FILE__, __LINE__, $db->error());
        $group_mod = $db->result();        if ($group_id == PUN_ADMIN || $group_mod == '1')
        {
            $db->setQuery('SELECT id, moderators FROM forum_forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());            while ($cur_forum = $db->fetch_assoc())
            {
                $cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();                if (in_array($id, $cur_moderators))
                {
                    unset($cur_moderators[$old_username]);
                    $cur_moderators[$form['username']] = $id;
                    ksort($cur_moderators);
$db->setQuery('UPDATE forum_forums SET moderators=\'' . $db->escape(serialize($cur_moderators)) . '\' WHERE id=' . $cur_forum['id'])->execute() or error('Unable to update forum', __FILE__, __LINE__, $db->error());
                }
            }
        }
    }    redirect('profile.php?section=' . $section . '&amp;id=' . $id, $lang_profile['Profile redirect']);
}$db->setQuery('SELECT u.username, u.email, u.title, u.realname, u.url, u.jabber, u.icq, u.msn, u.aim, u.yahoo, u.location, u.signature, u.disp_topics, u.disp_posts, u.email_setting, u.notify_with_post, u.auto_notify, u.show_smilies, u.show_img, u.show_img_sig, u.show_avatars, u.show_sig, u.timezone, u.dst, u.language, u.style, u.num_posts, u.last_post, u.registered, u.registration_ip, u.admin_note, u.date_format, u.time_format, g.g_id, g.g_user_title, g.g_moderator FROM forum_userprofiles AS u LEFT JOIN forum_groups AS g ON g.g_id=u.group_id WHERE u.id=' . $id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows())
    message($lang_common['Bad request']);$user = $db->fetch_assoc();$last_post = MDate::format($user['last_post']);if ($user['signature'] != '')
{
    require SHELL_PATH . 'include/parser.php';
    $parsed_signature = parse_signature($user['signature']);
}
// View or edit?
if ($_user['id'] != $id &&
    (!$_user['is_admmod'] || $user['g_id'] == PUN_ADMIN ||
        ($_user['g_moderator'] == '1' && $_user['g_mod_edit_users'] == '0') ||
            ($_user['g_moderator'] == '1' && $user['g_moderator'] == '1')))
{
    if ($user['email_setting'] == '0' && !$_user['is_guest'] && $_user['g_send_email'] == '1')
        $email_field = _CHtml::link($user['email'], 'mailto:' . $user['email']);
    else if ($user['email_setting'] == '1' && !$_user['is_guest'] && $_user['g_send_email'] == '1')
        $email_field = _CHtml::link($lang_common['Send email'], array('forum/misc', 'email' => $id)) . '</a>';
    else
        $email_field = $lang_profile['Private'];    $user_title_field = get_title($user);    if ($user['url'] != '')
    {
        $user['url'] = _CHtml::encode($user['url']);        if ($_config['o_censoring'] == '1')
            $user['url'] = censor_words($user['url']);        $url = _CHtml::link($user['url'], $user['url']);
    }
    else
        $url = $lang_profile['Unknown'];    if ($_config['o_avatars'] == '1')
    {
        $avatar_field = generate_avatar_markup($id);
        if ($avatar_field == '')
            $avatar_field = $lang_profile['No avatar'];
    }    $posts_field = '';
    if ($_config['o_show_post_count'] == '1' || $_user['is_admmod'])
        $posts_field = forum_number_format($user['num_posts']);
    if ($_user['g_search'] == '1')
        $posts_field .= (($posts_field != '') ? ' - ' : '') . _CHtml::link($lang_profile['Show posts'], array('forum/search', 'action' => 'show_user', 'user_id' => $id));    $page_title = _CHtml::encode($this->PageTitle . ' / ' . sprintf($lang_profile['Users profile'], $user['username']));
    define('PUN_ALLOW_INDEX', 1);
    require SHELL_PATH . 'header.php';    ?>
<div id="viewprofile" class="block">
	<h2><span><?php echo $lang_common['Profile'] ?></span></h2>
	<div class="box">
		<div class="fakeform">
			<div class="inform">
				<fieldset>
				<legend><?php echo $lang_profile['Section personal'] ?></legend>
					<div class="infldset">
						<dl>
							<dt><?php echo $lang_common['Username'] ?>: </dt>
							<dd><?php echo _CHtml::encode($user['username']) ?></dd>
							<dt><?php echo $lang_common['Title'] ?>: </dt>
							<dd><?php echo ($_config['o_censoring'] == '1') ? censor_words($user_title_field) : $user_title_field; ?></dd>
							<dt><?php echo $lang_profile['Realname'] ?>: </dt>
							<dd><?php echo ($user['realname'] != '') ? _CHtml::encode(($_config['o_censoring'] == '1') ? censor_words($user['realname']) : $user['realname']) : $lang_profile['Unknown']; ?></dd>
							<dt><?php echo $lang_profile['Location'] ?>: </dt>
							<dd><?php echo ($user['location'] != '') ? _CHtml::encode(($_config['o_censoring'] == '1') ? censor_words($user['location']) : $user['location']) : $lang_profile['Unknown']; ?></dd>
							<dt><?php echo $lang_profile['Website'] ?>: </dt>
							<dd><?php echo $url ?></dd>
							<dt><?php echo $lang_common['Email'] ?>: </dt>
							<dd><?php echo $email_field ?></dd>
						</dl>
						<div class="clearer"></div>
					</div>
				</fieldset>
			</div>
			<div class="inform">
				<fieldset>
				<legend><?php echo $lang_profile['Section messaging'] ?></legend>
					<div class="infldset">
						<dl>
							<dt><?php echo $lang_profile['Jabber'] ?>: </dt>
							<dd><?php echo ($user['jabber'] != '') ? _CHtml::encode($user['jabber']) : $lang_profile['Unknown']; ?></dd>
							<dt><?php echo $lang_profile['ICQ'] ?>: </dt>
							<dd><?php echo ($user['icq'] != '') ? $user['icq'] : $lang_profile['Unknown']; ?></dd>
							<dt><?php echo $lang_profile['MSN'] ?>: </dt>
							<dd><?php echo ($user['msn'] != '') ? _CHtml::encode(($_config['o_censoring'] == '1') ? censor_words($user['msn']) : $user['msn']) : $lang_profile['Unknown']; ?></dd>
							<dt><?php echo $lang_profile['AOL IM'] ?>: </dt>
							<dd><?php echo ($user['aim'] != '') ? _CHtml::encode(($_config['o_censoring'] == '1') ? censor_words($user['aim']) : $user['aim']) : $lang_profile['Unknown']; ?></dd>
							<dt><?php echo $lang_profile['Yahoo'] ?>: </dt>
							<dd><?php echo ($user['yahoo'] != '') ? _CHtml::encode(($_config['o_censoring'] == '1') ? censor_words($user['yahoo']) : $user['yahoo']) : $lang_profile['Unknown']; ?></dd>
						</dl>
						<div class="clearer"></div>
					</div>
				</fieldset>
			</div>
<?php if ($_config['o_avatars'] == '1' || $_config['o_signatures'] == '1'): ?>			<div class="inform">
				<fieldset>
				<legend><?php echo $lang_profile['Section personality'] ?></legend>
					<div class="infldset">
						<dl>
<?php if ($_config['o_avatars'] == '1'): ?>							<dt><?php echo $lang_profile['Avatar'] ?>: </dt>
							<dd><?php echo $avatar_field ?></dd>
<?php endif;
        if ($_config['o_signatures'] == '1'): ?>							<dt><?php echo $lang_profile['Signature'] ?>: </dt>
							<dd><?php echo isset($parsed_signature) ? '<div class="postsignature postmsg">' . $parsed_signature . '</div>' : $lang_profile['No sig']; ?></dd>
<?php endif; ?>						</dl>
						<div class="clearer"></div>
					</div>
				</fieldset>
			</div>
<?php endif; ?>			<div class="inform">
				<fieldset>
				<legend><?php echo $lang_profile['User activity'] ?></legend>
					<div class="infldset">
						<dl>
<?php if ($posts_field != ''): ?>							<dt><?php echo $lang_common['Posts'] ?>: </dt>
							<dd><?php echo $posts_field ?></dd>
<?php endif; ?>							<dt><?php echo $lang_common['Last post'] ?>: </dt>
							<dd><?php echo $last_post ?></dd>
							<dt><?php echo $lang_common['Registered'] ?>: </dt>
							<dd><?php echo MDate::format($user['registered'], true) ?></dd>
						</dl>
						<div class="clearer"></div>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
</div><?php        require SHELL_PATH . 'footer.php';
    }
    else
    {
        if (!$section || $section == 'essentials')
        {
            if ($_user['is_admmod'])
            {
                if ($_user['g_id'] == PUN_ADMIN || $_user['g_mod_rename_users'] == '1')
                    $username_field = '<input type="hidden" name="old_username" value="' . _CHtml::encode($user['username']) . '" /><label><strong>' . $lang_common['Username'] . '</strong><br /><input type="text" name="req_username" value="' . _CHtml::encode($user['username']) . '" size="25" maxlength="25" /><br /></label>' . "\n";
                else
                    $username_field = '<p>' . $lang_common['Username'] . ': ' . _CHtml::encode($user['username']) . '</p>' . "\n";                $email_field = '<label><strong>' . $lang_common['Email'] . '</strong><br /><input type="text" name="req_email" value="' . $user['email'] . '" size="40" maxlength="50" /><br /></label><p>' . _CHtml::link($lang_common['Send email'], array('forum/misc', 'email' => $id)) . '</p>' . "\n";
            }
            else
            {
                $username_field = '<p>' . $lang_common['Username'] . ': ' . _CHtml::encode($user['username']) . '</p>' . "\n";                if ($_config['o_regs_verify'] == '1')
                    $email_field = '<p>' . $lang_common['Email'] . ': ' . $user['email'] . '&nbsp;-&nbsp;' . _CHtml::link($lang_profile['Change email'], array('forum/profile', 'action' => 'change_email', 'id' => $id)) . '</p>' . "\n";
                else
                    $email_field = '<label><strong>' . $lang_common['Email'] . '</strong><br /><input type="text" name="req_email" value="' . $user['email'] . '" size="40" maxlength="50" /><br /></label>' . "\n";
            }            $posts_field = '';
            if ($_user['g_id'] == PUN_ADMIN)
                $posts_field = '<label>' . $lang_common['Posts'] . '<br /><input type="text" name="num_posts" value="' . $user['num_posts'] . '" size="8" maxlength="8" /><br /></label><p>' . _CHtml::link($lang_profile['Show posts'], array('forum/search', 'action' => 'show_user', 'user_id' => $id)) . '</p>' . "\n";
            else if ($_config['o_show_post_count'] == '1' || $_user['is_admmod'])
                $posts_field = '<p>' . $lang_common['Posts'] . ': ' . forum_number_format($user['num_posts']) . ($_user['g_search'] == '1' ? ' - ' . _CHtml::link($lang_profile['Show posts'], array('forum/search', 'action' => 'show_user', 'user_id' => $id)) : '') . '</p>' . "\n";
            else if ($_user['g_search'] == '1')
                $posts_field = '<p>' . _CHtml::link($lang_profile['Show posts'], array('forum/search', 'action' => 'show_user', 'user_id' => $id)) . '</p>' . "\n";            $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_common['Profile'];
            $required_fields = array('req_username' => $lang_common['Username'], 'req_email' => $lang_common['Email']);
            require SHELL_PATH . 'header.php';            generate_profile_menu('essentials');            ?>
	<div class="blockform">
		<h2><span><?php echo _CHtml::encode($user['username']) . ' - ' . $lang_profile['Section essentials'] ?></span></h2>
		<div class="box">
			<?php echo _CHtml::form(array('profile','section'=>'essentials','id'=>$id), 'POST', array('id'=>'profile1','onsubmit'=>'return process_form(this)'));?>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_profile['Username and pass legend'] ?></legend>
						<div class="infldset">
							<input type="hidden" name="form_sent" value="1" />
							<?php echo $username_field ?>
<?php if ($_user['id'] == $id || $_user['g_id'] == PUN_ADMIN || ($user['g_moderator'] == '0' && $_user['g_mod_change_passwords'] == '1')): ?><p><?php echo _CHtml::link($lang_profile['Change pass'], array('forum/profile', 'action' => 'change_pass', 'id' => $id));?></p>
<?php endif; ?>					</div>
					</fieldset>
				</div>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_prof_reg['Email legend'] ?></legend>
						<div class="infldset">
							<?php echo $email_field ?>
						</div>
					</fieldset>
				</div>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_prof_reg['Localisation legend'] ?></legend>
						<div class="infldset">
							<p><?php echo $lang_prof_reg['Time zone info'] ?></p>
							<label><?php echo $lang_prof_reg['Time zone'] ?>
								<br /><select name="form[timezone]">
								<option value="-12"<?php if ($user['timezone'] == - 12) echo ' selected="selected"' ?>>-12</option>
								<option value="-11"<?php if ($user['timezone'] == - 11) echo ' selected="selected"' ?>>-11</option>
								<option value="-10"<?php if ($user['timezone'] == - 10) echo ' selected="selected"' ?>>-10</option>
								<option value="-9.5"<?php if ($user['timezone'] == - 9.5) echo ' selected="selected"' ?>>-09.5</option>
								<option value="-9"<?php if ($user['timezone'] == - 9) echo ' selected="selected"' ?>>-09</option>
								<option value="-8.5"<?php if ($user['timezone'] == - 8.5) echo ' selected="selected"' ?>>-08.5</option>
								<option value="-8"<?php if ($user['timezone'] == - 8) echo ' selected="selected"' ?>>-08 PST</option>
								<option value="-7"<?php if ($user['timezone'] == - 7) echo ' selected="selected"' ?>>-07 MST</option>
								<option value="-6"<?php if ($user['timezone'] == - 6) echo ' selected="selected"' ?>>-06 CST</option>
								<option value="-5"<?php if ($user['timezone'] == - 5) echo ' selected="selected"' ?>>-05 EST</option>
								<option value="-4"<?php if ($user['timezone'] == - 4) echo ' selected="selected"' ?>>-04 AST</option>
								<option value="-3.5"<?php if ($user['timezone'] == - 3.5) echo ' selected="selected"' ?>>-03.5</option>
								<option value="-3"<?php if ($user['timezone'] == - 3) echo ' selected="selected"' ?>>-03 ADT</option>
								<option value="-2"<?php if ($user['timezone'] == - 2) echo ' selected="selected"' ?>>-02</option>
								<option value="-1"<?php if ($user['timezone'] == - 1) echo ' selected="selected"' ?>>-01</option>
								<option value="0"<?php if ($user['timezone'] == 0) echo ' selected="selected"' ?>>00 GMT</option>
								<option value="1"<?php if ($user['timezone'] == 1) echo ' selected="selected"' ?>>+01 CET</option>
								<option value="2"<?php if ($user['timezone'] == 2) echo ' selected="selected"' ?>>+02</option>
								<option value="3"<?php if ($user['timezone'] == 3) echo ' selected="selected"' ?>>+03</option>
								<option value="3.5"<?php if ($user['timezone'] == 3.5) echo ' selected="selected"' ?>>+03.5</option>
								<option value="4"<?php if ($user['timezone'] == 4) echo ' selected="selected"' ?>>+04</option>
								<option value="4.5"<?php if ($user['timezone'] == 4.5) echo ' selected="selected"' ?>>+04.5</option>
								<option value="5"<?php if ($user['timezone'] == 5) echo ' selected="selected"' ?>>+05</option>
								<option value="5.5"<?php if ($user['timezone'] == 5.5) echo ' selected="selected"' ?>>+05.5</option>
								<option value="6"<?php if ($user['timezone'] == 6) echo ' selected="selected"' ?>>+06</option>
								<option value="6.5"<?php if ($user['timezone'] == 6.5) echo ' selected="selected"' ?>>+06.5</option>
								<option value="7"<?php if ($user['timezone'] == 7) echo ' selected="selected"' ?>>+07</option>
								<option value="8"<?php if ($user['timezone'] == 8) echo ' selected="selected"' ?>>+08</option>
								<option value="9"<?php if ($user['timezone'] == 9) echo ' selected="selected"' ?>>+09</option>
								<option value="9.5"<?php if ($user['timezone'] == 9.5) echo ' selected="selected"' ?>>+09.5</option>
								<option value="10"<?php if ($user['timezone'] == 10) echo ' selected="selected"' ?>>+10</option>
								<option value="10.5"<?php if ($user['timezone'] == 10.5) echo ' selected="selected"' ?>>+10.5</option>
								<option value="11"<?php if ($user['timezone'] == 11) echo ' selected="selected"' ?>>+11</option>
								<option value="11.5"<?php if ($user['timezone'] == 11.5) echo ' selected="selected"' ?>>+11.5</option>
								<option value="12"<?php if ($user['timezone'] == 12) echo ' selected="selected"' ?>>+12</option>
								<option value="13"<?php if ($user['timezone'] == 13) echo ' selected="selected"' ?>>+13</option>
								<option value="14"<?php if ($user['timezone'] == 14) echo ' selected="selected"' ?>>+14</option>
							</select>
							<br /></label>
							<div class="rbox">
								<label><input type="checkbox" name="form[dst]" value="1"<?php if ($user['dst'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_prof_reg['DST'] ?><br /></label>
							</div>
							<label><?php echo $lang_prof_reg['Time format'] ?>							<br /><select name="form[time_format]">
<?php
        foreach (array_unique($forum_time_formats) as $key => $time_format)
    {
        echo "\t\t\t\t\t\t\t\t" . '<option value="' . $key . '"';
        if ($user['time_format'] == $key)
            echo ' selected="selected"';
        echo '>' . MDate::format(time(), false, null, $time_format, true, true);
        if ($key == 0)
            echo ' (' . $lang_prof_reg['Default'] . ')';
        echo "</option>\n";
    }    ?>
							</select>
							<br /></label>
							<label><?php echo $lang_prof_reg['Date format'] ?>							<br /><select name="form[date_format]">
<?php
    foreach (array_unique($forum_date_formats) as $key => $date_format)
    {
        echo "\t\t\t\t\t\t\t\t" . '<option value="' . $key . '"';
        if ($user['date_format'] == $key)
            echo ' selected="selected"';
        echo '>' . MDate::format(time(), true, $date_format, null, false, true);
        if ($key == 0)
            echo ' (' . $lang_prof_reg['Default'] . ')';
        echo "</option>\n";
    }    ?>
							</select>
							<br /></label><?php    $languages = array();
    $d = dir(SHELL_PATH . 'lang');
    while (($entry = $d->read()) !== false)
    {
        if ($entry != '.' && $entry != '..' && is_dir(SHELL_PATH . 'lang/' . $entry) && file_exists(SHELL_PATH . 'lang/' . $entry . '/common.php'))
            $languages[] = $entry;
    }
    $d->close();
    // Only display the language selection box if there's more than one language available
    if (count($languages) > 1)
    {
        natsort($languages);        ?>
							<label><?php echo $lang_prof_reg['Language'] ?>: <?php echo $lang_prof_reg['Language info'] ?>
							<br /><select name="form[language]">
<?php        while (list(, $temp) = @each($languages))
        {
            if ($user['language'] == $temp)
                echo "\t\t\t\t\t\t\t\t" . '<option value="' . $temp . '" selected="selected">' . $temp . '</option>' . "\n";
            else
                echo "\t\t\t\t\t\t\t\t" . '<option value="' . $temp . '">' . $temp . '</option>' . "\n";
        }        ?>
							</select>
							<br /></label>
<?php    }    ?>
						</div>
					</fieldset>
				</div>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_profile['User activity'] ?></legend>
						<div class="infldset">
							<p><?php echo $lang_common['Registered'] ?>: <?php echo MDate::format($user['registered'], true);
    if ($_user['is_admmod']) echo ' (' . _CHtml::link(_CHtml::encode($user['registration_ip']), array('forum/moderate', 'get_host' => _CHtml::encode($user['registration_ip'])));
    ?></p>
							<p><?php echo $lang_common['Last post'] ?>: <?php echo $last_post ?></p>
							<?php echo $posts_field ?>
<?php if ($_user['is_admmod']): ?>							<label><?php echo $lang_profile['Admin note'] ?><br />
							<input id="admin_note" type="text" name="admin_note" value="<?php echo _CHtml::encode($user['admin_note']) ?>" size="30" maxlength="30" /><br /></label>
<?php endif; ?>						</div>
					</fieldset>
				</div>
				<p class="buttons"><input type="submit" name="update" value="<?php echo $lang_common['Submit'] ?>" /> <?php echo $lang_profile['Instructions'] ?></p>
			</form>
		</div>
	</div>
<?php }
else if ($section == 'personal')
{
    if ($_user['g_set_title'] == '1')
        $title_field = '<label>' . $lang_common['Title'] . '&nbsp;&nbsp;(<em>' . $lang_profile['Leave blank'] . '</em>)<br /><input type="text" name="title" value="' . _CHtml::encode($user['title']) . '" size="30" maxlength="50" /><br /></label>' . "\n";    $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_common['Profile'];
    require SHELL_PATH . 'header.php';
    generate_profile_menu('personal');?>
	<div class="blockform">
		<h2><span><?php echo _CHtml::encode($user['username']) . ' - ' . $lang_profile['Section personal'] ?></span></h2>
		<div class="box">
			<?php echo _CHtml::form(array('profile','section'=>'personal','id'=>$id), 'POST', array('id'=>'profile2'));?>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_profile['Personal details legend'] ?></legend>
						<div class="infldset">
							<input type="hidden" name="form_sent" value="1" />
							<label><?php echo $lang_profile['Realname'] ?><br /><input type="text" name="form[realname]" value="<?php echo _CHtml::encode($user['realname']) ?>" size="40" maxlength="40" /><br /></label>
<?php if (isset($title_field)): ?>							<?php echo $title_field ?>
<?php endif; ?>							<label><?php echo $lang_profile['Location'] ?><br /><input type="text" name="form[location]" value="<?php echo _CHtml::encode($user['location']) ?>" size="30" maxlength="30" /><br /></label>
							<label><?php echo $lang_profile['Website'] ?><br /><input type="text" name="form[url]" value="<?php echo _CHtml::encode($user['url']) ?>" size="50" maxlength="80" /><br /></label>
						</div>
					</fieldset>
				</div>
				<p class="buttons"><input type="submit" name="update" value="<?php echo $lang_common['Submit'] ?>" /> <?php echo $lang_profile['Instructions'] ?></p>
			</form>
		</div>
	</div>
<?php }
else if ($section == 'messaging')
{
    $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_common['Profile'];
    require SHELL_PATH . 'header.php';
    generate_profile_menu('messaging');    ?>
	<div class="blockform">
		<h2><span><?php echo _CHtml::encode($user['username']) . ' - ' . $lang_profile['Section messaging'] ?></span></h2>
		<div class="box">
			<?php echo _CHtml::form(array('profile','section'=>'messaging','id'=>$id), 'POST', array('id'=>'profile3'));?>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_profile['Contact details legend'] ?></legend>
						<div class="infldset">
							<input type="hidden" name="form_sent" value="1" />
							<label><?php echo $lang_profile['Jabber'] ?><br /><input id="jabber" type="text" name="form[jabber]" value="<?php echo _CHtml::encode($user['jabber']) ?>" size="40" maxlength="75" /><br /></label>
							<label><?php echo $lang_profile['ICQ'] ?><br /><input id="icq" type="text" name="form[icq]" value="<?php echo $user['icq'] ?>" size="12" maxlength="12" /><br /></label>
							<label><?php echo $lang_profile['MSN'] ?><br /><input id="msn" type="text" name="form[msn]" value="<?php echo _CHtml::encode($user['msn']) ?>" size="40" maxlength="50" /><br /></label>
							<label><?php echo $lang_profile['AOL IM'] ?><br /><input id="aim" type="text" name="form[aim]" value="<?php echo _CHtml::encode($user['aim']) ?>" size="20" maxlength="30" /><br /></label>
							<label><?php echo $lang_profile['Yahoo'] ?><br /><input id="yahoo" type="text" name="form[yahoo]" value="<?php echo _CHtml::encode($user['yahoo']) ?>" size="20" maxlength="30" /><br /></label>
						</div>
					</fieldset>
				</div>
				<p class="buttons"><input type="submit" name="update" value="<?php echo $lang_common['Submit'] ?>" /> <?php echo $lang_profile['Instructions'] ?></p>
			</form>
		</div>
	</div>
<?php }
else if ($section == 'personality')
{
    if ($_config['o_avatars'] == '0' && $_config['o_signatures'] == '0')
        message($lang_common['Bad request']);    $avatar_field = _CHtml::link($lang_profile['Change avatar'], array('forum/profile', 'action' => 'upload_avatar', 'id' => $id));    $user_avatar = generate_avatar_markup($id);
    if ($user_avatar)
        $avatar_field .= '&nbsp;&nbsp;&nbsp;' . _CHtml::link($lang_profile['Delete avatar'], array('forum/profile', 'action' => 'delete_avatar', 'id' => $id));
    else
        $avatar_field = _CHtml::link($lang_profile['Upload avatar'], array('forum/profile', 'action' => 'upload_avatar', 'id' => $id));    if ($user['signature'] != '')
        $signature_preview = '<p>' . $lang_profile['Sig preview'] . '</p>' . "\n\t\t\t\t\t\t\t" . '<div class="postsignature postmsg">' . "\n\t\t\t\t\t\t\t\t" . '<hr />' . "\n\t\t\t\t\t\t\t\t" . $parsed_signature . "\n\t\t\t\t\t\t\t" . '</div>' . "\n";
    else
        $signature_preview = '<p>' . $lang_profile['No sig'] . '</p>' . "\n";    $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_common['Profile'];
    require SHELL_PATH . 'header.php';    generate_profile_menu('personality');    ?>
	<div class="blockform">
		<h2><span><?php echo _CHtml::encode($user['username']) . ' - ' . $lang_profile['Section personality'] ?></span></h2>
		<div class="box">
			<?php echo _CHtml::form(array('profile','section'=>'personality','id'=>$id), 'POST', array('id'=>'profile4'));?>
				<div><input type="hidden" name="form_sent" value="1" /></div>
<?php if ($_config['o_avatars'] == '1'): ?>				<div class="inform">
					<fieldset id="profileavatar">
						<legend><?php echo $lang_profile['Avatar legend'] ?></legend>
						<div class="infldset">
							<?php if ($user_avatar) echo $user_avatar ?>
							<p><?php echo $lang_profile['Avatar info'] ?></p>
							<p class="clearb"><?php echo $avatar_field ?></p>
						</div>
					</fieldset>
				</div>
<?php endif;
        if ($_config['o_signatures'] == '1'): ?>				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_profile['Signature legend'] ?></legend>
						<div class="infldset">
							<p><?php echo $lang_profile['Signature info'] ?></p>
							<div class="txtarea">
								<label><?php echo $lang_profile['Sig max length'] ?>: <?php echo forum_number_format($_config['p_sig_length']) ?> / <?php echo $lang_profile['Sig max lines'] ?>: <?php echo $_config['p_sig_lines'] ?><br />
								<textarea name="signature" rows="4" cols="65"><?php echo _CHtml::encode($user['signature']) ?></textarea><br /></label>
							</div>
							<ul class="bblinks">
								<li><?php echo _CHtml::link($lang_common['BBCode'], array('forum/help#bbcode'), array('onclick' => 'window.open(this.href); return false;'));?>: <?php echo ($_config['p_sig_bbcode'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
								<li><?php echo _CHtml::link($lang_common['img tag'], array('forum/help#img'), array('onclick' => 'window.open(this.href); return false;'));?>: <?php echo ($_config['p_sig_img_tag'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
								<li><?php echo _CHtml::link($lang_common['Smilies'], array('forum/help#smilies'), array('onclick' => 'window.open(this.href); return false;'));?>: <?php echo ($_config['o_smilies_sig'] == '1') ? $lang_common['on'] : $lang_common['off']; ?></li>
							</ul>
							<?php echo $signature_preview ?>
						</div>
					</fieldset>
				</div>
<?php endif; ?>				<p class="buttons"><input type="submit" name="update" value="<?php echo $lang_common['Submit'] ?>" /> <?php echo $lang_profile['Instructions'] ?></p>
			</form>
		</div>
	</div>
<?php    }
    else if ($section == 'display')
    {
        $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_common['Profile'];
        require SHELL_PATH . 'header.php';        generate_profile_menu('display');        ?>
	<div class="blockform">
		<h2><span><?php echo _CHtml::encode($user['username']) . ' - ' . $lang_profile['Section display'] ?></span></h2>
		<div class="box">
			<?php echo _CHtml::form(array('profile','section'=>'display','id'=>$id), 'POST', array('id'=>'profile5'));?>
				<div><input type="hidden" name="form_sent" value="1" /></div>
<?php        $styles = array();
        $d = dir(SHELL_PATH . 'style');
        while (($entry = $d->read()) !== false)
        {
            if (substr($entry, strlen($entry) - 4) == '.css')
                $styles[] = substr($entry, 0, strlen($entry) - 4);
        }
        $d->close();
        // Only display the style selection box if there's more than one style available
        if (count($styles) == 1)
            echo "\t\t\t" . '<div><input type="hidden" name="form[style]" value="' . $styles[0] . '" /></div>' . "\n";
        else if (count($styles) > 1)
        {
            natsort($styles);            ?>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_profile['Style legend'] ?></legend>
						<div class="infldset">
							<label><?php echo $lang_profile['Style info'] ?><br />
							<select name="form[style]">
<?php            while (list(, $temp) = @each($styles))
            {
                if ($user['style'] == $temp)
                    echo "\t\t\t\t\t\t\t\t" . '<option value="' . $temp . '" selected="selected">' . str_replace('_', ' ', $temp) . '</option>' . "\n";
                else
                    echo "\t\t\t\t\t\t\t\t" . '<option value="' . $temp . '">' . str_replace('_', ' ', $temp) . '</option>' . "\n";
            }            ?>
							</select>
							<br /></label>
						</div>
					</fieldset>
				</div>
<?php        }        ?>
<?php if ($_config['o_smilies'] == '1' || $_config['o_smilies_sig'] == '1' || $_config['o_signatures'] == '1' || $_config['o_avatars'] == '1' || $_config['p_message_img_tag'] == '1' || $_config['p_sig_img_tag'] == '1'): ?>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_profile['Post display legend'] ?></legend>
						<div class="infldset">
							<p><?php echo $lang_profile['Post display info'] ?></p>
							<div class="rbox">
<?php if ($_config['o_smilies'] == '1' || $_config['o_smilies_sig'] == '1'): ?>								<label><input type="checkbox" name="form[show_smilies]" value="1"<?php if ($user['show_smilies'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_profile['Show smilies'] ?><br /></label>
<?php endif;
                if ($_config['o_signatures'] == '1'): ?>								<label><input type="checkbox" name="form[show_sig]" value="1"<?php if ($user['show_sig'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_profile['Show sigs'] ?><br /></label>
<?php endif;
                    if ($_config['o_avatars'] == '1'): ?>								<label><input type="checkbox" name="form[show_avatars]" value="1"<?php if ($user['show_avatars'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_profile['Show avatars'] ?><br /></label>
<?php endif;
                        if ($_config['p_message_img_tag'] == '1'): ?>								<label><input type="checkbox" name="form[show_img]" value="1"<?php if ($user['show_img'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_profile['Show images'] ?><br /></label>
<?php endif;
                            if ($_config['p_sig_img_tag'] == '1'): ?>								<label><input type="checkbox" name="form[show_img_sig]" value="1"<?php if ($user['show_img_sig'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_profile['Show images sigs'] ?><br /></label>
<?php endif; ?>
							</div>
						</div>
					</fieldset>
				</div>
<?php endif; ?>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_profile['Pagination legend'] ?></legend>
						<div class="infldset">
							<label class="conl"><?php echo $lang_profile['Topics per page'] ?><br /><input type="text" name="form[disp_topics]" value="<?php echo $user['disp_topics'] ?>" size="6" maxlength="3" /><br /></label>
							<label class="conl"><?php echo $lang_profile['Posts per page'] ?><br /><input type="text" name="form[disp_posts]" value="<?php echo $user['disp_posts'] ?>" size="6" maxlength="3" /><br /></label>
							<p class="clearb"><?php echo $lang_profile['Paginate info'] ?> <?php echo $lang_profile['Leave blank'] ?></p>
						</div>
					</fieldset>
				</div>
				<p class="buttons"><input type="submit" name="update" value="<?php echo $lang_common['Submit'] ?>" /> <?php echo $lang_profile['Instructions'] ?></p>
			</form>
		</div>
	</div>
<?php                            }
                            else if ($section == 'privacy')
                            {
                                $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_common['Profile'];
                                require SHELL_PATH . 'header.php';
            generate_profile_menu('privacy');
            ?>
	<div class="blockform">
		<h2><span><?php echo _CHtml::encode($user['username']) . ' - ' . $lang_profile['Section privacy'] ?></span></h2>
		<div class="box">
			<?php echo _CHtml::form(array('profile','section'=>'privacy','id'=>$id), 'POST', array('id'=>'profile6'));?>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_prof_reg['Privacy options legend'] ?></legend>
						<div class="infldset">
							<input type="hidden" name="form_sent" value="1" />
							<p><?php echo $lang_prof_reg['Email setting info'] ?></p>
							<div class="rbox">
								<label><input type="radio" name="form[email_setting]" value="0"<?php if ($user['email_setting'] == '0') echo ' checked="checked"' ?> /><?php echo $lang_prof_reg['Email setting 1'] ?><br /></label>
								<label><input type="radio" name="form[email_setting]" value="1"<?php if ($user['email_setting'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_prof_reg['Email setting 2'] ?><br /></label>
								<label><input type="radio" name="form[email_setting]" value="2"<?php if ($user['email_setting'] == '2') echo ' checked="checked"' ?> /><?php echo $lang_prof_reg['Email setting 3'] ?><br /></label>
							</div>
						</div>
					</fieldset>
				</div>
<?php if ($_config['o_subscriptions'] == '1'): ?>				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_profile['Subscription legend'] ?></legend>
						<div class="infldset">
							<div class="rbox">
								<label><input type="checkbox" name="form[notify_with_post]" value="1"<?php if ($user['notify_with_post'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_profile['Notify full'] ?><br /></label>
								<label><input type="checkbox" name="form[auto_notify]" value="1"<?php if ($user['auto_notify'] == '1') echo ' checked="checked"' ?> /><?php echo $lang_profile['Auto notify full'] ?><br /></label>
							</div>
						</div>
					</fieldset>
				</div>
<?php endif; ?>				<p class="buttons"><input type="submit" name="update" value="<?php echo $lang_common['Submit'] ?>" /> <?php echo $lang_profile['Instructions'] ?></p>
			</form>
		</div>
	</div>
<?php                                                }
                                                else if ($section == 'admin')
                                                {
                                                    if (!$_user['is_admmod'] || ($_user['g_moderator'] == '1' && $_user['g_mod_ban_users'] == '0'))
                                                        message($lang_common['Bad request']);
                                $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_common['Profile'];
                                                    require SHELL_PATH . 'header.php';
                                generate_profile_menu('admin');
                                ?>
	<div class="blockform">
		<h2><span><?php echo _CHtml::encode($user['username']) . ' - ' . $lang_profile['Section admin'] ?></span></h2>
		<div class="box">
			<?php echo _CHtml::form(array('profile','section'=>'admin','id'=>$id,'action'=>'foo'), 'POST', array('id'=>'profile7'));?>
				<div class="inform">
				<input type="hidden" name="form_sent" value="1" />
					<fieldset>
<?php                                                    if ($_user['g_moderator'] == '1')
                                                    {?>
						<legend><?php echo $lang_profile['Delete ban legend'] ?></legend>
						<div class="infldset">
							<p><input type="submit" name="ban" value="<?php echo $lang_profile['Ban user'] ?>" /></p>
						</div>
					</fieldset>
				</div>
<?php                                                    }
                                                    else
                                                    {
                                                        if ($_user['id'] != $id)
                                                        {?>
						<legend><?php echo $lang_profile['Group membership legend'] ?></legend>
						<div class="infldset">
							<select id="group_id" name="group_id">
<?php                                                            $db->setQuery('SELECT g_id, g_title FROM forum_groups WHERE g_id!=' . PUN_GUEST . ' ORDER BY g_title') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());
                                        while ($cur_group = $db->fetch_assoc($result))
                                                            {
                                                                if ($cur_group['g_id'] == $user['g_id'] || ($cur_group['g_id'] == $_config['o_default_user_group'] && $user['g_id'] == ''))
                                                                    echo "\t\t\t\t\t\t\t\t" . '<option value="' . $cur_group['g_id'] . '" selected="selected">' . _CHtml::encode($cur_group['g_title']) . '</option>' . "\n";
                                                                else
                                                                    echo "\t\t\t\t\t\t\t\t" . '<option value="' . $cur_group['g_id'] . '">' . _CHtml::encode($cur_group['g_title']) . '</option>' . "\n";
                                                            }                                                            ?>
							</select>
							<input type="submit" name="update_group_membership" value="<?php echo $lang_profile['Save'] ?>" />
						</div>
					</fieldset>
				</div>
				<div class="inform">
					<fieldset>
<?php                                                        }                                                        ?>
						<legend><?php echo $lang_profile['Delete ban legend'] ?></legend>
						<div class="infldset">
							<input type="submit" name="delete_user" value="<?php echo $lang_profile['Delete user'] ?>" />&nbsp;&nbsp;<input type="submit" name="ban" value="<?php echo $lang_profile['Ban user'] ?>" />
						</div>
					</fieldset>
				</div>
<?php                                                        if ($user['g_moderator'] == '1' || $user['g_id'] == PUN_ADMIN)
                                                        {?>
				<div class="inform">
					<fieldset>
						<legend><?php echo $lang_profile['Set mods legend'] ?></legend>
						<div class="infldset">
							<p><?php echo $lang_profile['Moderator in info'] ?></p>
<?php                                                            $db->setQuery('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.moderators FROM forum_categories AS c INNER JOIN forum_forums AS f ON c.id=f.cat_id WHERE f.redirect_url IS NULL ORDER BY c.disp_position, c.id, f.disp_position') or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());
                                        $cur_category = 0;
                                                            while ($cur_forum = $db->fetch_assoc($result))
                                                            {
                                                                if ($cur_forum['cid'] != $cur_category) // A new category since last iteration?
                                                                    {
                                                                        if ($cur_category)
                                                                            echo "\n\t\t\t\t\t\t\t\t" . '</div>';
                                                    if ($cur_category != 0)
                                                                            echo "\n\t\t\t\t\t\t\t" . '</div>' . "\n";
                                                    echo "\t\t\t\t\t\t\t" . '<div class="conl">' . "\n\t\t\t\t\t\t\t\t" . '<p><strong>' . $cur_forum['cat_name'] . '</strong></p>' . "\n\t\t\t\t\t\t\t\t" . '<div class="rbox">';
                                                                        $cur_category = $cur_forum['cid'];
                                                                    }                                                                    $moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();
                                                echo "\n\t\t\t\t\t\t\t\t\t" . '<label><input type="checkbox" name="moderator_in[' . $cur_forum['fid'] . ']" value="1"' . ((in_array($id, $moderators)) ? ' checked="checked"' : '') . ' />' . _CHtml::encode($cur_forum['forum_name']) . '<br /></label>' . "\n";
                                                                }                                                                ?>
								</div>
							</div>
							<br class="clearb" /><input type="submit" name="update_forums" value="<?php echo $lang_profile['Update forums'] ?>" />
						</div>
					</fieldset>
				</div>
<?php                                                            }
                                                        }                                                        ?>
			</form>
		</div>
	</div>
<?php                                                    }
                                                    else
                                                        message($lang_common['Bad request']);
                                ?>
	<div class="clearer"></div>
</div>
<?php                                                    require SHELL_PATH . 'footer.php';
                                                }