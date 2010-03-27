<?php
// Tell header.php to use the admin template
define('PUN_ADMIN_CONSOLE', 1);
require SHELL_PATH . 'include/common.php';
require SHELL_PATH . 'include/common_admin.php';if ($_user['g_id'] != PUN_ADMIN)
    message($lang_common['No permission']);
// Add a new category
if (isset($_POST['add_cat']))
{
    confirm_referrer('admin_categories.php');    $new_cat_name = trim($_POST['new_cat_name']);
    if ($new_cat_name == '')
        message('You must enter a name for the category.');    $db->setQuery('INSERT INTO forum_categories (cat_name) VALUES(\'' . $db->escape($new_cat_name) . '\')')->execute() or error('Unable to create category', __FILE__, __LINE__, $db->error());    redirect('admin_categories.php', 'Category added. Redirecting &hellip;');
}
// Delete a category
else if (isset($_POST['del_cat']) || isset($_POST['del_cat_comply']))
{
    confirm_referrer('admin_categories.php');    $cat_to_delete = intval($_POST['cat_to_delete']);
    if ($cat_to_delete < 1)
        message($lang_common['Bad request']);    if (isset($_POST['del_cat_comply'])) // Delete a category with all forums and posts
        {
            @set_time_limit(0);        $db->setQuery('SELECT id FROM forum_forums WHERE cat_id=' . $cat_to_delete) or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());
        $num_forums = $db->num_rows();        for ($i = 0; $i < $num_forums; ++$i)
        {
            $cur_forum = $db->result($result, $i);
            // Prune all posts and topics
            prune($cur_forum, 1, - 1);
            // Delete the forum
            $db->setQuery('DELETE FROM forum_forums WHERE id=' . $cur_forum)->execute() or error('Unable to delete forum', __FILE__, __LINE__, $db->error());
        }
        // Locate any "orphaned redirect topics" and delete them
        $db->setQuery('SELECT t1.id FROM forum_topics AS t1 LEFT JOIN forum_topics AS t2 ON t1.moved_to=t2.id WHERE t2.id IS NULL AND t1.moved_to IS NOT NULL') or error('Unable to fetch redirect topics', __FILE__, __LINE__, $db->error());
        $num_orphans = $db->num_rows();        if ($num_orphans)
        {
            for ($i = 0; $i < $num_orphans; ++$i)
            $orphans[] = $db->result($result, $i);            $db->setQuery('DELETE FROM forum_topics WHERE id IN(' . implode(',', $orphans) . ')')->execute() or error('Unable to delete redirect topics', __FILE__, __LINE__, $db->error());
        }
        // Delete the category
        $db->setQuery('DELETE FROM forum_categories WHERE id=' . $cat_to_delete)->execute() or error('Unable to delete category', __FILE__, __LINE__, $db->error());
        // Regenerate the quick jump cache
        if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
            require SHELL_PATH . 'include/cache.php';        generate_quickjump_cache();        redirect('admin_categories.php', 'Category deleted. Redirecting &hellip;');
    }
    else // If the user hasn't comfirmed the delete
        {
            $db->setQuery('SELECT cat_name FROM forum_categories WHERE id=' . $cat_to_delete) or error('Unable to fetch category info', __FILE__, __LINE__, $db->error());
        $cat_name = $db->result();        require SHELL_PATH . 'header.php';
        generate_admin_menu('categories');        ?>
	<div class="blockform">
		<h2><span>Category delete</span></h2>
		<div class="box">
			<?php echo _CHtml::form('admin_categories', 'POST');?>
				<div class="inform">
				<input type="hidden" name="cat_to_delete" value="<?php echo $cat_to_delete ?>" />
					<fieldset>
						<legend>Confirm delete category</legend>
						<div class="infldset">
							<p>Are you sure that you want to delete the category "<?php echo _CHtml::encode($cat_name) ?>"?</p>
							<p>WARNING! Deleting a category will delete all forums and posts (if any) in that category!</p>
						</div>
					</fieldset>
				</div>
				<p><input type="submit" name="del_cat_comply" value="Delete" /><?php echo _CHtml::link($lang_common['Go back'], array('javascript:history.go(-1);'));?></p>
			</form>
		</div>
	</div>
	<div class="clearer"></div>
</div>
<?php        require SHELL_PATH . 'footer.php';
    }
}else if (isset($_POST['update'])) // Change position and name of the categories
    {
        confirm_referrer('admin_categories.php');    $cat_order = $_POST['cat_order'];
    $cat_name = $_POST['cat_name'];    $db->setQuery('SELECT id, disp_position FROM forum_categories ORDER BY disp_position') or error('Unable to fetch category list', __FILE__, __LINE__, $db->error());
    $num_cats = $db->num_rows();    for ($i = 0; $i < $num_cats; ++$i)
    {
        if ($cat_name[$i] == '')
            message('You must enter a category name.');        if (!@preg_match('#^\d+$#', $cat_order[$i]))
            message('Position must be an integer value.');        list($cat_id, $position) = $db->fetch_row();        $db->setQuery('UPDATE forum_categories SET cat_name=\'' . $db->escape($cat_name[$i]) . '\', disp_position=' . $cat_order[$i] . ' WHERE id=' . $cat_id)->execute() or error('Unable to update category', __FILE__, __LINE__, $db->error());
    }
    // Regenerate the quick jump cache
    if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
        require SHELL_PATH . 'include/cache.php';    generate_quickjump_cache();    redirect('admin_categories.php', 'Categories updated. Redirecting &hellip;');
}
// Generate an array with all categories
$db->setQuery('SELECT id, cat_name, disp_position FROM forum_categories ORDER BY disp_position') or error('Unable to fetch category list', __FILE__, __LINE__, $db->error());
$num_cats = $db->num_rows();for ($i = 0; $i < $num_cats; ++$i)
$cat_list[] = $db->fetch_row();require SHELL_PATH . 'header.php';generate_admin_menu('categories');?>
	<div class="blockform">
		<h2><span>Add categories</span></h2>
		<div class="box">
		<?php echo _CHtml::form(array('admin_categories','action'=>'foo'), 'POST');?>
			<div class="inform">
				<fieldset>
					<legend>Add categories</legend>
					<div class="infldset">
						<table class="aligntop" cellspacing="0">
							<tr>
								<th scope="row">Add a new category<div><input type="submit" name="add_cat" value="Add New" tabindex="2" /></div></th>
								<td>
									<input type="text" name="new_cat_name" size="35" maxlength="80" tabindex="1" />
									<span>The name of the new category you want to add. You can edit the name of the category later (see below). Go to <?php echo _CHtml::link('Forums', array('forum/admin_forums'));?> to add forums to your new category.</span>
								</td>
							</tr>
						</table>
					</div>
				</fieldset>
			</div>
		</form>
		</div><?php if ($num_cats): ?>		<h2 class="block2"><span>Remove categories</span></h2>
		<div class="box">
		<?php echo _CHtml::form(array('admin_categories','action'=>'foo'), 'POST');?>
			<div class="inform">
				<fieldset>
					<legend>Delete categories</legend>
					<div class="infldset">
						<table class="aligntop" cellspacing="0">
							<tr>
								<th scope="row">Delete a category<div><input type="submit" name="del_cat" value="Delete" tabindex="4" /></div></th>
								<td>
									<select name="cat_to_delete" tabindex="3">
