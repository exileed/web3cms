<?php
/*---
Copyright (C) 2008-2009 FluxBB.org
based on code copyright (C) 2002-2005 Rickard Andersson
License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
---*/
if (isset($_GET['action']))
    define('PUN_QUIET_VISIT', 1);
require SHELL_PATH . 'include/common.php';
// Load the login.php language file
require SHELL_PATH . 'lang/' . $pun_user['language'] . '/login.php';
$action = isset($_GET['action']) ? $_GET['action'] : null;
if (isset($_POST['form_sent']) && $action == 'in')
{
    $form_username = trim($_POST['req_username']);
    $form_password = trim($_POST['req_password']);
    $save_pass = isset($_POST['save_pass']);
    $username_sql = ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb') ? 'username=\'' . $db->escape($form_username) . '\'' : 'LOWER(username)=LOWER(\'' . $db->escape($form_username) . '\')';
    $db->setQuery('SELECT * FROM ' . $db->db_prefix . 'users WHERE ' . $username_sql) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
    $cur_user = $db->fetch_assoc();
    $authorized = false;
    if (!empty($cur_user['password']))
    {
        $sha1_in_db = (strlen($cur_user['password']) == 40) ? true : false;
        $sha1_available = (function_exists('sha1') || function_exists('mhash')) ? true : false;
        $form_password_hash = pun_hash($form_password); // This could result in either an SHA-1 or an MD5 hash (depends on $sha1_available)
        // If there is a salt in the database we have upgraded from 1.3-legacy though havent yet logged in
        if (!empty($cur_user['salt']))
        {
            if (sha1($cur_user['salt'] . sha1($form_password)) == $cur_user['password'])
            {
                $authorized = true;
                $db->setQuery('UPDATE ' . $db->db_prefix . 'users SET password=\'' . $form_password_hash . '\', salt=NULL WHERE id=' . $cur_user['id']) or error('Unable to update user password', __FILE__, __LINE__, $db->error());
            }
        }
        else
        {
            if ($sha1_in_db && $sha1_available && $cur_user['password'] == $form_password_hash)
                $authorized = true;
            else if (!$sha1_in_db && $cur_user['password'] == md5($form_password))
            {
                $authorized = true;
                if ($sha1_available) // There's an MD5 hash in the database, but SHA1 hashing is available, so we update the DB
                    $db->setQuery('UPDATE ' . $db->db_prefix . 'users SET password=\'' . $form_password_hash . '\' WHERE id=' . $cur_user['id']) or error('Unable to update user password', __FILE__, __LINE__, $db->error());
            }
        }
    }
    if (!$authorized)
        message($lang_login['Wrong user/pass'] . ' ' . CHtml::link($lang_login['Forgotten pass'], array('forum/login', 'action' => 'forget')));
    // Update the status if this is the first time the user logged in
    if ($cur_user['group_id'] == PUN_UNVERIFIED)
        $db->setQuery('UPDATE ' . $db->db_prefix . 'users SET group_id=' . $pun_config['o_default_user_group'] . ' WHERE id=' . $cur_user['id']) or error('Unable to update user status', __FILE__, __LINE__, $db->error()); // Remove this users guest entry from the online list
    $db->setQuery('DELETE FROM ' . $db->db_prefix . 'online WHERE ident=\'' . $db->escape(get_remote_address()) . '\'') or error('Unable to delete from online list', __FILE__, __LINE__, $db->error());
    $expire = ($save_pass == '1') ? time() + 1209600 : time() + $pun_config['o_timeout_visit'];
    pun_setcookie($cur_user['id'], $form_password_hash, $expire);
    // Reset tracked topics
    set_tracked_topics(null);
    redirect(htmlspecialchars($_POST['redirect_url']), $lang_login['Login redirect']);
}
else if ($action == 'out')
{
    if ($pun_user['is_guest'] || !isset($_GET['id']) || $_GET['id'] != $pun_user['id'] || !isset($_GET['csrf_token']) || $_GET['csrf_token'] != pun_hash($pun_user['id'] . pun_hash(get_remote_address())))
    {
        Yii::app()->request->redirect(Yii::app()->createUrl('forum/'));
    }
    // Remove user from "users online" list
    $db->setQuery('DELETE FROM ' . $db->db_prefix . 'online WHERE user_id=' . $pun_user['id']) or error('Unable to delete from online list', __FILE__, __LINE__, $db->error());
    // Update last_visit (make sure there's something to update it with)
    if (isset($pun_user['logged']))
        $db->setQuery('UPDATE ' . $db->db_prefix . 'users SET last_visit=' . $pun_user['logged'] . ' WHERE id=' . $pun_user['id']) or error('Unable to update user visit data', __FILE__, __LINE__, $db->error());
    pun_setcookie(1, md5(uniqid(rand(), true)), time() + 31536000);
    redirect('index.php', $lang_login['Logout redirect']);
}
else if ($action == 'forget' || $action == 'forget_2')
{
    if (!$pun_user['is_guest']) Yii::app()->request->redirect(Yii::app()->createUrl('forum/'));      if (isset($_POST['form_sent']))
    {
        require SHELL_PATH . 'include/email.php'; // Validate the email address
        $email = strtolower(trim($_POST['req_email']));
        if (!is_valid_email($email))
            message($lang_common['Invalid email']);
        $db->setQuery('SELECT id, username, last_email_sent FROM ' . $db->db_prefix . 'users WHERE email=\'' . $db->escape($email) . '\'') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
        if ($db->num_rows())
        {
            // Load the "activate password" template
            $mail_tpl = trim(file_get_contents(SHELL_PATH . 'lang/' . $pun_user['language'] . '/mail_templates/activate_password.tpl'));
            // The first row contains the subject
            $first_crlf = strpos($mail_tpl, "\n");
            $mail_subject = trim(substr($mail_tpl, 8, $first_crlf - 8));
            $mail_message = trim(substr($mail_tpl, $first_crlf));
            // Do the generic replacements first (they apply to all emails sent out here)
            $mail_message = str_replace('<WEB_PATH>', $pun_config['o_WEB_PATH'] . ' / ', $mail_message);
            $mail_message = str_replace('<board_mailer>', $pun_config['o_board_title'] . ' ' . $lang_common['Mailer'], $mail_message);
            // Loop through users we found
            while ($cur_hit = $db->fetch_assoc())
            {
                if ($cur_hit['last_email_sent'] != '' && (time() - $cur_hit['last_email_sent']) < 3600 && (time() - $cur_hit['last_email_sent']) >= 0)
                    message($lang_login['Email flood']);
                // Generate a new password and a new password activation code
                $new_password = random_pass(8);
                $new_password_key = random_pass(8);
                $db->setQuery('UPDATE ' . $db->db_prefix . 'users SET activate_string = \'' . pun_hash($new_password) . '\', activate_key=\'' . $new_password_key . '\', last_email_sent = ' . time() . ' WHERE id=' . $cur_hit['id']) or error('Unable to update activation data', __FILE__, __LINE__, $db->error());
                // Do the user specific replacements to the template
                $cur_mail_message = str_replace('<username>', $cur_hit['username'], $mail_message);
                $cur_mail_message = str_replace('<activation_url>', $pun_config['o_WEB_PATH'] . ' / profile.php?id = ' . $cur_hit['id'] . '&action = change_pass&key = ' . $new_password_key, $cur_mail_message);
                $cur_mail_message = str_replace('<new_password>', $new_password, $cur_mail_message);
                pun_mail($email, $mail_subject, $cur_mail_message);
            }
            message($lang_login['Forget mail'] . ' ' . CHtml::link($pun_config['o_admin_email'], 'mailto:' . $pun_config['o_admin_email']));
        }
        else message($lang_login['No email match'] . ' ' . htmlspecialchars($email) . ' . ');
    }
    $page_title = pun_htmlspecialchars($pun_config['o_board_title']) . ' / ' . $lang_login['Request pass'];
    $required_fields = array('req_email' => $lang_common['Email']);
    $focus_element = array('request_pass', 'req_email');
    require SHELL_PATH . 'header.php';

    ?>
	<div class="blockform">	<h2>
	<span>
	<?php echo $lang_login['Request pass'] ?>
	</span></h2>
	<div class="box">
	<?php echo CHtml::form(array('login','action'=>'forget_2'), 'POST', array('id'=>'request_pass','onsubmit'=>'this.request_pass.disabled=true;if(process_form(this))
	{
		return true;
	}
	else{this.request_pass.disabled=false;return false;
	}'));?>
	<div class="inform">
	<fieldset>
	<legend>
	<?php echo $lang_login['Request pass legend'] ?>
	</legend>
	<div class="infldset">
	<input type="hidden" name="form_sent" value="1" />
	<input id="req_email" type="text" name="req_email" size="50" maxlength="50" />
	<p>
	<?php echo $lang_login['Request pass info'] ?>
	</p>
	</div>
	</fieldset>
	</div>
	<p class="buttons">
	<input type="submit" name="request_pass" value="<?php echo $lang_common['Submit'] ?>" />
	<?php echo CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?>
	</p>
	</form>
	</div>
	</div>
	<?php
    require SHELL_PATH . 'footer.php';
}
if (!$pun_user['is_guest'])
    Yii::app()->request->redirect(Yii::app()->createUrl('forum/'));
// Try to determine if the data in HTTP_REFERER is valid (if not, we redirect to index.php after login)
$redirect_url = (isset($_SERVER['HTTP_REFERER']) && preg_match('#^' . preg_quote($pun_config['o_WEB_PATH']) . '/(.*?)\.php#i', $_SERVER['HTTP_REFERER'])) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : $pun_config['o_WEB_PATH'] . '/index.php';
$page_title = pun_htmlspecialchars($pun_config['o_board_title']) . ' / ' . $lang_common['Login'];
$required_fields = array('req_username' => $lang_common['Username'], 'req_password' => $lang_common['Password']);
$focus_element = array('login', 'req_username');
require SHELL_PATH . 'header.php';

?>
<div class="blockform">	<h2>
<span>
<?php echo $lang_common['Login'] ?>
</span></h2>
<div class="box">
<?php echo CHtml::form(array('login','action'=>'in'), 'POST', array('id'=>'login','onsubmit'=>'return process_form(this)'));?>
<div class="inform">
<fieldset>
<legend>
<?php echo $lang_login['Login legend'] ?>
</legend>
<div class="infldset">
<input type="hidden" name="form_sent" value="1" />
<input type="hidden" name="redirect_url" value="<?php echo $redirect_url ?>" />
<label class="conl"><strong>
<?php echo $lang_common['Username'] ?></strong><br />
<input type="text" name="req_username" size="25" maxlength="25" tabindex="1" /><br />
</label>
<label class="conl"><strong>
<?php echo $lang_common['Password'] ?></strong><br />
<input type="password" name="req_password" size="25" tabindex="2" /><br />
</label>
<div class="rbox clearb">
<label>
<input type="checkbox" name="save_pass" value="1" tabindex="3" />
<?php echo $lang_login['Remember me'] ?> <br />
</label>
</div>
<p class="clearb">
<?php echo $lang_login['Login info'] ?>
</p>
<p>
<?php echo CHtml::link($lang_login['Not registered'], array('forum/register'), array('tabindex' => '4'));?>
&nbsp;&nbsp;
<?php echo CHtml::link($lang_login['Forgotten pass'], array('forum/login', 'action' => 'forget'), array('tabindex' => '5'));?>
</p>
</div>
</fieldset>
</div>
<p class="buttons">
<input type="submit" name="login" value="<?php echo $lang_common['Login'] ?>" tabindex="3" />
</p>
</form>
</div>
</div>
<?php
require SHELL_PATH . 'footer.php';