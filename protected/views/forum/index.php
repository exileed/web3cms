<?php
define('WEB_PATH', Yii::app()->request->getBaseUrl(true) . '/_forum/');
define('SHELL_PATH', Yii::app()->getBasePath() . '/../_forum/');

// TODO : remove the following and replace it with Yii session vars
define('PUN', 1);
define('PUN_CONFIG_LOADED', 1);
global $_config;
$_config = array (
    'o_board_desc' => '<p><span>Unfortunately no one can be told what FluxBB is - you have to see it for yourself.</span></p>',
    'o_default_timezone' => '0',
    'o_timeout_visit' => '1800',
    'o_timeout_online' => '300',
    'o_redirect_delay' => '1',
    'o_show_version' => '0',
    'o_show_user_info' => '1',
    'o_show_post_count' => '1',
    'o_signatures' => '1',
    'o_smilies' => '1',
    'o_smilies_sig' => '1',
    'o_make_links' => '1',
    'o_default_lang' => 'English',
    'o_default_style' => 'Oxygen',
    'o_default_user_group' => '4',
    'o_topic_review' => '15',
    'o_disp_topics_default' => '30',
    'o_disp_posts_default' => '25',
    'o_indent_num_spaces' => '4',
    'o_quote_depth' => '3',
    'o_quickpost' => '1',
    'o_users_online' => '1',
    'o_censoring' => '0',
    'o_ranks' => '1',
    'o_show_dot' => '0',
    'o_topic_views' => '1',
    'o_quickjump' => '1',
    'o_gzip' => '0',
    'o_additional_navlinks' => '',
    'o_report_method' => '0',
    'o_regs_report' => '0',
    'o_default_email_setting' => '1',
    'o_mailing_list' => '',
    'o_avatars' => '1',
    'o_avatars_dir' => 'img/avatars',
    'o_avatars_width' => '60',
    'o_avatars_height' => '60',
    'o_avatars_size' => '10240',
    'o_search_all_forums' => '1',
    'o_web_path' => 'http://localhost/_forum',
    'o_admin_email' => '',
    'o_webmaster_email' => '',
    'o_subscriptions' => '1',
    'o_smtp_host' => null,
    'o_smtp_user' => null,
    'o_smtp_pass' => null,
    'o_smtp_ssl' => '0',
    'o_regs_allow' => '1',
    'o_regs_verify' => '0',
    'o_announcement' => '0',
    'o_announcement_message' => 'Enter your announcement here.',
    'o_rules' => '0',
    'o_rules_message' => 'Enter your rules here.',
    'o_maintenance' => '0',
    'o_maintenance_message' => 'The forums are temporarily down for maintenance. Please try again in a few minutes.<br />
<br />
/Administrator',
    'o_default_dst' => '0',
    'p_message_bbcode' => '1',
    'p_message_img_tag' => '1',
    'p_message_all_caps' => '1',
    'p_subject_all_caps' => '1',
    'p_sig_all_caps' => '1',
    'p_sig_bbcode' => '1',
    'p_sig_img_tag' => '0',
    'p_sig_length' => '400',
    'p_sig_lines' => '4',
    'p_allow_banned_email' => '1',
    'p_allow_dupe_email' => '0',
    'p_force_guest_email' => '1',
);
// END TODO

include((file_exists(SHELL_PATH . $page . '.php') ? SHELL_PATH . $page . '.php' : SHELL_PATH . 'index.php'));

?>