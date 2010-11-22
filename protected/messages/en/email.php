<?php

// i18n - English Language Pack (Emails)
$retval=array(
    'New member account' => 'New member account',
    'Content(New member account)' => 'Welcome to {siteTitle}

New member account "{screenName}" has been successfully created.

--------------------------------------------------
Confirmation key: {emailConfirmationKey}
--------------------------------------------------

To confirm your email address, please visit the following link
{emailConfirmationLink}',
);
$myfile=dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'_local'.DIRECTORY_SEPARATOR.basename(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.basename(dirname(__FILE__)).DIRECTORY_SEPARATOR.basename(__FILE__);
return (file_exists($myfile) && is_array($myarray=require($myfile))) ? CMap::mergeArray($retval,$myarray) : $retval;