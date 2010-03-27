<?php
global $db, $_user;
// Define the version and database revision that this code was written for
define('FORUM_VERSION', '1.4-rc1');
define('FORUM_DB_REVISION', 2);
// If we have the 1.3-legacy constant defined, define the proper 1.4 constant so we don't get an incorrect "need to install" message
if (defined('FORUM'))
    define('PUN', FORUM);
// Load DB abstraction layer and connect
$db = Yii::app()->db;
$db->active = true;
// Load the functions script
require SHELL_PATH . 'include/functions.php';
// Load UTF-8 functions
require SHELL_PATH . 'include/utf8/utf8.php';
// Block prefetch requests
if (isset($_SERVER['HTTP_X_MOZ']) && $_SERVER['HTTP_X_MOZ'] == 'prefetch')
{
    header('HTTP/1.1 403 Prefetching Forbidden');
    // Send no-cache headers
    header('Expires: Thu, 21 Jul 1977 07:30:00 GMT'); // When yours truly first set eyes on this world! :)
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache'); // For HTTP/1.0 compatibility    exit;
}
// Reverse the effect of register_globals
forum_unregister_globals();
// Record the start time (will be used to calculate the generation time for the page)
$_start = get_microtime();
// Make sure PHP reports all errors except E_NOTICE. FluxBB supports E_ALL, but a lot of scripts it may interact with, do not
error_reporting(E_ALL ^ E_NOTICE);
// Turn off magic_quotes_runtime
if (get_magic_quotes_runtime())
    set_magic_quotes_runtime(0);
// Force POSIX locale (to prevent functions such as strtolower() from messing up UTF-8 strings)
setlocale(LC_CTYPE, 'C');
// Strip slashes from GET/POST/COOKIE (if magic_quotes_gpc is enabled)
if (get_magic_quotes_gpc())
{
    function stripslashes_array($array)
    {
        return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
    }    $_GET = stripslashes_array($_GET);
    $_POST = stripslashes_array($_POST);
    $_COOKIE = stripslashes_array($_COOKIE);
}
$cookie_name = '_cookie';
// If the cache directory is not specified, we use the default setting
if (!defined('FORUM_CACHE_DIR'))
    define('FORUM_CACHE_DIR', SHELL_PATH . 'cache/');
// Define a few commonly used constants
define('PUN_UNVERIFIED', 0);
define('PUN_ADMIN', 1);
define('PUN_MOD', 2);
define('PUN_GUEST', 3);
define('PUN_MEMBER', 4);
{
    if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
        require SHELL_PATH . 'include/cache.php';
}
// Enable output buffering
if (!defined('PUN_DISABLE_BUFFERING'))
{
    // Should we use gzip output compression?
    if ($_config['o_gzip'] && extension_loaded('zlib'))
        ob_start('ob_gzhandler');
    else
        ob_start();
}
// Check/update/set cookie and fetch user info
$_user = array();
$_user = check_cookie($_user);
// Attempt to load the common language file
if (file_exists(SHELL_PATH . 'lang/' . $_user['language'] . '/common.php'))
    include SHELL_PATH . 'lang/' . $_user['language'] . '/common.php';
else
{
    error('There is no valid language pack \'' . _CHtml::encode($_user['language']) . '\' installed. Please reinstall a language of that name.');
}
// Check if we are to display a maintenance message
if ($_config['o_maintenance'] && $_user['g_id'] > PUN_ADMIN && !defined('PUN_TURN_OFF_MAINT'))
    maintenance_message();
// Load cached bans
if (file_exists(FORUM_CACHE_DIR . 'cache_bans.php'))
    include FORUM_CACHE_DIR . 'cache_bans.php';if (!defined('PUN_BANS_LOADED'))
{
    if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
        require SHELL_PATH . 'include/cache.php';    generate_bans_cache();
    require FORUM_CACHE_DIR . 'cache_bans.php';
}
// Check if current user is banned
check_bans();
// Update online list
update_users_online();
// Check to see if we logged in without a cookie being set
if ($_user['is_guest'] && isset($_GET['login']))
    message($lang_common['No cookie']);