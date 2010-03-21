<?php

/*---

	Copyright (C) 2008-2009 FluxBB.org
	based on code copyright (C) 2002-2005 Rickard Andersson
	License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher

---*/
// Tell header.php to use the admin template
define('PUN_ADMIN_CONSOLE', 1);

require SHELL_PATH . 'include/common.php';
require SHELL_PATH . 'include/common_admin.php';

if (!$pun_user['is_admmod'])
    message($lang_common['No permission']);
// Zap a report
if (isset($_POST['zap_id']))
{
    confirm_referrer('admin_reports.php');

    $zap_id = intval(key($_POST['zap_id']));

    $db->setQuery('SELECT zapped FROM ' . $db->tablePrefix . 'reports WHERE id=' . $zap_id) or error('Unable to fetch report info', __FILE__, __LINE__, $db->error());
    $zapped = $db->result($result);

    if ($zapped == '')
        $db->setQuery('UPDATE ' . $db->tablePrefix . 'reports SET zapped=' . time() . ', zapped_by=' . $pun_user['id'] . ' WHERE id=' . $zap_id) or error('Unable to zap report', __FILE__, __LINE__, $db->error());

    redirect('admin_reports.php', 'Report zapped. Redirecting &hellip;');
}

$page_title = pun_htmlspecialchars($pun_config['o_board_title']) . ' / Admin / Reports';
require SHELL_PATH . 'header.php';

generate_admin_menu('reports');

?>
	<div class="blockform">
		<h2><span>New reports</span></h2>
		<div class="box">
			<?php echo CHtml::form(array('admin_reports','action'=>'zap'), 'POST');
$db->setQuery('SELECT r.id, r.topic_id, r.forum_id, r.reported_by, r.created, r.message, p.id AS pid, t.subject, f.forum_name, u.username AS reporter FROM ' . $db->tablePrefix . 'reports AS r LEFT JOIN ' . $db->tablePrefix . 'posts AS p ON r.post_id=p.id LEFT JOIN ' . $db->tablePrefix . 'topics AS t ON r.topic_id=t.id LEFT JOIN ' . $db->tablePrefix . 'forums AS f ON r.forum_id=f.id LEFT JOIN ' . $db->tablePrefix . 'users AS u ON r.reported_by=u.id WHERE r.zapped IS NULL ORDER BY created DESC') or error('Unable to fetch report list', __FILE__, __LINE__, $db->error());

if ($db->num_rows())
{
    while ($cur_report = $db->fetch_assoc())
    {
        $reporter = ($cur_report['reporter'] != '') ? CHtml::link(pun_htmlspecialchars($cur_report['reporter']), array('forum/profile', 'id' => $cur_report['reported_by'])) : 'Deleted user';
        $forum = ($cur_report['forum_name'] != '') ? CHtml::link(pun_htmlspecialchars($cur_report['forum_name']), array('forum/viewforum', 'id' => $cur_report['forum_id'])) : 'Deleted';
        $topic = ($cur_report['subject'] != '') ? CHtml::link(pun_htmlspecialchars($cur_report['subject']), array('forum/viewtopic', 'id' => $cur_report['topic_id'])) : 'Deleted';
        $post = str_replace("\n", '<br />', pun_htmlspecialchars($cur_report['message']));
        $postid = ($cur_report['pid'] != '') ? CHtml::link('Post #' . $cur_report['pid'], array('forum/viewtopic', 'pid' => $cur_report['pid'] . '#p' . $cur_report['pid'])) : 'Deleted';

        ?>
				<div class="inform">
					<fieldset>
						<legend>Reported <?php echo format_time($cur_report['created']) ?></legend>
						<div class="infldset">
							<table cellspacing="0">
								<tr>
									<th scope="row">Forum&nbsp;&raquo;&nbsp;Topic&nbsp;&raquo;&nbsp;Post</th>
									<td><?php echo $forum ?>&nbsp;&raquo;&nbsp;<?php echo $topic ?>&nbsp;&raquo;&nbsp;<?php echo $postid ?></td>
								</tr>
								<tr>
									<th scope="row">Report by <?php echo $reporter ?><div><input type="submit" name="zap_id[<?php echo $cur_report['id'] ?>]" value=" Zap " /></div></th>
									<td><?php echo $post ?></td>
								</tr>
							</table>
						</div>
					</fieldset>
				</div>
<?php

    }
}
else
    echo "\t\t\t\t" . '<p>There are no new reports.</p>' . "\n";

