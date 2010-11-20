<?php
/**
 * Manage Path
 */
class MPath
{
    /**
     * Check whether a css theme exists.
     * @param string $theme
     * @return bool
     */
    public static function jqGridLocaleExists($locale=null)
    {
        if(empty($locale))
            return false;
        return file_exists(dirname(Yii::app()->basePath).DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'jqgrid'.DIRECTORY_SEPARATOR.'i18n'.DIRECTORY_SEPARATOR.'grid.locale-'.$locale.'.js');
    }

    /**
     * Check whether a css theme exists.
     * @param string $interface
     * @return bool
     */
    public static function interfaceExists($interface=null)
    {
        if(empty($interface))
            return false;
        return file_exists(dirname(Yii::app()->basePath).DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'ui'.DIRECTORY_SEPARATOR.$interface.DIRECTORY_SEPARATOR.'jquery-ui-'.MParams::jqueryUIVersion.'.custom.css');
    }
}