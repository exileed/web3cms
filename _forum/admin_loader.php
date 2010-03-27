<?php
// Tell header.php to use the admin template
define('PUN_ADMIN_CONSOLE', 1);
require SHELL_PATH . 'include/common.php';
require SHELL_PATH . 'include/common_admin.php';
if (!$_user['is_admmod'])
    message($lang_common['No permission']);
// The plugin to load should be supplied via GET
$plugin = isset($_GET['plugin']) ? $_GET['plugin'] : '';
if (!@preg_match('/^AM?P_(\w*?)\.php$/i', $plugin))
    message($lang_common['Bad request']);
// AP_ == Admins only, AMP_ == admins and moderators
$prefix = substr($plugin, 0, strpos($plugin, '_'));
if ($_user['g_moderator'] == '1' && $prefix == 'AP')
    message($lang_common['No permission']);
// Make sure the file actually exists
if (!file_exists(SHELL_PATH . 'plugins/' . $plugin))
    message('There is no plugin called \'' . $plugin . '\' in the plugin directory.');
// Construct REQUEST_URI if it isn't set
if (!isset($_SERVER['REQUEST_URI']))
    $_SERVER['REQUEST_URI'] = (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '') . '?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
require SHELL_PATH . 'header.php';
// Attempt to load the plugin. We don't use here to supress error messages,
// because if we did and a parse error occurred in the plugin, we would only
// get the "blank page of death"
include SHELL_PATH . 'plugins/' . $plugin;
if (!defined('PUN_PLUGIN_LOADED'))
    message('Loading of the plugin \'' . $plugin . '\' failed.');
// Output the clearer div
?>
	<div class="clearer"></div>
</div>
<?php require SHELL_PATH . 'footer.php';