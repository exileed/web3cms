<?php
/**
 * MClientScript class file.
 * Manage client script - javascript / css code and files.
 */
class MClientScript
{
    /**
     * Register or return a piece of the javascript code.
     * Usually - some jquery code that is used in 2 or more pages.
     * @param string id of the script
     * @param array of the supplementary parameters
     * @param boolean whether script should be returned (not registered)
     * @return boolean whether script is recognized or string code of the script
     */
    public static function registerScript($id,$parameters=array(),$returnScript=false)
    {
        // parse the array of parameters
        $selector=isset($parameters['selector']) ? $parameters['selector'] : null;
        // recognize script based on the id parameter
        switch($id)
        {
            case 'actionButton':
                $scriptId=$id;
                $script=
"jQuery(\".w3-pre-item-action-bar ul li a\").hover(
    function(){ jQuery(this).parent().addClass('ui-state-hover').removeClass('ui-state-default').removeClass('ui-state-active'); }, 
    function(){ jQuery(this).parent().addClass('ui-state-default').removeClass('ui-state-hover').removeClass('ui-state-active'); } 
)
.mousedown(function(){ jQuery(this).parent().addClass('ui-state-active').removeClass('ui-state-default').removeClass('ui-state-hover'); })
.mouseup(function(){ jQuery(this).parent().addClass('ui-state-default').removeClass('ui-state-active').removeClass('ui-state-hover'); });";
                break;
            case 'autocompleteOff':
                $scriptId=$id.'['.$selector.']';
                $script=
"jQuery(\"".$selector."\").attr({'autocomplete': 'off'});";
                break;
            case 'datepicker':
                $scriptId=$id.'['.$selector.']';
                $script=
"jQuery(\"".$selector."\").datepicker({
    dateFormat: 'yy-mm-dd'
 });";
                break;
            case 'focusOnFormFirstItem':
                $scriptId=$id;
                $script=
"jQuery(\".w3-content form.w3-main-form .w3-input-text:first\").focus();
jQuery(\".w3-content form.w3-main-form .ui-state-error:first\").focus();";
                break;
            case 'formButton':
                $scriptId=$id;
                $script=
"jQuery(\".w3-form-row .w3-input-button\").hover(
    function(){ jQuery(this).addClass('ui-state-hover').removeClass('ui-state-default').removeClass('ui-state-active'); },
    function(){ jQuery(this).addClass('ui-state-default').removeClass('ui-state-hover').removeClass('ui-state-active'); }
)
.mousedown(function(){ jQuery(this).addClass('ui-state-active').removeClass('ui-state-default').removeClass('ui-state-hover'); })
.mouseup(function(){ jQuery(this).addClass('ui-state-default').removeClass('ui-state-active').removeClass('ui-state-hover'); });";
                break;
            case 'gridClose':
                $scriptId=$id.'['.$selector.']';
                $script=