<?php    while (list(, list($cat_id, $cat_name, ,)) = @each($cat_list))
    echo "\t\t\t\t\t\t\t\t\t\t" . '<option value="' . $cat_id . '">' . _CHtml::encode($cat_name) . '</option>' . "\n";?>
									</select>
									<span>Select the name of the category you want to delete. You will be asked to confirm your choice of category for deletion before it is deleted.</span>
								</td>
							</tr>
						</table>
					</div>
				</fieldset>
			</div>
		</form>
		</div>
<?php endif; ?><?php if ($num_cats): ?>		<h2 class="block2"><span>Edit categories</span></h2>
		<div class="box">
		<?php echo _CHtml::form(array('admin_categories','action'=>'foo'), 'POST');?>
			<div class="inform">
				<fieldset>
					<legend>Edit categories</legend>
					<div class="infldset">
						<table id="categoryedit" cellspacing="0" >
						<thead>
							<tr>
								<th class="tcl" scope="col">Name</th>
								<th scope="col">Position</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody>
<?php    @reset($cat_list);
for ($i = 0; $i < $num_cats; ++$i)
{
    list(, list($cat_id, $cat_name, $position)) = @each($cat_list);    ?>
							<tr><td><input type="text" name="cat_name[<?php echo $i ?>]" value="<?php echo _CHtml::encode($cat_name) ?>" size="35" maxlength="80" /></td><td><input type="text" name="cat_order[<?php echo $i ?>]" value="<?php echo $position ?>" size="3" maxlength="3" /></td><td>&nbsp;</td></tr>
<?php } ?>
						</tbody>
						</table>
						<div class="fsetsubmit"><input type="submit" name="update" value="Update" /></div>
					</div>
				</fieldset>
			</div>
		</form>
		</div>
<?php endif; ?>	</div>
	<div class="clearer"></div>
</div>
<?php
require SHELL_PATH . 'footer.php';