<?php
/**
 * MLinkList class file.
 * Manage list of links for the {@link WLinkList} widget.
 */
class MLinkList
{
    /**
     * @var multi-dimensional array of links.
     */
    protected static $links;

    /**
     * Get array of the links for the specified id.
     * @param string identificator
     */
    public static function get($id=null)
    {
        if($id===null)
            $retval=self::$links;
        else
            $retval=isset(self::$links[$id]) ? self::$links[$id] : array();
        return $retval;
    }

    /**
     * Set array of the links for the specified id.
     * @param string identificator
     * @param array of parameters
     */
    public static function set($id,$parameters)
    {
        self::$links[$id]=$parameters;
    }

    /**
     * Check whether array of the links exist for the specified id.
     * @param string identificator
     * @return bool
     */
    public static function has($id)
    {
        return isset(self::$links[$id]) && is_array(self::$links[$id]);
    }
}