?>
			</form>
		</div>
	</div>

	<div class="blockform block2">
		<h2><span>10 last zapped reports</span></h2>
		<div class="box">
			<div class="fakeform">
<?php

$db->setQuery('SELECT r.id, r.topic_id, r.forum_id, r.reported_by, r.message, r.zapped, r.zapped_by AS zapped_by_id, p.id AS pid, t.subject, f.forum_name, u.username AS reporter, u2.username AS zapped_by FROM ' . $db->tablePrefix . 'reports AS r LEFT JOIN ' . $db->tablePrefix . 'posts AS p ON r.post_id=p.id LEFT JOIN ' . $db->tablePrefix . 'topics AS t ON r.topic_id=t.id LEFT JOIN ' . $db->tablePrefix . 'forums AS f ON r.forum_id=f.id LEFT JOIN ' . $db->tablePrefix . 'users AS u ON r.reported_by=u.id LEFT JOIN ' . $db->tablePrefix . 'users AS u2 ON r.zapped_by=u2.id WHERE r.zapped IS NOT NULL ORDER BY zapped DESC LIMIT 10') or error('Unable to fetch report list', __FILE__, __LINE__, $db->error());

if ($db->num_rows())
{
    while ($cur_report = $db->fetch_assoc())
    {
        $reporter = ($cur_report['reporter'] != '') ? CHtml::link(pun_htmlspecialchars($cur_report['reporter']), array('forum/profile', 'id' => $cur_report['reported_by'])) : 'Deleted user';
        $forum = ($cur_report['forum_name'] != '') ? CHtml::link(pun_htmlspecialchars($cur_report['forum_name']), array('forum/viewforum', 'id' => $cur_report['forum_id'])) : 'Deleted';
        $topic = ($cur_report['subject'] != '') ? CHtml::link(pun_htmlspecialchars($cur_report['subject']), array('forum/viewtopic', 'id' => $cur_report['topic_id'])) : 'Deleted';
        $post = str_replace("\n", '<br />', pun_htmlspecialchars($cur_report['message']));
        $post_id = ($cur_report['pid'] != '') ? CHtml::link('Post #' . $cur_report['pid'], array('forum/viewtopic', 'pid' => $cur_report['pid'] . '#p' . $cur_report['pid'])) : 'Deleted';
        $zapped_by = ($cur_report['zapped_by'] != '') ? CHtml::link(pun_htmlspecialchars($cur_report['zapped_by']), array('forum/profile', 'id' => $cur_report['zapped_by_id'])) : 'N/A';

        ?>
				<div class="inform">
					<fieldset>
						<legend>Zapped <?php echo format_time($cur_report['zapped']) ?></legend>
						<div class="infldset">
							<table cellspacing="0">
								<tr>
									<th scope="row">Forum&nbsp;&raquo;&nbsp;Topic&nbsp;&raquo;&nbsp;Post</th>
									<td><?php echo $forum ?>&nbsp;&raquo;&nbsp;<?php echo $topic ?>&nbsp;&raquo;&nbsp;<?php echo $post_id ?></td>
								</tr>
								<tr>
									<th scope="row">Reported by <?php echo $reporter ?><div class="topspace">Zapped by <?php echo $zapped_by ?></div></th>
									<td><?php echo $post ?></td>
								</tr>
							</table>
						</div>
					</fieldset>
				</div>
<?php

    }
}
else
    echo "\t\t\t\t" . '<p>There are no zapped reports.</p>' . "\n";

?>
			</div>
		</div>
	</div>
	<div class="clearer"></div>
</div>
<?php

require SHELL_PATH . 'footer.php';