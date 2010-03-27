<?php
// The contents of this file are very much inspired by the file search.php
// from the phpBB Group forum software phpBB2 (http://www.phpbb.com)
require SHELL_PATH . 'include/common.php';
// Load the search.php language file
require SHELL_PATH . 'lang/' . $_user['language'] . '/search.php';
require SHELL_PATH . 'lang/' . $_user['language'] . '/forum.php';
Yii::app()->getClientScript()->registerScript('focus', 'document.getElementById(\'search\').keywords.focus()', POS_LOAD);

if ($_user['g_read_board'] == '0')
    message($lang_common['No view']);
else if ($_user['g_search'] == '0')
    message($lang_search['No search permission']);
// Figure out what to do :-)
if (isset($_GET['action']) || isset($_GET['search_id'])) {
    $action = (isset($_GET['action'])) ? $_GET['action'] : null;
    $forum = (isset($_GET['forum'])) ? intval($_GET['forum']) : - 1;
    $sort_dir = (isset($_GET['sort_dir'])) ? (($_GET['sort_dir'] == 'DESC') ? 'DESC' : 'ASC') : 'DESC';
    // If a search_id was supplied
    if (isset($_GET['search_id'])) {
        $search_id = intval($_GET['search_id']);
        if ($search_id < 1)
            message($lang_common['Bad request']);
    }
    // If it's a regular search (keywords and/or author)
    else if ($action == 'search') {
        $keywords = (isset($_GET['keywords'])) ? strtolower(trim($_GET['keywords'])) : null;
        $author = (isset($_GET['author'])) ? strtolower(trim($_GET['author'])) : null;

        if (preg_match('#^[\*%]+$#', $keywords) || strlen(str_replace(array('*', '%'), '', $keywords)) < 3)
            $keywords = '';

        if (preg_match('#^[\*%]+$#', $author) || strlen(str_replace(array('*', '%'), '', $author)) < 2)
            $author = '';

        if (!$keywords && !$author)
            message($lang_search['No terms']);

        if ($author)
            $author = str_replace('*', '%', $author);

        $show_as = (isset($_GET['show_as'])) ? $_GET['show_as'] : 'posts';
        $sort_by = (isset($_GET['sort_by'])) ? intval($_GET['sort_by']) : null;
        $search_in = (!isset($_GET['search_in']) || $_GET['search_in'] == 'all') ? 0 : (($_GET['search_in'] == 'message') ? 1 : - 1);
    }
    // If it's a user search (by ID)
    else if ($action == 'show_user') {
        $user_id = (isset($_GET['user_id'])) ? intval($_GET['user_id']) : 0;
        if ($user_id < 2)
            message($lang_common['Bad request']);
    }else {
        if ($action != 'show_new' && $action != 'show_24h' && $action != 'show_unanswered' && $action != 'show_subscriptions')
            message($lang_common['Bad request']);
    }
    // If a valid search_id was supplied we attempt to fetch the search results from the db
    if (isset($search_id)) {
        $ident = ($_user['is_guest']) ? get_remote_address() : $_user['username'];

        $db->setQuery('SELECT search_data FROM forum_search_cache WHERE id=' . $search_id . ' AND ident=\'' . $db->escape($ident) . '\'') or error('Unable to fetch search results', __FILE__, __LINE__, $db->error());
        if ($row = $db->fetch_assoc()) {
            $temp = unserialize($row['search_data']);

            $search_results = $temp['search_results'];
            $num_hits = $temp['num_hits'];
            $sort_by = $temp['sort_by'];
            $sort_dir = $temp['sort_dir'];
            $show_as = $temp['show_as'];

            unset($temp);
        }else
            message($lang_search['No hits']);
    }else {
        $keyword_results = $author_results = array();
        // Search a specific forum?
        $forum_sql = ($forum != - 1 || ($forum == - 1 && $_config['o_search_all_forums'] == '0' && !$_user['is_admmod'])) ? ' AND t.forum_id = ' . $forum : '';

        if (!empty($author) || !empty($keywords)) {
            // Flood protection
            if ($_user['last_search'] && (time() - $_user['last_search']) < $_user['g_search_flood'] && (time() - $_user['last_search']) >= 0)
                message(sprintf($lang_search['Search flood'], $_user['g_search_flood']));

            if (!$_user['is_guest']) {
                $db->setQuery('UPDATE w3_user SET last_search=' . time() . ' WHERE id=' . $_user['id']) or error('Unable to update user', __FILE__, __LINE__, $db->error());
            }else {
                $db->setQuery('UPDATE forum_online SET last_search=' . time() . ' WHERE ident=\'' . $db->escape(get_remote_address()) . '\'') or error('Unable to update user', __FILE__, __LINE__, $db->error());
            }
            // If it's a search for keywords
            if ($keywords) {
                $stopwords = (array)@file(SHELL_PATH . 'lang/' . $_user['language'] . '/stopwords.txt');
                $stopwords = array_map('_trim', $stopwords);
                // Remove any apostrophes which aren't part of words
                $keywords = substr(preg_replace('((?<=\W)\'|\'(?=\W))', '', ' ' . $keywords . ' '), 1, - 1);
                // Remove symbols and multiple whitespace
                $keywords = preg_replace('/[\^\$&\(\)<>`"\|,@_\?%~\+\[\]{}:=\/#\\\\;!\.\s]+/', ' ', $keywords);
                // Fill an array with all the words
                $keywords_array = array_unique(explode(' ', $keywords));

                if (empty($keywords_array))
                    message($lang_search['No hits']);

                while (list($i, $word) = @each($keywords_array)) {
                    $num_chars = _strlen($word);

                    if ($word !== 'or' && ($num_chars < 3 || $num_chars > 20 || in_array($word, $stopwords)))
                        unset($keywords_array[$i]);
                }
                // Should we search in message body or topic subject specifically?
                $search_in_cond = ($search_in) ? (($search_in > 0) ? ' AND m.subject_match = 0' : ' AND m.subject_match = 1') : '';

                $word_count = 0;
                $match_type = 'and';
                $result_list = array();
                @reset($keywords_array);
                while (list(, $cur_word) = @each($keywords_array)) {
                    switch ($cur_word) {
                        case 'and':
                        case 'or':
                        case 'not':
                            $match_type = $cur_word;
                            break;

                        default: {
                                $db->setQuery('SELECT m.post_id FROM forum_search_words AS w INNER JOIN forum_search_matches AS m ON m.word_id = w.id WHERE w.word LIKE \'' . str_replace('*', '%', $cur_word) . '\'' . $search_in_cond, true) or error('Unable to search for posts', __FILE__, __LINE__, $db->error());

                                $row = array();
                                while ($temp = $db->fetch_row()) {
                                    $row[$temp[0]] = 1;

                                    if (!$word_count)
                                        $result_list[$temp[0]] = 1;
                                    else if ($match_type == 'or')
                                        $result_list[$temp[0]] = 1;
                                    else if ($match_type == 'not')
                                        $result_list[$temp[0]] = 0;
                                }

                                if ($match_type == 'and' && $word_count) {
                                    @reset($result_list);
                                    while (list($post_id,) = @each($result_list)) {
                                        if (!isset($row[$post_id]))
                                            $result_list[$post_id] = 0;
                                    }
                                }

                                ++$word_count;
                                break;
                            }
                    }
                }

                @reset($result_list);
                while (list($post_id, $matches) = @each($result_list)) {
                    if ($matches)
                        $keyword_results[] = $post_id;
                }

                unset($result_list);
            }
            // If it's a search for author name (and that author name isn't Guest)
            if ($author && strcasecmp($author, 'Guest') && strcasecmp($author, $lang_common['Guest'])) {
                switch ($db->type) {
                    case 'pgsql':
                        $db->setQuery('SELECT id FROM w3_user WHERE username ILIKE \'' . $db->escape($author) . '\'') or error('Unable to fetch users', __FILE__, __LINE__, $db->error());
                        break;

                    default:
                        $db->setQuery('SELECT id FROM w3_user WHERE username LIKE \'' . $db->escape($author) . '\'') or error('Unable to fetch users', __FILE__, __LINE__, $db->error());
                        break;
                }

                if ($db->num_rows()) {
                    $user_ids = '';
                    while ($row = $db->fetch_row())
                    $user_ids .= (($user_ids != '') ? ',' : '') . $row[0];

                    $db->setQuery('SELECT id FROM forum_posts WHERE poster_id IN(' . $user_ids . ')') or error('Unable to fetch matched posts list', __FILE__, __LINE__, $db->error());

                    $search_ids = array();
                    while ($row = $db->fetch_row())
                    $author_results[] = $row[0];
                }
            }

            if ($author && $keywords) {
                // If we searched for both keywords and author name we want the intersection between the results
                $search_ids = array_intersect($keyword_results, $author_results);
                unset($keyword_results, $author_results);
            }else if ($keywords)
                $search_ids = $keyword_results;
            else
                $search_ids = $author_results;

            $num_hits = count($search_ids);
            if (!$num_hits)
                message($lang_search['No hits']);

            if ($show_as == 'topics') {
                $db->setQuery('SELECT t.id FROM forum_posts AS p INNER JOIN forum_topics AS t ON t.id=p.topic_id INNER JOIN forum_forums AS f ON f.id=t.forum_id LEFT JOIN forum_forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=' . $_user['g_id'] . ') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND p.id IN(' . implode(',', $search_ids) . ')' . $forum_sql . ' GROUP BY t.id', true) or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
                $search_ids = array();
                while ($row = $db->fetch_row())
                $search_ids[] = $row[0];
                $num_hits = count($search_ids);
            }else {
                $db->setQuery('SELECT p.id FROM forum_posts AS p INNER JOIN forum_topics AS t ON t.id=p.topic_id INNER JOIN forum_forums AS f ON f.id=t.forum_id LEFT JOIN forum_forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=' . $_user['g_id'] . ') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND p.id IN(' . implode(',', $search_ids) . ')' . $forum_sql, true) or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());

                $search_ids = array();
                while ($row = $db->fetch_row())
                $search_ids[] = $row[0];
                $num_hits = count($search_ids);
            }
        }else if ($action == 'show_new' || $action == 'show_24h' || $action == 'show_user' || $action == 'show_subscriptions' || $action == 'show_unanswered') {
            // If it's a search for new posts
            if ($action == 'show_new') {
                if ($_user['is_guest'])
                    message($lang_common['No permission']);

                $db->setQuery('SELECT t.id FROM forum_topics AS t INNER JOIN forum_forums AS f ON f.id=t.forum_id LEFT JOIN forum_forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=' . $_user['g_id'] . ') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.last_post>' . $_user['last_visit'] . ' AND t.moved_to IS NULL') or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
                $num_hits = $db->num_rows();

                if (!$num_hits)
                    message($lang_search['No new posts']);
            }
            // If it's a search for todays posts
            else if ($action == 'show_24h') {
                $db->setQuery('SELECT t.id FROM forum_topics AS t INNER JOIN forum_forums AS f ON f.id=t.forum_id LEFT JOIN forum_forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=' . $_user['g_id'] . ') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.last_post>' . (time() - 86400) . ' AND t.moved_to IS NULL') or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
                $num_hits = $db->num_rows();

                if (!$num_hits)
                    message($lang_search['No recent posts']);
            }
            // If it's a search for posts by a specific user ID
            else if ($action == 'show_user') {
                $db->setQuery('SELECT t.id FROM forum_topics AS t INNER JOIN forum_posts AS p ON t.id=p.topic_id INNER JOIN forum_forums AS f ON f.id=t.forum_id LEFT JOIN forum_forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=' . $_user['g_id'] . ') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND p.poster_id=' . $user_id . ' GROUP BY t.id') or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
                $num_hits = $db->num_rows();

                if (!$num_hits)
                    message($lang_search['No user posts']);
            }
            // If it's a search for subscribed topics
            else if ($action == 'show_subscriptions') {
                if ($_user['is_guest'])
                    message($lang_common['Bad request']);

                $db->setQuery('SELECT t.id FROM forum_topics AS t INNER JOIN forum_subscriptions AS s ON (t.id=s.topic_id AND s.user_id=' . $_user['id'] . ') INNER JOIN forum_forums AS f ON f.id=t.forum_id LEFT JOIN forum_forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=' . $_user['g_id'] . ') WHERE (fp.read_forum IS NULL OR fp.read_forum=1)') or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
                $num_hits = $db->num_rows();

                if (!$num_hits)
                    message($lang_search['No subscriptions']);
            }
            // If it's a search for unanswered posts
            else {
                $db->setQuery('SELECT t.id FROM forum_topics AS t INNER JOIN forum_forums AS f ON f.id=t.forum_id LEFT JOIN forum_forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=' . $_user['g_id'] . ') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.num_replies=0 AND t.moved_to IS NULL') or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
                $num_hits = $db->num_rows();

                if (!$num_hits)
                    message($lang_search['No unanswered']);
            }
            // We want to sort things after last post
            $sort_by = 4;
            $search_ids = array();
            while ($row = $db->fetch_row())
            $search_ids[] = $row[0];
            $show_as = 'topics';
        }else
            message($lang_common['Bad request']);
        // Prune "old" search results
        $old_searches = array();
        $db->setQuery('SELECT ident FROM forum_online') or error('Unable to fetch online list', __FILE__, __LINE__, $db->error());

        if ($db->num_rows()) {
            while ($row = $db->fetch_row())
            $old_searches[] = '\'' . $db->escape($row[0]) . '\'';

            $db->setQuery('DELETE FROM forum_search_cache WHERE ident NOT IN(' . implode(',', $old_searches) . ')')->execute() or error('Unable to delete search results', __FILE__, __LINE__, $db->error());
        }
        // Final search results
        $search_results = implode(',', $search_ids);
        // Fill an array with our results and search properties
        $temp['search_results'] = $search_results;
        $temp['num_hits'] = $num_hits;
        $temp['sort_by'] = $sort_by;
        $temp['sort_dir'] = $sort_dir;
        $temp['show_as'] = $show_as;
        $temp = serialize($temp);
        $search_id = mt_rand(1, 2147483647);

        $ident = ($_user['is_guest']) ? get_remote_address() : $_user['username'];

        $db->setQuery('INSERT INTO forum_search_cache (id, ident, search_data) VALUES(' . $search_id . ', \'' . $db->escape($ident) . '\', \'' . $db->escape($temp) . '\')')->execute() or error('Unable to insert search results', __FILE__, __LINE__, $db->error());

        if ($action != 'show_new' && $action != 'show_24h') {
            $db->close();
            // Redirect the user to the cached result page
            Yii::app()->request->redirect(Yii::app()->createUrl('forum/search', array('search_id' => $search_id)));
        }
    }
    // Fetch results to display
    if ($search_results != '') {
        switch ($sort_by) {
            case 1:
                $sort_by_sql = ($show_as == 'topics') ? 't.poster' : 'p.poster';
                break;

            case 2:
                $sort_by_sql = 't.subject';
                break;

            case 3:
                $sort_by_sql = 't.forum_id';
                break;

            case 4:
                $sort_by_sql = 't.last_post';
                break;

            default:
                $sort_by_sql = ($show_as == 'topics') ? 't.last_post' : 'p.posted';
                break;
        }

        if ($show_as == 'posts') {
            $sql = 'SELECT p.id AS pid, p.poster AS pposter, p.posted AS pposted, p.poster_id, p.message, p.hide_smilies, t.id AS tid, t.poster, t.subject, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.forum_id FROM forum_posts AS p INNER JOIN forum_topics AS t ON t.id=p.topic_id WHERE p.id IN(' . $search_results . ') ORDER BY ' . $sort_by_sql;
        }else
            $sql = 'SELECT t.id AS tid, t.poster, t.subject, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.forum_id FROM forum_topics AS t WHERE t.id IN(' . $search_results . ') ORDER BY ' . $sort_by_sql;
        // Determine the topic or post offset (based on $_GET['p'])
        $per_page = ($show_as == 'posts') ? $_user['disp_posts'] : $_user['disp_topics'];
        $num_pages = ceil($num_hits / $per_page);

        $p = (!isset($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $num_pages) ? 1 : intval($_GET['p']);
        $start_from = $per_page * ($p - 1);
        // Generate paging links
        $paging_links = $lang_common['Pages'] . ': ' . paginate($num_pages, $p, 'search/search_id/' . $search_id);

        $sql .= ' ' . $sort_dir . ' LIMIT ' . $start_from . ', ' . $per_page;

        $db->setQuery($sql) or error('Unable to fetch search results', __FILE__, __LINE__, $db->error());

        $search_set = array();
        while ($row = $db->fetch_assoc())
        $search_set[] = $row;
        $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_search['Search results'];
        require SHELL_PATH . 'header.php';

        ?>
<div class="linkst">
	<div class="inbox">
		<p class="pagelink"><?php echo $paging_links ?></p>
		<div class="clearer"></div>
	</div>
</div>

<?php
        // Set background switching on for show as posts
        $bg_switch = true;

        if ($show_as == 'topics') {
            // Get topic/forum tracking data
            if (!$_user['is_guest'])
                $tracked_topics = get_tracked_topics();

            ?>
<div id="vf" class="blocktable">
	<h2><span><?php echo $lang_search['Search results'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
			<thead>
				<tr>
					<th class="tcl" scope="col"><?php echo $lang_common['Topic'] ?></th>
					<th class="tc2" scope="col"><?php echo $lang_common['Forum'] ?></th>
					<th class="tc3" scope="col"><?php echo $lang_common['Replies'] ?></th>
					<th class="tcr" scope="col"><?php echo $lang_common['Last post'] ?></th>
				</tr>
			</thead>
			<tbody>
<?php

        }

        if ($show_as == 'posts') {
            require SHELL_PATH . 'include/parser.php';
            $post_count = 0;
        }
        // Fetch the list of forums
        $db->setQuery('SELECT id, forum_name FROM forum_forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

        $forum_list = array();
        while ($forum_list[] = $db->fetch_row());
        // Finally, lets loop through the results and output them
        $count_search_set = count($search_set);
        for ($i = 0; $i < $count_search_set; ++$i) {
            @reset($forum_list);
            while (list(, $temp) = @each($forum_list)) {
                if ($temp[0] == $search_set[$i]['forum_id'])
                    $forum = _CHtml::link(_CHtml::encode($temp[1]), array('forum/viewforum', 'id' => $temp[0]));
            }

            if ($_config['o_censoring'] == '1')
                $search_set[$i]['subject'] = censor_words($search_set[$i]['subject']);

            if ($show_as == 'posts') {
                ++$post_count;
                $icon = '<div class="icon"><div class="nosize">' . $lang_common['Normal icon'] . '</div></div>' . "\n";
                $subject = _CHtml::link(_CHtml::encode($search_set[$i]['subject']), array('forum/viewtopic', 'id' => $search_set[$i]['tid']));

                if (!$_user['is_guest'] && $search_set[$i]['last_post'] > $_user['last_visit'])
                    $icon = '<div class="icon inew"><div class="nosize">' . $lang_common['New icon'] . '</div></div>' . "\n";

                if ($_config['o_censoring'] == '1')
                    $search_set[$i]['message'] = censor_words($search_set[$i]['message']);

                $message = parse_message($search_set[$i]['message'], $search_set[$i]['hide_smilies']);
                $pposter = _CHtml::encode($search_set[$i]['pposter']);

                if ($search_set[$i]['poster_id'] > 1) {
                    if ($_user['g_view_users'] == '1')
                        $pposter = '<strong>' . _CHtml::link($pposter, array('forum/profile', 'id' => $search_set[$i]['poster_id'])) . '</strong>';
                    else
                        $pposter = '<strong>' . $pposter . '</strong>';
                }

                $vtpost1 = ($i == 0) ? ' vtp1' : '';

                ?>
<div class="blockpost<?php echo ($post_count % 2 == 0) ? ' roweven' : ' rowodd' ?>">
	<h2><span><?php echo $forum ?></span> <span>&raquo;&nbsp;<?php echo $subject ?></span> <span>&raquo;&nbsp;
	<?php echo _CHtml::link(MDate::format($search_set[$i]['pposted']), array('forum/viewtopic', 'pid' => $search_set[$i]['pid'] . '#p' . $search_set[$i]['pid']));?></a></span></h2>
	<div class="box">
		<div class="inbox">
			<div class="postbody">
				<div class="postleft">
					<dl>
						<dt><?php echo $pposter ?></dt>
						<dd><?php echo $lang_common['Replies'] ?>: <?php echo forum_number_format($search_set[$i]['num_replies']) ?></dd>
						<dd><?php echo $icon ?></dd>
					</dl>
				</div>
				<div class="postright">
					<div class="postmsg">
						<?php echo $message ?>
					</div>
				</div>
				<div class="clearer"></div>
			</div>
		</div>
		<div class="inbox">
			<div class="postfoot clearb">
				<div class="postfootright"><ul><li>
				<?php echo _CHtml::link($lang_search['Go to topic'], array('forum/viewtopic', 'id' => $search_set[$i]['tid']));
                echo $lang_common['Link separator'] ?></li><li><?php echo _CHtml::link($lang_search['Go to post'], array('forum/viewtopic', 'pid' => $search_set[$i]['pid'] . '#p' . $search_set[$i]['pid']));?></li></ul></div>
			</div>
		</div>
	</div>
</div>
<?php

            }else {
                $icon = '<div class="icon"><div class="nosize">' . $lang_common['Normal icon'] . '</div></div>' . "\n";

                $icon_text = $lang_common['Normal icon'];
                $item_status = '';
                $icon_type = 'icon';

                $subject = _CHtml::link(_CHtml::encode($search_set[$i]['subject']), array('forum/viewtopic', 'id' => $search_set[$i]['tid'])) . '<span class="byuser">' . $lang_common['by'] . '&nbsp;' . _CHtml::encode($search_set[$i]['poster']) . '</span>';

                if ($search_set[$i]['closed'] != '0') {
                    $icon_text = $lang_common['Closed icon'];
                    $item_status = 'iclosed';
                }

                if (!$_user['is_guest'] && $search_set[$i]['last_post'] > $_user['last_visit'] && (!isset($tracked_topics['topics'][$search_set[$i]['tid']]) || $tracked_topics['topics'][$search_set[$i]['tid']] < $search_set[$i]['last_post']) && (!isset($tracked_topics['forums'][$search_set[$i]['forum_id']]) || $tracked_topics['forums'][$search_set[$i]['forum_id']] < $search_set[$i]['last_post'])) {
                    $icon_text .= ' ' . $lang_common['New icon'];
                    $item_status .= ' inew';
                    $icon_type = 'icon inew';
                    $subject = '<strong>' . $subject . '</strong>';
                    $subject_new_posts = '<span class="newtext">[ ' . _CHtml::link($lang_common['New posts'], array('forum/viewtopic', 'id' => $search_set[$i]['tid'], 'action' => 'new'), array('title' => $lang_common['New posts info'])) . ' ]</span>';
                }else
                    $subject_new_posts = null;

                if ($search_set[$i]['sticky'] == '1') {
                    $subject = '<span class="stickytext">' . $lang_forum['Sticky'] . ': </span>' . $subject;
                    $item_status .= ' isticky';
                    $icon_text .= ' ' . $lang_forum['Sticky'];
                }

                $num_pages_topic = ceil(($search_set[$i]['num_replies'] + 1) / $_user['disp_posts']);

                if ($num_pages_topic > 1)
                    $subject_multipage = '<span class="pagestext">[ ' . paginate($num_pages_topic, - 1, 'viewtopic.php?id=' . $search_set[$i]['tid']) . ' ]</span>';
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
					<td class="tc2"><?php echo $forum ?></td>
					<td class="tc3"><?php echo forum_number_format($search_set[$i]['num_replies']) ?></td>
					<td class="tcr"><?php echo _CHtml::link(MDate::format($search_set[$i]['last_post']), array('forum/viewtopic', 'pid' => $search_set[$i]['last_post_id'] . '#p' . $search_set[$i]['last_post_id'])) . ' ' . $lang_common['by'] . '&nbsp;' . _CHtml::encode($search_set[$i]['last_poster']) ?></td>
				</tr>
<?php

            }
        }

        if ($show_as == 'topics')
            echo "\t\t\t" . '</tbody>' . "\n\t\t\t" . '</table>' . "\n\t\t" . '</div>' . "\n\t" . '</div>' . "\n" . '</div>' . "\n\n";

        ?>
<div class="<?php echo ($show_as == 'topics') ? 'linksb' : 'postlinksb'; ?>">
	<div class="inbox">
		<p class="pagelink"><?php echo $paging_links ?></p>
		<div class="clearer"></div>
	</div>
</div>
<?php

        $footer_style = 'search';
        require SHELL_PATH . 'footer.php';
    }else
        message($lang_search['No hits']);
}else {
    $page_title = _CHtml::encode($this->PageTitle) . ' / ' . $lang_search['Search'];
    $focus_element = array('search', 'keywords');
    require SHELL_PATH . 'header.php';

    ?>
<div id="searchform" class="blockform">
	<h2><span><?php echo $lang_search['Search'] ?></span></h2>
	<div class="box">
		<?php echo _CHtml::form(array('forum/search'), 'GET', array('id' => 'search'));?>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_search['Search criteria legend'] ?></legend>
					<div class="infldset">
						<input type="hidden" name="action" value="search" />
						<label class="conl"><?php echo $lang_search['Keyword search'] ?><br /><input type="text" name="keywords" size="40" maxlength="100" /><br /></label>
						<label class="conl"><?php echo $lang_search['Author search'] ?><br /><input id="author" type="text" name="author" size="25" maxlength="25" /><br /></label>
						<p class="clearb"><?php echo $lang_search['Search info'] ?></p>
					</div>
				</fieldset>
			</div>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_search['Search in legend'] ?></legend>
					<div class="infldset">
						<label class="conl"><?php echo $lang_search['Forum search'] . "\n" ?>
						<br /><select id="forum" name="forum">
<?php

    if ($_config['o_search_all_forums'] == '1' || $_user['is_admmod'])
        echo "\t\t\t\t\t\t\t" . '<option value="-1">' . $lang_search['All forums'] . '</option>' . "\n";

    $db->setQuery('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.redirect_url FROM forum_categories AS c INNER JOIN forum_forums AS f ON c.id=f.cat_id LEFT JOIN forum_forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id=' . $_user['g_id'] . ') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.redirect_url IS NULL ORDER BY c.disp_position, c.id, f.disp_position', true) or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

    $cur_category = 0;
    while ($cur_forum = $db->fetch_assoc()) {
        if ($cur_forum['cid'] != $cur_category) { // A new category since last iteration?
                if ($cur_category)
                    echo "\t\t\t\t\t\t\t" . '</optgroup>' . "\n";

                echo "\t\t\t\t\t\t\t" . '<optgroup label="' . _CHtml::encode($cur_forum['cat_name']) . '">' . "\n";
                $cur_category = $cur_forum['cid'];
            }

            echo "\t\t\t\t\t\t\t\t" . '<option value="' . $cur_forum['fid'] . '">' . _CHtml::encode($cur_forum['forum_name']) . '</option>' . "\n";
        }

        ?>
							</optgroup>
						</select>
						<br /></label>
						<label class="conl"><?php echo $lang_search['Search in'] . "\n" ?>
						<br /><select id="search_in" name="search_in">
							<option value="all"><?php echo $lang_search['Message and subject'] ?></option>
							<option value="message"><?php echo $lang_search['Message only'] ?></option>
							<option value="topic"><?php echo $lang_search['Topic only'] ?></option>
						</select>
						<br /></label>
						<p class="clearb"><?php echo $lang_search['Search in info'] ?></p>
					</div>
				</fieldset>
			</div>
			<div class="inform">
				<fieldset>
					<legend><?php echo $lang_search['Search results legend'] ?></legend>
					<div class="infldset">
						<label class="conl"><?php echo $lang_search['Sort by'] . "\n" ?>
						<br /><select name="sort_by">
							<option value="0"><?php echo $lang_search['Sort by post time'] ?></option>
							<option value="1"><?php echo $lang_search['Sort by author'] ?></option>
							<option value="2"><?php echo $lang_search['Sort by subject'] ?></option>
							<option value="3"><?php echo $lang_search['Sort by forum'] ?></option>
						</select>
						<br /></label>
						<label class="conl"><?php echo $lang_search['Sort order'] . "\n" ?>
						<br /><select name="sort_dir">
							<option value="DESC"><?php echo $lang_search['Descending'] ?></option>
							<option value="ASC"><?php echo $lang_search['Ascending'] ?></option>
						</select>
						<br /></label>
						<label class="conl"><?php echo $lang_search['Show as'] . "\n" ?>
						<br /><select name="show_as">
							<option value="topics"><?php echo $lang_search['Show as topics'] ?></option>
							<option value="posts"><?php echo $lang_search['Show as posts'] ?></option>
						</select>
						<br /></label>
						<p class="clearb"><?php echo $lang_search['Search results info'] ?></p>
					</div>
				</fieldset>
			</div>
			<p class="buttons"><input type="submit" name="search" value="<?php echo $lang_common['Submit'] ?>" accesskey="s" /></p>
		</form>
	</div>
</div>
<?php

        require SHELL_PATH . 'footer.php';
    }