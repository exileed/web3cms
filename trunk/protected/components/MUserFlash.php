<?php
/**
 * MUserFlash class file.
 * Manage user flash (feedback messages) for the {@link WUserFlash} widget.
 */
class MUserFlash
{
    const contentSuccess='contentSuccess';
    const contentInfo='contentInfo';
    const contentError='contentError';
    const sidebarSuccess='sidebarSuccess';
    const sidebarInfo='sidebarInfo';
    const sidebarError='sidebarError';
    const sidebar1Success='sidebar1Success';
    const sidebar1Info='sidebar1Info';
    const sidebar1Error='sidebar1Error';
    const sidebar2Success='sidebar2Success';
    const sidebar2Info='sidebar2Info';
    const sidebar2Error='sidebar2Error';
    const topSuccess='topSuccess';
    const topInfo='topInfo';
    const topError='topError';

    /**
     * Get array of messages from user flash reporting system.
     * If user flash has no messages, then return an empty array.
     * @param string $id
     * @param bool $delete
     * @return array of messages
     */
    public static function get($id,$delete=true)
    {
        if(self::has($id))
        {
            $defaultValue=null;
            $userFlash=Yii::app()->user->getFlash(self::checkId($id),$defaultValue,$delete);
            !is_array($userFlash) && $userFlash=array($userFlash);
        }
        else
            $userFlash=array();
        return $userFlash;
    }

    /**
     * Set/append a new string into user flash (reporting system) array.
     * In case of append, new value is checked for repeat (to prevent double-messaging).
     * @param string $id
     * @param string message $str
     * @param bool $append
     * @return bool (success)
     */
    public static function set($id,$str,$append=true)
    {
        if(empty($str))
            return false;
        if($append && self::has($id))
        {
            $userFlash=self::get($id);
            !is_array($userFlash) && $userFlash=array($userFlash);
            !in_array($str,$userFlash) && ($userFlash[]=$str);
        }
        else
            $userFlash=array($str);
        Yii::app()->user->setFlash(self::checkId($id),$userFlash);
        return true;
    }

    /**
     * Check whether user flash reporting system has any message for the given id.
     * @param string $id
     * @return bool
     */
    public static function has($id)
    {
        return Yii::app()->user->hasFlash(self::checkId($id));
    }

    /**
     * Check user flash id
     * @param string $id
     * @return string
     */
    public static function checkId($id)
    {
        $vals=array(
            self::contentInfo,self::contentError,self::contentSuccess,
            self::sidebarInfo,self::sidebarError,self::sidebarSuccess,
            self::sidebar1Info,self::sidebar1Error,self::sidebar1Success,
            self::sidebar2Info,self::sidebar2Error,self::sidebar2Success,
            self::topInfo,self::topError,self::topSuccess
        );
        if($id===true || !in_array($id,$vals))
            Yii::log(W3::t('system',
                'Uncommon parameter in method call: {method}.',
                array('{method}'=>__METHOD__.'('.var_export($id,true).')')
            ),'w3','info');
        return $id;
    }

    /* content aliases */
    public static function getContentSuccess($delete=true)
    {
        return self::get(self::contentSuccess,$delete);
    }
    public static function setContentSuccess($str,$append=true)
    {
        self::set(self::contentSuccess,$str,$append);
    }
    public static function hasContentSuccess()
    {
        return self::has(self::contentSuccess);
    }
    public static function getContentInfo($delete=true)
    {
        return self::get(self::contentInfo,$delete);
    }
    public static function setContentInfo($str,$append=true)
    {
        self::set(self::contentInfo,$str,$append);
    }
    public static function hasContentInfo()
    {
        return self::has(self::contentInfo);
    }
    public static function getContentError($delete=true)
    {
        return self::get(self::contentError,$delete);
    }
    public static function setContentError($str,$append=true)
    {
        self::set(self::contentError,$str,$append);
    }
    public static function hasContentError()
    {
        return self::has(self::contentError);
    }