"jQuery(\"".$selector."Wrapper\").find('a.w3-grid-titlebar-close').hover(
    function(){ jQuery(this).addClass('ui-state-hover').addClass('ui-corner-all'); },
    function(){ jQuery(this).removeClass('ui-state-hover').removeClass('ui-corner-all'); }
)
.mousedown(function(){ jQuery(this).addClass('ui-state-active'); })
.mouseup(function(){ jQuery(this).removeClass('ui-state-active'); })
.click(
    function(){
        if(jQuery(this).find('span.ui-icon').hasClass('ui-icon-circle-triangle-s')){
            jQuery(\"".$selector."\").slideDown('normal');
            jQuery(this).find('span.ui-icon').removeClass('ui-icon-circle-triangle-s').addClass('ui-icon-circle-triangle-n');
            jQuery(this).closest('.w3-grid-titlebar').removeClass('ui-corner-all');
        }
        else{
            jQuery(\"".$selector."\").slideUp('normal');
            jQuery(this).find('span.ui-icon').removeClass('ui-icon-circle-triangle-n').addClass('ui-icon-circle-triangle-s');
            jQuery(this).closest('.w3-grid-titlebar').removeClass('ui-corner-all').addClass('ui-corner-all');
        }
    }
);";
                break;
            case 'gridLinkIcon':
                $controllerId=$parameters['controllerId'];
                $gridId=$parameters['gridId'];
                $scriptId=$id;
                $script=
"jQuery(\"a.w3-ig.w3-link-icon span.ui-icon-trash\").click(
    function(){
        var recordId = jQuery(this).closest('tr').attr('id');
        if(recordId >= 1){
            var thisIcon = jQuery(this);
            var thisLink = jQuery(this).closest('a.w3-ig.w3-link-icon');
            var thisBox = jQuery(this).closest('tr');
            var dialogBoxId = Math.random()*100000000000000000000; //3 extra zeros
            var confirmBoxId = dialogBoxId*10;
            var confirmMessage = '".CHtml::encode(Yii::t('hint','Are you sure you want to delete the record number {id}? Deleted records may not be restored!',array(1)))."<strong>'+recordId+'</strong>".
                CHtml::encode(Yii::t('hint','Are you sure you want to delete the record number {id}? Deleted records may not be restored!',array(2)))."';
            jQuery('body').append('<div id=\"'+confirmBoxId+'\" class=\"'+confirmBoxId+'\" title=\"".Yii::t('hint','Please, confirm')."\"><span class=\"w3-icon-left ui-icon ui-icon-notice\"></span>'+confirmMessage+'</div>');
            jQuery('div#'+confirmBoxId+'.'+confirmBoxId).dialog({
                modal: true,
                buttons: {
                    '".Yii::t('link','Cancel[form]')."': function(){jQuery(this).dialog('close');},
                    '".Yii::t('link','Delete')."': function(){
                        jQuery.ajax({
                            'type': 'POST',
                            'data': 'id='+recordId,
                            'dataType': 'json',
                            'beforeSend': function(){
                                jQuery(thisIcon).removeClass('ui-icon-trash').removeClass('ui-icon-flag').removeClass('ui-icon-alert').addClass('ui-icon-flag');
                                jQuery(thisLink).removeClass('ui-state-default').removeClass('ui-state-error').removeClass('ui-state-highlight').addClass('ui-state-default');
                                jQuery(thisBox).addClass('ui-state-error');
                            },
                            'success': function(data){
                                if(data['status']=='success'){
                                    jQuery(thisIcon).removeClass('ui-icon-trash').removeClass('ui-icon-flag').removeClass('ui-icon-alert').addClass('ui-icon-trash');
                                    jQuery(thisLink).removeClass('ui-state-default').removeClass('ui-state-error').removeClass('ui-state-highlight').addClass('ui-state-highlight');
                                    jQuery(thisBox).find('td').animate({opacity: 0.1},2400);
                                    setTimeout(function(){jQuery('#".$gridId."').jqGrid('delRowData',recordId);},2400);
                                    jQuery('body').append('<div id=\"'+dialogBoxId+'\" class=\"'+dialogBoxId+'\" title=\"".Yii::t('t','Done')."\"><span class=\"w3-icon-left ui-icon ui-icon-circle-check\"></span>'+data['message']+'</div>');
                                    jQuery('div#'+dialogBoxId+'.'+dialogBoxId).dialog({position: ['right','top'], buttons: {'".Yii::t('t','Ok')."': function(){jQuery(this).dialog('close');}}});
                                }
                                else{
                                    jQuery(thisIcon).removeClass('ui-icon-trash').removeClass('ui-icon-flag').removeClass('ui-icon-alert').addClass('ui-icon-alert');
                                    jQuery(thisLink).removeClass('ui-state-default').removeClass('ui-state-error').removeClass('ui-state-highlight').addClass('ui-state-error');
                                    jQuery(thisBox).removeClass('ui-state-error');
                                    jQuery('body').append('<div id=\"'+dialogBoxId+'\" class=\"'+dialogBoxId+'\" title=\"".Yii::t('t','Error')."\"><span class=\"w3-icon-left ui-icon ui-icon-alert\"></span>'+data['message']+'</div>');
                                    jQuery('div#'+dialogBoxId+'.'+dialogBoxId).dialog({position: ['right','top'], buttons: {'".Yii::t('t','Ok')."': function(){jQuery(this).dialog('close');}}});
                                }
                            },
                            'error': function(){
                                jQuery(thisIcon).removeClass('ui-icon-trash').removeClass('ui-icon-flag').removeClass('ui-icon-alert').addClass('ui-icon-alert');
                                jQuery(thisLink).removeClass('ui-state-default').removeClass('ui-state-error').removeClass('ui-state-highlight').addClass('ui-state-error');
                                jQuery(thisBox).removeClass('ui-state-error');
                                var message = '".CHtml::encode(Yii::t('hint','An error has occured while deleting the record number {id}.',array(1)))."<strong>'+recordId+'</strong>".
                                    CHtml::encode(Yii::t('hint','An error has occured while deleting the record number {id}.',array(2)))."';
                                jQuery('body').append('<div id=\"'+dialogBoxId+'\" class=\"'+dialogBoxId+'\" title=\"".Yii::t('t','Error')."\"><span class=\"w3-icon-left ui-icon ui-icon-alert\"></span>'+message+'</div>');
                                jQuery('div#'+dialogBoxId+'.'+dialogBoxId).dialog({position: ['right','top'], buttons: {'".Yii::t('t','Ok')."': function(){jQuery(this).dialog('close');}}});
                            },
                            'url': '". CHtml::normalizeUrl(array($controllerId.'/ajaxDelete')) ."',
                            'cache': false
                        });
                        jQuery(this).dialog('close');
                    }
                }
            });
        }
        return false;
    }
);";
                break;
            case 'hide':
                $scriptId=$id.'['.$selector.']';
                $script=
"jQuery(\"".$selector."\").hide();";
                break;
            case 'linkIcon':
                $box=$parameters['box'];
                $scriptId=$id.'['.$box.']';
                $script=
"jQuery(\"a.".$box.".w3-link-icon\").hover(
    function(){ jQuery(this).addClass('ui-state-hover').removeClass('ui-state-active').removeClass('w3-border-1px-transparent'); },
    function(){ jQuery(this).addClass('w3-border-1px-transparent').removeClass('ui-state-hover').removeClass('ui-state-active'); }
)
.mousedown(function(){ jQuery(this).addClass('w3-border-1px-transparent').addClass('ui-state-active').removeClass('ui-state-hover'); })
.mouseup(function(){ jQuery(this).removeClass('ui-state-active').removeClass('ui-state-hover'); });";
                break;
            case 'linkListClose':
                $scriptId=$id;
                $script=
