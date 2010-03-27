<?php
// Tell header.php to use the admin template
define('PUN_ADMIN_CONSOLE', 1);require SHELL_PATH . 'include/common.php';
require SHELL_PATH . 'include/common_admin.php';if ($_user['g_id'] != PUN_ADMIN)
    message($lang_common['No permission']);if (isset($_GET['action']) || isset($_POST['prune']) || isset($_POST['prune_comply']))
{
    if (isset($_POST['prune_comply']))
    {
        confirm_referrer('admin_prune.php');        $prune_from = $_POST['prune_from'];
        $prune_sticky = isset($_POST['prune_sticky']) ? '1' : '0';
        $prune_days = intval($_POST['prune_days']);
        $prune_date = ($prune_days) ? time() - ($prune_days * 86400) : - 1;        @set_time_limit(0);        if ($prune_from == 'all')
        {
            $db->setQuery('SELECT id FROM forum_forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());
            $num_forums = $db->num_rows();            for ($i = 0; $i < $num_forums; ++$i)
            {
                $fid = $db->result($result, $i);                prune($fid, $prune_sticky, $prune_date);
                update_forum($fid);
            }
        }
        else
        {
            $prune_from = intval($prune_from);
            prune($prune_from, $prune_sticky, $prune_date);
            update_forum($prune_from);
        }
        // Locate any "orphaned redirect topics" and delete them
        $db->setQuery('SELECT t1.id FROM forum_topics AS t1 LEFT JOIN forum_topics AS t2 ON t1.moved_to=t2.id WHERE t2.id IS NULL AND t1.moved_to IS NOT NULL') or error('Unable to fetch redirect topics', __FILE__, __LINE__, $db->error());
        $num_orphans = $db->num_rows();        if ($num_orphans)
        {
            for ($i = 0; $i < $num_orphans; ++$i)
            $orphans[] = $db->result($result, $i);            $db->setQuery('DELETE FROM forum_topics WHERE id IN(' . implode(',', $orphans) . ')')->execute() or error('Unable to delete redirect topics', __FILE__, __LINE__, $db->error());
        }        redirect('admin_prune.php', 'Posts pruned. Redirecting &hellip;');
    }    $prune_days = $_POST['req_prune_days'];
    if (!@preg_match('#^\d+$#', $prune_days))
        message('Days to prune must be a positive integer.');    $prune_date = time() - ($prune_days * 86400);
    $prune_from = $_POST['prune_from'];
    // Concatenate together the query for counting number of topics to prune
    $sql = 'SELECT COUNT(id) FROM forum_topics WHERE last_post<' . $prune_date . ' AND moved_to IS NULL';    if (!$prune_sticky)
        $sql .= ' AND sticky=\'0\'';    if ($prune_from != 'all')
    {
        $prune_from = intval($prune_from);
        $sql .= ' AND forum_id=' . $prune_from;
        // Fetch the forum name (just for cosmetic reasons)
        $db->setQuery('SELECT forum_name FROM forum_forums WHERE id=' . $prune_from) or error('Unable to fetch forum name', __FILE__, __LINE__, $db->error());
        $forum = '"' . _CHtml::encode($db->result()) . '"';
    }
    else
        $forum = 'all forums';    $db->setQuery($sql) or error('Unable to fetch topic prune count', __FILE__, __LINE__, $db->error());
    $num_topics = $db->result();    if (!$num_topics)
        message('There are no topics that are ' . $prune_days . ' days old. Please decrease the value of "Days old" and try again.');    require SHELL_PATH . 'header.php';
    generate_admin_menu('prune');    ?>
	<div class="blockform">
		<h2><span>Prune</span></h2>
		<div class="box">
			<?php echo _CHtml::form(array('admin_prune','action'=>'foo'), 'POST');?>
				<div class="inform">
					<input type="hidden" name="prune_days" value="<?php echo $prune_days ?>" />
					<input type="hidden" name="prune_sticky" value="<?php echo $prune_sticky ?>" />
					<input type="hidden" name="prune_from" value="<?php echo $prune_from ?>" />
					<fieldset>
						<legend>Confirm prune posts</legend>
						<div class="infldset">
							<p>Are you sure that you want to prune all topics older than <?php echo $prune_days ?> days from <?php echo $forum ?>? (<?php echo $num_topics ?> topics)</p>
							<p>WARNING! Pruning posts deletes them permanently.</p>
						</div>
					</fieldset>
				</div>
				<p><input type="submit" name="prune_comply" value="Prune" /><?php echo _CHtml::link($lang_common['Go back'], 'javascript:history.go(-1);');?></p>
			</form>
		</div>
	</div>
	<div class="clearer"></div>
</div>
<?php    require SHELL_PATH . 'footer.php';
}else
{
    $required_fields = array('req_prune_days' => 'Days old');
    $focus_element = array('prune', 'req_prune_days');
    require SHELL_PATH . 'header.php';
    generate_admin_menu('prune');    ?>
	<div class="blockform">
		<h2><span>Prune</span></h2>
		<div class="box">
			<?php echo _CHtml::form(array('admin_prune','action'=>'foo'), 'POST', array('id'=>'prune','onsubmit'=>'return process_form(this);'));?>
				<div class="inform">
				<input type="hidden" name="form_sent" value="1" />
					<fieldset>
						<legend>Prune old posts</legend>
						<div class="infldset">
							<table class="aligntop" cellspacing="0">
								<tr>
									<th scope="row">Days old</th>
									<td>
										<input type="text" name="req_prune_days" size="3" maxlength="3" tabindex="1" />
										<span>The number of days "old" a topic must be to be pruned. E.g. if you were to enter 30, every topic that didn't contain a post dated less than 30 days old would be deleted.</span>
									</td>
								</tr>
								<tr>
									<th scope="row">Prune sticky topics</th>
									<td>
										<input type="radio" name="prune_sticky" value="1" tabindex="2" checked="checked" />&nbsp;<strong>Yes</strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="prune_sticky" value="0" />&nbsp;<strong>No</strong>
										<span>When enabled sticky topics will also be pruned.</span>
									</td>
								</tr>
								<tr>
									<th scope="row">Prune from forum</th>
									<td>
										<select name="prune_from" tabindex="3">
											<option value="all">All forums</option>
<?php    $db->setQuery('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name FROM forum_categories AS c INNER JOIN forum_forums AS f ON c.id=f.cat_id WHERE f.redirect_url IS NULL ORDER BY c.disp_position, c.id, f.disp_position') or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());    $cur_category = 0;
    while ($forum = $db->fetch_assoc())
    {
        if ($forum['cid'] != $cur_category) // Are we still in the same category?
            {
                if ($cur_category)
                    echo "\t\t\t\t\t\t\t\t\t\t\t" . '</optgroup>' . "\n";                echo "\t\t\t\t\t\t\t\t\t\t\t" . '<optgroup label="' . _CHtml::encode($forum['cat_name']) . '">' . "\n";
                $cur_category = $forum['cid'];
            }            echo "\t\t\t\t\t\t\t\t\t\t\t\t" . '<option value="' . $forum['fid'] . '">' . _CHtml::encode($forum['forum_name']) . '</option>' . "\n";
        }        ?>
											</optgroup>
										</select>
										<span>The forum from which you want to prune posts.</span>
									</td>
								</tr>
							</table>
							<p class="topspace">Use this feature with caution. Pruned posts can <strong>never</strong> be recovered. For best performance you should put the forum in maintenance mode during pruning.</p>
							<div class="fsetsubmit"><input type="submit" name="prune" value="Prune" tabindex="5" /></div>
						</div>
					</fieldset>
				</div>
			</form>
		</div>
	</div>
	<div class="clearer"></div>
</div>
<?php        require SHELL_PATH . 'footer.php';
    }