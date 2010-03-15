<?php

if (!defined('PUN')) exit;
define('PUN_QJ_LOADED', 1);

?>				<form id="qjump" action="forum/viewforum" method="GET">
					<div><label><?php echo $lang_common['Jump to'] ?>

					<br /><select name="id" onchange="window.location=('/index.php?r=forum/viewforum&id=%2Bthis.options%5Bthis.selectedIndex%5D.value')">
						<optgroup label="Test category">
							<option value="1"<?php echo ($forum_id == 1) ? ' selected="selected"' : '' ?>>Test forum</option>
						</optgroup>
					</select>
					<input type="submit" value="<?php echo $lang_common['Go'] ?>" accesskey="g" />
					</label></div>
				</form>
