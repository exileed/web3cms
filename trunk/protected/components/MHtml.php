<?php
/**
 * MHtml class file.
 * Manage html elements, DOM and the look.
 */
class MHtml
{
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
}