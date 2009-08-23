<?php
/**
 * Manage Path
 */
class MPath
{
    /**
     * Check whether a css theme exists.
     * 
     * @param string $theme
     * @return bool
     */
    public static function cssThemeExists($theme=null)
    {
        if(empty($theme))
            return false;
        return file_exists(dirname(Yii::app()->basePath).DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'jquery-ui-'.MParams::jqueryUIVersion.'.custom.css');
    }
}