<?php
/**
 * MClientScript class file.
 * Manage client script - javascript / css code and files.
 */
class MClientScript
{
    /**
     * Register a piece of the javascript code.
     * Usually - some jquery code that is used in 2 or more pages.
     * @param string id of the script
     * @param array of the supplementary parameters
     * @return boolean whether script is registered
     */
    public static function registerScript($id,$parameters=array())
    {
        switch($id)
        {
            case 'autocompleteOff':
                $selector=$parameters['selector'];
                Yii::app()->getClientScript()->registerScript('autocompleteOff['.$selector.']',
"jQuery(\"".$selector."\").attr({'autocomplete': 'off'});");
                $retval=true;
                break;
            case 'datepicker':
                $selector=$parameters['selector'];
                Yii::app()->getClientScript()->registerScript('datepicker['.$selector.']',
"jQuery(\"".$selector."\").datepicker({
	dateFormat: 'yy-mm-dd'
 });");
                $retval=true;
                break;
            case 'focusOnFormFirstItem':
                Yii::app()->getClientScript()->registerScript('focusOnFormFirstItem',
"jQuery(\".w3-content form.w3-main-form .w3-input-text:first\").focus();
jQuery(\".w3-content form.w3-main-form .ui-state-error:first\").focus();");
                $retval=true;
                break;
            case 'screenNameSame':
                Yii::app()->getClientScript()->registerScript('screenNameSame',
"if(jQuery(\"input#User_screenNameSame\").attr('checked'))
    jQuery(\"input#User_screenName\").hide();
jQuery(\"input#User_screenNameSame\").click(
    function(){
        if(jQuery(this).attr('checked'))
            jQuery(\"input#User_screenName\").fadeOut('normal');
        else{
            jQuery(\"input#User_screenName\").fadeIn('normal');
            jQuery(\"input#User_screenName\").focus();
        }
    }
);");
                $retval=true;
                break;
            case 'w3ActionButton':
                Yii::app()->getClientScript()->registerScript('w3ActionButton',
"jQuery(\".w3-pre-item-action-bar ul li a\").hover(
    function(){ jQuery(this).parent().removeClass('ui-state-default').addClass('ui-state-hover'); }, 
    function(){ jQuery(this).parent().removeClass('ui-state-hover').addClass('ui-state-default'); } 
)
.mousedown(function(){ jQuery(this).parent().addClass('ui-state-active'); })
.mouseup(function(){ jQuery(this).parent().removeClass('ui-state-active'); });");
                $retval=true;
                break;
            case 'w3FormButton':
                Yii::app()->getClientScript()->registerScript('w3FormButton',
"jQuery(\".w3-form-row .w3-input-button\").hover(
    function(){ jQuery(this).addClass('ui-state-hover'); },
    function(){ jQuery(this).removeClass('ui-state-hover'); }
)
.mousedown(function(){ jQuery(this).addClass('ui-state-active'); })
.mouseup(function(){ jQuery(this).removeClass('ui-state-active'); });");
                $retval=true;
                break;
            default:
                $retval=false;
                break;
        }
        return $retval;
    }
}