    /* sidebar aliases - useful if only 1 sidebar is active */
    public static function getSidebarSuccess($delete=true)
    {
        return self::get(self::sidebarSuccess,$delete);
    }
    public static function setSidebarSuccess($str,$append=true)
    {
        self::set(self::sidebarSuccess,$str,$append);
    }
    public static function hasSidebarSuccess()
    {
        return self::has(self::sidebarSuccess);
    }
    public static function getSidebarInfo($delete=true)
    {
        return self::get(self::sidebarInfo,$delete);
    }
    public static function setSidebarInfo($str,$append=true)
    {
        self::set(self::sidebarInfo,$str,$append);
    }
    public static function hasSidebarInfo()
    {
        return self::has(self::sidebarInfo);
    }
    public static function getSidebarError($delete=true)
    {
        return self::get(self::sidebarError,$delete);
    }
    public static function setSidebarError($str,$append=true)
    {
        self::set(self::sidebarError,$str,$append);
    }
    public static function hasSidebarError()
    {
        return self::has(self::sidebarError);
    }

    /* sidebar1 aliases */
    public static function getSidebar1Success($delete=true)
    {
        return self::get(self::sidebar1Success,$delete);
    }
    public static function setSidebar1Success($str,$append=true)
    {
        self::set(self::sidebar1Success,$str,$append);
    }
    public static function hasSidebar1Success()
    {
        return self::has(self::sidebar1Success);
    }
    public static function getSidebar1Info($delete=true)
    {
        return self::get(self::sidebar1Info,$delete);
    }
    public static function setSidebar1Info($str,$append=true)
    {
        self::set(self::sidebar1Info,$str,$append);
    }
    public static function hasSidebar1Info()
    {
        return self::has(self::sidebar1Info);
    }
    public static function getSidebar1Error($delete=true)
    {
        return self::get(self::sidebar1Error,$delete);
    }
    public static function setSidebar1Error($str,$append=true)
    {
        self::set(self::sidebar1Error,$str,$append);
    }
    public static function hasSidebar1Error()
    {
        return self::has(self::sidebar1Error);
    }

    /* sidebar2 aliases */
    public static function getSidebar2Success($delete=true)
    {
        return self::get(self::sidebar2Success,$delete);
    }
    public static function setSidebar2Success($str,$append=true)
    {
        self::set(self::sidebar2Success,$str,$append);
    }
    public static function hasSidebar2Success()
    {
        return self::has(self::sidebar2Success);
    }
    public static function getSidebar2Info($delete=true)
    {
        return self::get(self::sidebar2Info,$delete);
    }
    public static function setSidebar2Info($str,$append=true)
    {
        self::set(self::sidebar2Info,$str,$append);
    }
    public static function hasSidebar2Info()
    {
        return self::has(self::sidebar2Info);
    }
    public static function getSidebar2Error($delete=true)
    {
        return self::get(self::sidebar2Error,$delete);
    }
    public static function setSidebar2Error($str,$append=true)
    {
        self::set(self::sidebar2Error,$str,$append);
    }
    public static function hasSidebar2Error()
    {
        return self::has(self::sidebar2Error);
    }

    /* top aliases */
    public static function getTopSuccess($delete=true)
    {
        return self::get(self::topSuccess,$delete);
    }
    public static function setTopSuccess($str,$append=true)
    {
        self::set(self::topSuccess,$str,$append);
    }
    public static function hasTopSuccess()
    {
        return self::has(self::topSuccess);
    }
    public static function getTopInfo($delete=true)
    {
        return self::get(self::topInfo,$delete);
    }
    public static function setTopInfo($str,$append=true)
    {
        self::set(self::topInfo,$str,$append);
    }
    public static function hasTopInfo()
    {
        return self::has(self::topInfo);
    }
    public static function getTopError($delete=true)
    {
        return self::get(self::topError,$delete);
    }
    public static function setTopError($str,$append=true)
    {
        self::set(self::topError,$str,$append);
    }
    public static function hasTopError()
    {
        return self::has(self::topError);
    }
}