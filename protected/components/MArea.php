<?php
/**
 * MArea class file.
 * Manage frontend & backend area.
 */
class MArea
{
    /**
     * Check whether is backend.
     * @return bool
     */
    public static function isBackend()
    {
        // setPathOfAlias is in backend/config/main.php
        return (bool)Yii::getPathOfAlias('backend');
    }

    /**
     * Check whether is frontend.
     * @return bool
     */
    public static function isFrontend()
    {
        return !self::backend();
    }
}