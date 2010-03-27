<?php
// Tell header.php to use the admin template
define('PUN_ADMIN_CONSOLE', 1);
require SHELL_PATH . 'include/common.php';
require SHELL_PATH . 'include/common_admin.php';
if (!$_user['is_admmod'])
    message($lang_common['No permission']);
// Show IP statistics for a certain user ID
if (isset($_GET['ip_stats'])) {
    $ip_stats = intval($_GET['ip_stats']);
    if ($ip_stats < 1)
        message($lang_common['Bad request']);
    require SHELL_PATH . 'header.php';
    ?>
<div class="linkst">
	<div class="inbox">
		<div><?php echo _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></div>
	</div>
</div><div id="users1" class="blocktable">
	<h2><span>Users</span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
			<thead>
				<tr>
					<th class="tcl" scope="col">IP address</th>
					<th class="tc2" scope="col">Last used</th>
					<th class="tc3" scope="col">Times found</th>
					<th class="tcr" scope="col">Action</th>
				</tr>
			</thead>
			<tbody>
<?php $db->setQuery('SELECT poster_ip, MAX(posted) AS last_used, COUNT(id) AS used_times FROM forum_posts WHERE poster_id=' . $ip_stats . ' GROUP BY poster_ip ORDER BY last_used DESC') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
    if ($db->num_rows()) {
        while ($cur_ip = $db->fetch_assoc()) {?>
				<tr>
					<td class="tcl"><?php echo _CHtml::link($cur_ip['poster_ip'], array('forum/moderate', 'get_host' => $cur_ip['poster_ip']));?></td>
					<td class="tc2"><?php echo MDate::format($cur_ip['last_used']) ?></td>
					<td class="tc3"><?php echo $cur_ip['used_times'] ?></td>
					<td class="tcr"><?php echo _CHtml::link('Find more users for this ip', array('forum/admin_users', 'show_users' => $cur_ip['poster_ip']));?></td>
				</tr>
<?php }
    }else
        echo "\t\t\t\t" . '<tr><td class="tcl" colspan="4">There are currently no posts by that user in the forum.</td></tr>' . "\n";
    ?>
			</tbody>
			</table>
		</div>
	</div>
</div><div class="linksb">
	<div class="inbox">
		<div><?php echo _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></div>
	</div>
</div>
<?php require SHELL_PATH . 'footer.php';
}
if (isset($_GET['show_users'])) {
    $ip = $_GET['show_users'];
    if (!@preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $ip) && !@preg_match('/^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/', $ip))
        message('The supplied IP address is not correctly formatted.');
    require SHELL_PATH . 'header.php';
    ?>
<div class="linkst">
	<div class="inbox">
		<div><?php echo _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></div>
	</div>
</div><div id="users2" class="blocktable">
	<h2><span>Users</span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
			<thead>
				<tr>
					<th class="tcl" scope="col">Username</th>
					<th class="tc2" scope="col">Email</th>
					<th class="tc3" scope="col">Title/Status</th>
					<th class="tc4" scope="col">Posts</th>
					<th class="tc5" scope="col">Admin note</th>
					<th class="tcr" scope="col">Actions</th>
				</tr>
			</thead>
			<tbody>
