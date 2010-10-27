<?php
/**
 * MDate class file.
 * Manage date and time.
 */
class MDate
{
    const DB_DATE_FORMAT='Y-m-d';
    const DB_DATETIME_FORMAT='Y-m-d H:i:s';

    /**
     * Formats a date according to a predefined pattern.
     * @param mixed UNIX timestamp or a string in strtotime format
     * @param string width of the date pattern. It can be 'full', 'long', 'medium' and 'short'.
     * @param string width of the time pattern. It can be 'full', 'long', 'medium' and 'short'.
     * @return string
     */
    public static function format($time,$dateWidth='medium',$timeWidth='short')
    {
        if(!is_int($time) && !ctype_digit($time) && strtotime($time)===false)
            // parameter is not a number of seconds since the Unix Epoch
            // and is not a parseable date
            return $time;
        $retval=Yii::app()->dateFormatter->formatDateTime($time,$dateWidth,$timeWidth);
        return $retval;
    }

    /**
     * Formats a date for insert in the database.
     * @param mixed UNIX timestamp or a string in strtotime format
     * @param string format of the field
     * @return string
     */
    public static function formatToDb($time,$format='datetime')
    {
        // validate first parameter
        if(!is_int($time) && !ctype_digit($time) && strtotime($time)===false)
            return null;
        // convert to time (if necessary)
        if(is_string($time))
            $time=ctype_digit($time) ? (int)$time : strtotime($time);
        // compare format against magick keywords
        if($format==='date')
            $format=self::DB_DATE_FORMAT;
        else if($format==='datetime')
            $format=self::DB_DATETIME_FORMAT;
        // create date and return
        return date($format,$time);
    }
}