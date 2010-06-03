<?php
/**
 * MHtml class file.
 * Manage html elements, DOM and the look.
 */
class MHtml
{
    const EMOTICONS_DIR='/static/js/markitup/images/emoticons/';

    /**
     * Wrap a text in a html tag.
     * @param string text to be wrapped
     * @param string html tag
     * @return string
     */
    public static function wrapInTag($text,$tag)
    {
        return '<'.$tag.'>'.$text.'</'.$tag.'>';
    }

    /*
     * BBCode parser that renders HTML
     * used @ forum controller
     * @param string input text
     * @return string
     */
    public static function renderBB($text) {
	$text = trim($text);

	// BBCode [code]
	if (!function_exists('escape')) {
		function escape($s) {
			global $text;
			$text = strip_tags($text);
			$code = $s[1];
			$code = htmlspecialchars($code);
			$code = str_replace("[", "&#91;", $code);
			$code = str_replace("]", "&#93;", $code);
			return '<pre><code>'.$code.'</code></pre>';
		}
	}
	$text = preg_replace_callback('/\[code\](.*?)\[\/code\]/ms', "escape", $text);

	// Smileys to find...
	$in = array(                     ':)',
					 ':D',
					 ':o',
					 ':p',
					 ':(',
					 ';)'
	);
	// And replace them by...
	$out = array(	 '<img alt=":)" src="'.EMOTICONS_DIR.'emoticon-happy.png" />',
					 '<img alt=":D" src="'.EMOTICONS_DIR.'emoticon-smile.png" />',
					 '<img alt=":o" src="'.EMOTICONS_DIR.'emoticon-surprised.png" />',
					 '<img alt=":p" src="'.EMOTICONS_DIR.'emoticon-tongue.png" />',
					 '<img alt=":(" src="'.EMOTICONS_DIR.'emoticon-unhappy.png" />',
					 '<img alt=";)" src="'.EMOTICONS_DIR.'emoticon-wink.png" />'
	);
	$text = str_replace($in, $out, $text);

	// BBCode to find...
	$in = array( 	 '/\[b\](.*?)\[\/b\]/ms',
					 '/\[i\](.*?)\[\/i\]/ms',
					 '/\[u\](.*?)\[\/u\]/ms',
					 '/\[img\](.*?)\[\/img\]/ms',
					 '/\[email\](.*?)\[\/email\]/ms',
					 '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms',
					 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
					 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
					 '/\[quote](.*?)\[\/quote\]/ms',
					 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
					 '/\[list\](.*?)\[\/list\]/ms',
					 '/\[\*\]\s?(.*?)\n/ms'
	);
	// And replace them by...
	$out = array(	 '<strong>\1</strong>',
					 '<em>\1</em>',
					 '<u>\1</u>',
					 '<img src="\1" alt="\1" />',
					 '<a href="mailto:\1">\1</a>',
					 '<a href="\1">\2</a>',
					 '<span style="font-size:\1%">\2</span>',
					 '<span style="color:\1">\2</span>',
					 '<blockquote>\1</blockquote>',
					 '<ol start="\1">\2</ol>',
					 '<ul>\1</ul>',
					 '<li>\1</li>'
	);
	$text = preg_replace($in, $out, $text);

	// paragraphs
	$text = str_replace("\r", '', $text);
	$text = '<p>'.preg_replace("/(\n){2,}/", '</p><p>', $text).'</p>';
	$text = nl2br($text);

	// clean some tags to remain strict
	// not very elegant, but it works. No time to do better ;)
	if (!function_exists('removeBr')) {
		function removeBr($s) {
			return str_replace("<br />", '', $s[0]);
		}
	}
	$text = preg_replace_callback('/<pre>(.*?)<\/pre>/ms', "removeBr", $text);
	$text = preg_replace('/<p><pre>(.*?)<\/pre><\/p>/ms', "<pre>\\1</pre>", $text);

	$text = preg_replace_callback('/<ul>(.*?)<\/ul>/ms', "removeBr", $text);
	$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/ms', "<ul>\\1</ul>", $text);

	return $text;
    }
}