<?php $db->setQuery('SELECT DISTINCT poster_id, poster FROM forum_posts WHERE poster_ip=\'' . $db->escape($ip) . '\' ORDER BY poster DESC') or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
    $num_posts = $db->num_rows();
    if ($num_posts) {
        // Loop through users and print out some info
        for ($i = 0; $i < $num_posts; ++$i) {
            list($poster_id, $poster) = $db->fetch_row();
            $db->setQuery('SELECT u.id, u.username, u.email, ud.title, ud.num_posts, g.g_id, g.g_user_title FROM w3_user AS u INNER JOIN w3_user_details AS ud INNER JOIN forum_groups AS g ON g.g_id=ud.forumGroupId WHERE u.id>1 AND u.id=' . $poster_id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
            if (($user_data = $db->fetch_assoc())) {
                $user_title = get_title($user_data);
                $actions = _CHtml::link('View IP stats', array('forum/admin_users', 'ip_stats' => $user_data['id'])) . ' - ' . _CHtml::link('Show posts', array('forum/search', 'action' => 'show_user' , 'user_id' => $user_data['id']));
                ?>
				<tr>
					<td class="tcl"><?php echo _CHtml::link(_CHtml::encode($user_data['username']), array('forum/profile', 'id' => $user_data['id']));?>?></td>
					<td class="tc2"><?php echo _CHtml::link($user_data['email'], 'mailto:' . $user_data['email']);?></td>
					<td class="tc3"><?php echo $user_title ?></td>
					<td class="tc4"><?php echo forum_number_format($user_data['num_posts']) ?></td>
					<td class="tc5">&nbsp;</td>
					<td class="tcr"><?php echo $actions ?></td>
				</tr>
<?php }else {?>
				<tr>
					<td class="tcl"><?php echo _CHtml::encode($poster) ?></td>
					<td class="tc2">&nbsp;</td>
					<td class="tc3">Guest</td>
					<td class="tc4">&nbsp;</td>
					<td class="tc5">&nbsp;</td>
					<td class="tcr">&nbsp;</td>
				</tr>
<?php }
        }
    }else
        echo "\t\t\t\t" . '<tr><td class="tcl" colspan="6">The supplied IP address could not be found in the database.</td></tr>' . "\n";
    ?>
			</tbody>
			</table>
		</div>
	</div>
</div><div class="linksb">
	<div class="inbox">
		<div><?php echo _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></div>
	</div>
</div>
<?php
    require SHELL_PATH . 'footer.php';
} else if (isset($_POST['find_user'])) {
    $form = $_POST['form'];
    $form['username'] = $_POST['username'];
    // trim() all elements in $form
    $form = array_map('_trim', $form);
    $conditions = array();
    $posts_greater = trim($_POST['posts_greater']);
    $posts_less = trim($_POST['posts_less']);
    $last_post_after = trim($_POST['last_post_after']);
    $last_post_before = trim($_POST['last_post_before']);
    $registered_after = trim($_POST['registered_after']);
    $registered_before = trim($_POST['registered_before']);
    $order_by = $_POST['order_by'];
    $direction = $_POST['direction'];
    $user_group = $_POST['user_group'];
    if (preg_match('/[^0-9]/', $posts_greater . $posts_less))
        message('You entered a non-numeric value into a numeric only column.');
    // Try to convert date/time to timestamps
    if ($last_post_after != '')
        $last_post_after = strtotime($last_post_after);
    if ($last_post_before != '')
        $last_post_before = strtotime($last_post_before);
    if ($registered_after != '')
        $registered_after = strtotime($registered_after);
    if ($registered_before != '')
        $registered_before = strtotime($registered_before);
    if ($last_post_after == - 1 || $last_post_before == - 1 || $registered_after == - 1 || $registered_before == - 1)
        message('You entered an invalid date/time.');
    if ($last_post_after != '')
        $conditions[] = 'u.last_post>' . $last_post_after;
    if ($last_post_before != '')
        $conditions[] = 'u.last_post<' . $last_post_before;
    if ($registered_after != '')
        $conditions[] = 'u.createTime>' . $registered_after;
    if ($registered_before != '')
        $conditions[] = 'u.createTime<' . $registered_before;
    $like_command = ($db->type == 'pgsql') ? 'ILIKE' : 'LIKE';
    while (list($key, $input) = @each($form)) {
        if ($input != '' && in_array($key, array('username', 'email', 'title', 'realname', 'url', 'jabber', 'icq', 'msn', 'aim', 'yahoo', 'location', 'signature')))
            $conditions[] = 'u.' . $db->escape($key) . ' ' . $like_command . ' \'' . $db->escape(str_replace('*', '%', $input)) . '\'';
    }
    if ($posts_greater != '')
        $conditions[] = 'ud.num_posts>' . $posts_greater;
    if ($posts_less != '')
        $conditions[] = 'ud.num_posts<' . $posts_less;
    if ($user_group != 'all')
        $conditions[] = 'ud.forumGroupId=' . intval($user_group);
    if (empty($conditions))
        message('You didn\'t enter any search terms.');
    require SHELL_PATH . 'header.php';
    ?>
<div class="linkst">
	<div class="inbox">
		<div><?php echo _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></div>
	</div>
</div><div id="users2" class="blocktable">
	<h2><span>Users</span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
			<thead>
				<tr>
					<th class="tcl" scope="col">Username</th>
					<th class="tc2" scope="col">Email</th>
					<th class="tc3" scope="col">Title/Status</th>
					<th class="tc4" scope="col">Posts</th>
					<th class="tc5" scope="col">Admin note</th>
					<th class="tcr" scope="col">Actions</th>
				</tr>
			</thead>
			<tbody>
<?php $db->setQuery('SELECT u.id, u.username, u.email, ud.title, ud.num_posts, g.g_id, g.g_user_title FROM w3_user AS u INNER JOIN w3_user_details AS ud LEFT JOIN forum_groups AS g ON g.g_id=ud.forumGroupId WHERE u.id>1 AND ' . implode(' AND ', $conditions) . ' ORDER BY ' . $db->escape($order_by) . ' ' . $db->escape($direction)) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
    if ($db->num_rows()) {
        while ($user_data = $db->fetch_assoc()) {
            $user_title = get_title($user_data);
            // This script is a special case in that we want to display "Not verified" for non-verified users
            if (($user_data['g_id'] == '' || $user_data['g_id'] == PUN_UNVERIFIED) && $user_title != $lang_common['Banned'])
                $user_title = '<span class="warntext">Not verified</span>';
            $actions = _CHtml::link('View IP stats', array('forum/admin_users', 'ip_stats' => $user_data['id'])) . ' - ' . _CHtml::link('Show posts', array('forum/search', 'action' => 'show_user', 'user_id' => $user_data['id']));
            ?>
				<tr>
					<td class="tcl"><?php echo _CHtml::link(_CHtml::encode($user_data['username']), array('forum/profile', 'id' => $user_data['id']));?></td>
					<td class="tc2"><?php echo _CHtml::link($user_data['email'], 'mailto:' . $user_data['email']);?></td>
					<td class="tc3"><?php echo $user_title ?></td>
					<td class="tc4"><?php echo forum_number_format($user_data['num_posts']) ?></td>
					<td class="tc5">&nbsp;</td>
					<td class="tcr"><?php echo $actions ?></td>
				</tr>
<?php }
    }else
        echo "\t\t\t\t" . '<tr><td class="tcl" colspan="6">No match.</td></tr>' . "\n";
    ?>
			</tbody>
			</table>
		</div>
	</div>
</div><div class="linksb">
	<div class="inbox">
		<div><?php echo _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></div>
	</div>
</div>
<?php require SHELL_PATH . 'footer.php';
} else {
    $focus_element = array('find_user', 'username');
    require SHELL_PATH . 'header.php';
    generate_admin_menu('users');
    ?>
	<div class="blockform">
		<h2><span>User search</span></h2>
		<div class="box">
			<?php echo _CHtml::form(array('admin_users', 'action' => 'find_user'), 'POST', array('id' => 'find_user'));?>
				<p class="submittop"><input type="submit" name="find_user" value="Submit search" tabindex="1" /></p>
				<div class="inform">
					<fieldset>
						<legend>Enter search criteria</legend>
						<div class="infldset">
							<p>Search for users in the database. You can enter one or more terms to search for. Wildcards in the form of asterisks (*) are accepted.</p>
							<table class="aligntop" cellspacing="0">
								<tr>
									<th scope="row">Username</th>
									<td><input type="text" name="username" size="25" maxlength="25" tabindex="2" /></td>
								</tr>
								<tr>
									<th scope="row">Email address</th>
									<td><input type="text" name="form[email]" size="30" maxlength="50" tabindex="3" /></td>
								</tr>
								<tr>
									<th scope="row">Title</th>
									<td><input type="text" name="form[title]" size="30" maxlength="50" tabindex="4" /></td>
								</tr>
								<tr>
									<th scope="row">Real name</th>
									<td><input type="text" name="form[realname]" size="30" maxlength="40" tabindex="5" /></td>
								</tr>
								<tr>
									<th scope="row">Website</th>
									<td><input type="text" name="form[url]" size="35" maxlength="100" tabindex="6" /></td>
								</tr>
								<tr>
									<th scope="row">ICQ</th>
									<td><input type="text" name="form[icq]" size="12" maxlength="12" tabindex="7" /></td>
								</tr>
								<tr>
									<th scope="row">MSN Messenger</th>
									<td><input type="text" name="form[msn]" size="30" maxlength="50" tabindex="8" /></td>
								</tr>
								<tr>
									<th scope="row">AOL IM</th>
									<td><input type="text" name="form[aim]" size="20" maxlength="20" tabindex="9" /></td>
								</tr>
								<tr>
									<th scope="row">Yahoo! Messenger</th>
									<td><input type="text" name="form[yahoo]" size="20" maxlength="20" tabindex="10" /></td>
								</tr>
								<tr>
									<th scope="row">Location</th>
									<td><input type="text" name="form[location]" size="30" maxlength="30" tabindex="11" /></td>
								</tr>
								<tr>
									<th scope="row">Signature</th>
									<td><input type="text" name="form[signature]" size="35" maxlength="512" tabindex="12" /></td>
								</tr>
								<tr>
									<th scope="row">Number of posts greater than</th>
									<td><input type="text" name="posts_greater" size="5" maxlength="8" tabindex="14" /></td>
								</tr>
								<tr>
									<th scope="row">Number of posts less than</th>
									<td><input type="text" name="posts_less" size="5" maxlength="8" tabindex="15" /></td>
								</tr>
								<tr>
									<th scope="row">Last post is after</th>
									<td><input type="text" name="last_post_after" size="24" maxlength="19" tabindex="16" />
									<span>(yyyy-mm-dd hh:mm:ss)</span></td>
								</tr>
								<tr>
									<th scope="row">Last post is before</th>
									<td><input type="text" name="last_post_before" size="24" maxlength="19" tabindex="17" />
									<span>(yyyy-mm-dd hh:mm:ss)</span></td>
								</tr>
								<tr>
									<th scope="row">Registered after</th>
									<td><input type="text" name="registered_after" size="24" maxlength="19" tabindex="18" />
									<span>(yyyy-mm-dd hh:mm:ss)</span></td>
								</tr>
								<tr>
									<th scope="row">Registered before</th>
									<td><input type="text" name="registered_before" size="24" maxlength="19" tabindex="19" />
									<span>(yyyy-mm-dd hh:mm:ss)</span></td>
								</tr>
								<tr>
									<th scope="row">Order by</th>
									<td>
										<select name="order_by" tabindex="20">
											<option value="username" selected="selected">username</option>
											<option value="email">email</option>
											<option value="num_posts">posts</option>
											<option value="last_post">last post</option>
											<option value="registered">registered</option>
										</select>&nbsp;&nbsp;&nbsp;<select name="direction" tabindex="21">
											<option value="ASC" selected="selected">ascending</option>
											<option value="DESC">descending</option>
										</select>
									</td>
								</tr>
								<tr>
									<th scope="row">User group</th>
									<td>
										<select name="user_group" tabindex="22">
												<option value="all" selected="selected">All groups</option>
												<option value="0">Unverified users</option>
<?php $db->setQuery('SELECT g_id, g_title FROM forum_groups WHERE g_id!=' . PUN_GUEST . ' ORDER BY g_title') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());
    while ($cur_group = $db->fetch_assoc())
    echo "\t\t\t\t\t\t\t\t\t\t\t" . '<option value="' . $cur_group['g_id'] . '">' . _CHtml::encode($cur_group['g_title']) . '</option>' . "\n";
    ?>
										</select>
									</td>
								</tr>
							</table>
						</div>
					</fieldset>
				</div>
				<p class="submitend"><input type="submit" name="find_user" value="Submit search" tabindex="23" /></p>
			</form>
		</div>		<h2 class="block2"><span>IP search</span></h2>
		<div class="box">
			<?php echo _CHtml::form('admin_users', 'GET');?>
				<div class="inform">
					<fieldset>
						<legend>Enter IP to search for</legend>
						<div class="infldset">
							<table class="aligntop" cellspacing="0">
								<tr>
									<th scope="row">IP address<div><input type="submit" value=" Find " tabindex="25" /></div></th>
									<td><input type="text" name="show_users" size="18" maxlength="15" tabindex="24" />
									<span>The IP address to search for in the post database.</span></td>
								</tr>
							</table>
						</div>
					</fieldset>
				</div>
			</form>
		</div>
	</div>
	<div class="clearer"></div>
</div>
<?php require SHELL_PATH . 'footer.php';
}