"jQuery(\".w3-link-list-box a.w3-link-list.w3-titlebar-close\").hover(
    function(){ jQuery(this).addClass('ui-state-hover').addClass('ui-corner-all'); },
    function(){ jQuery(this).removeClass('ui-state-hover').removeClass('ui-corner-all'); }
)
.mousedown(function(){ jQuery(this).addClass('ui-state-active'); })
.mouseup(function(){ jQuery(this).removeClass('ui-state-active'); })
.click(
    function(){
        if(jQuery(this).find('span.ui-icon').hasClass('ui-icon-circle-triangle-s')){
            jQuery(this).closest('.w3-link-list.w3-titlebar').next('.w3-link-list').slideDown('normal');
            jQuery(this).find('span.ui-icon').removeClass('ui-icon-circle-triangle-s').addClass('ui-icon-circle-triangle-n');
        }
        else{
            jQuery(this).closest('.w3-link-list.w3-titlebar').next('.w3-link-list').slideUp('normal');
            jQuery(this).find('span.ui-icon').removeClass('ui-icon-circle-triangle-n').addClass('ui-icon-circle-triangle-s');
        }
    }
);";
                break;
            case 'linkListHover':
                $scriptId=$id;
                $script=
"jQuery(\".w3-link-list.w3-effects-on ul li a\").hover(
    function(){ jQuery(this).addClass('ui-state-hover').addClass('ui-corner-all'); }, 
    function(){ jQuery(this).removeClass('ui-state-hover').removeClass('ui-corner-all'); } 
)
.mousedown(function(){ jQuery(this).addClass('ui-state-active'); })
.mouseup(function(){ jQuery(this).removeClass('ui-state-active'); });";
                break;
            case 'mainMenu':
                $scriptId=$id;
                $script=
"jQuery('.w3-header .w3-main-menu ul li a').hover( 
    function(){ jQuery(this).parent().removeClass('ui-state-default').addClass('ui-state-hover'); }, 
    function(){ jQuery(this).parent().removeClass('ui-state-hover').addClass('ui-state-default'); } 
);";
                break;
            case 'screenNameSame':
                $scriptId=$id;
                $script=
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
);";
                break;
            default:
                $scriptId=null;
                break;
        }
        if($scriptId!==null)
        {
            if($returnScript)
                // return script code
                return $script;
            else
            {
                // register the script and return true
                Yii::app()->getClientScript()->registerScript($scriptId,$script);
                return true;
            }
        }
        else
            // script is not recognized
            return false;
    }
}