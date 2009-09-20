<?php
/**
 * _CActiveRecord class file.
 * Add and redefine model methods of Yii core class CActiveRecord.
 * Table name - add the same table prefix to all database tables.
 */
class _CActiveRecord extends CActiveRecord
{
    // cache for table prefix so we don't have excessive overhead constantly retrieving it
    protected $tablePrefix;
 
    /**
     * Force the child classes to use our table name prefixer
     */
    final public function tableName()
    {
        // if we haven't retrieved the table prefix yet
        if(is_null($this->tablePrefix))
            // fetch prefix
            $this->tablePrefix = Yii::app()->params['tablePrefix'];
 
        // prepend prefix, call our new method
        return ($this->tablePrefix . $this->_tableName());
    }
 
    /**
     * Function for child classes to implement to return the table name associated with it
     */
    protected function _tableName()
    {
        // call the original method for our table name stuff
        return parent::tableName();
    }
}