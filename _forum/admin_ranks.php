<?php
// Tell header.php to use the admin template
define('PUN_ADMIN_CONSOLE', 1);
require SHELL_PATH . 'include/common.php';
require SHELL_PATH . 'include/common_admin.php';
if ($_user['g_id'] != PUN_ADMIN)
    message($lang_common['No permission']);
// Add a rank
if (isset($_POST['add_rank'])) {
    confirm_referrer('admin_ranks.php');
    $rank = trim($_POST['new_rank']);
    $min_posts = $_POST['new_min_posts'];
    if ($rank == '')
        message('You must enter a rank title.');
    if (!@preg_match('#^\d+$#', $min_posts))
        message('Minimum posts must be a positive integer value.');
    // Make sure there isn't already a rank with the same min_posts value
    $db->setQuery('SELECT 1 FROM forum_ranks WHERE min_posts=' . $min_posts) or error('Unable to fetch rank info', __FILE__, __LINE__, $db->error());
    if ($db->num_rows())
        message('There is already a rank with a minimun posts value of ' . $min_posts . '.');
    $db->setQuery('INSERT INTO forum_ranks (rank, min_posts) VALUES(\'' . $db->escape($rank) . '\', ' . $min_posts . ')')->execute() or error('Unable to add rank', __FILE__, __LINE__, $db->error());
    // Regenerate the ranks cache
    if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
        require SHELL_PATH . 'include/cache.php';
    generate_ranks_cache();
    redirect('admin_ranks.php', 'Rank added. Redirecting &hellip;');
}
// Update a rank
else if (isset($_POST['update'])) {
    confirm_referrer('admin_ranks.php');
    $id = intval(key($_POST['update']));
    $rank = trim($_POST['rank'][$id]);
    $min_posts = trim($_POST['min_posts'][$id]);
    if ($rank == '')
        message('You must enter a rank title.');
    if (!@preg_match('#^\d+$#', $min_posts))
        message('Minimum posts must be a positive integer value.');
    // Make sure there isn't already a rank with the same min_posts value
    $db->setQuery('SELECT 1 FROM forum_ranks WHERE id!=' . $id . ' AND min_posts=' . $min_posts) or error('Unable to fetch rank info', __FILE__, __LINE__, $db->error());
    if ($db->num_rows())
        message('There is already a rank with a minimun posts value of ' . $min_posts . '.');
    $db->setQuery('UPDATE forum_ranks SET rank=\'' . $db->escape($rank) . '\', min_posts=' . $min_posts . ' WHERE id=' . $id)->execute() or error('Unable to update rank', __FILE__, __LINE__, $db->error());
    // Regenerate the ranks cache
    if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
        require SHELL_PATH . 'include/cache.php';
    generate_ranks_cache();
    redirect('admin_ranks.php', 'Rank updated. Redirecting &hellip;');
}
// Remove a rank
else if (isset($_POST['remove'])) {
    confirm_referrer('admin_ranks.php');
    $id = intval(key($_POST['remove']));
    $db->setQuery('DELETE FROM forum_ranks WHERE id=' . $id)->execute() or error('Unable to delete rank', __FILE__, __LINE__, $db->error());
    // Regenerate the ranks cache
    if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
        require SHELL_PATH . 'include/cache.php';
    generate_ranks_cache();
    redirect('admin_ranks.php', 'Rank removed. Redirecting &hellip;');
}
$focus_element = array('ranks', 'new_rank');
require SHELL_PATH . 'header.php';
generate_admin_menu('ranks');
?>
	<div class="blockform">
		<h2><span>Ranks</span></h2>
		<div class="box">
			<?php echo _CHtml::form(array('admin_ranks', 'action' => 'foo'), 'POST', array('id' => 'ranks'));?>
				<div class="inform">
					<fieldset>
						<legend>Add rank</legend>
						<div class="infldset">
							<p>Enter a rank and the minimum number of posts that a user has to have to aquire the rank. Different ranks cannot have the same value for minimum posts. If a title is set for a user, the title will be displayed instead of any rank. <strong>User ranks must be enabled in <?php echo _CHtml::link('Options', array('forum/admin_options#ranks'));?> for this to have any effect.</strong></p>
							<table cellspacing="0">
							<thead>
								<tr>
									<th class="tcl" scope="col">Rank&nbsp;title</th>
									<th class="tc2" scope="col">Minimum&nbsp;posts</th>

									<th class="hidehead" scope="col">Action</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><input type="text" name="new_rank" size="24" maxlength="50" tabindex="1" /></td>
									<td><input type="text" name="new_min_posts" size="7" maxlength="7" tabindex="2" /></td>
									<td><input type="submit" name="add_rank" value=" Add " tabindex="3" /></td>
								</tr>
							</tbody>
							</table>
						</div>
					</fieldset>
				</div>
				<div class="inform">
					<fieldset>
						<legend>Edit/remove ranks</legend>
						<div class="infldset">
<?php $db->setQuery('SELECT id, rank, min_posts FROM forum_ranks ORDER BY min_posts') or error('Unable to fetch rank list', __FILE__, __LINE__, $db->error());
if ($db->num_rows()) {?>
							<table cellspacing="0">
							<thead>
								<tr>
									<th class="tcl" scope="col"><strong>Rank&nbsp;title</strong></th>
									<th class="tc2" scope="col"><strong>Minimum&nbsp;Posts</strong></th>
									<th class="hidehead" scope="col">Actions</th>
								</tr>
							</thead>
							<tbody>
<?php while ($cur_rank = $db->fetch_assoc())
    echo "\t\t\t\t\t\t\t\t" . '<tr><td><input type="text" name="rank[' . $cur_rank['id'] . ']" value="' . _CHtml::encode($cur_rank['rank']) . '" size="24" maxlength="50" /></td><td><input type="text" name="min_posts[' . $cur_rank['id'] . ']" value="' . $cur_rank['min_posts'] . '" size="7" maxlength="7" /></td><td><input type="submit" name="update[' . $cur_rank['id'] . ']" value="Update" />&nbsp;<input type="submit" name="remove[' . $cur_rank['id'] . ']" value="Remove" /></td></tr>' . "\n";
    ?>
							</tbody>
							</table>
<?php }else
    echo "\t\t\t\t\t\t\t" . '<p>No ranks in list.</p>' . "\n";
?>
						</div>
					</fieldset>
				</div>
			</form>
		</div>
	</div>
	<div class="clearer"></div>
</div>
<?php require SHELL_PATH . 'footer.php';