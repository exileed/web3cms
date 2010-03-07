<?php
/**
 * _CActiveRecord class file.
 * Add and redefine model methods of Yii core class CActiveRecord.
 * Table name - add the same table prefix to all database tables.
 */
class _CActiveRecord extends CActiveRecord
{
    /**
     * cache for table prefix so we don't have excessive overhead constantly retrieving it
     * @var string
     */
    protected $tablePrefix;

    /**
     * common constants
     */
    //const IS_ACTIVE='1';
    //const IS_NOT_ACTIVE='0';
 
    /**
     * Force the child classes to use our table name prefixer.
     */
    /*final */public function tableName()
    {
        // if we haven't retrieved the table prefix yet
        if($this->tablePrefix===null)
            // fetch prefix
            $this->tablePrefix=MParams::getTablePrefix();
 
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

    /**
     * Prepares attributes before performing validation.
     */
    protected function beforeValidate()
    {
        $scenario=$this->getScenario();
        if($this->isNewRecord)
        {
            if(isset($this->tableSchema->columns['createTime']) && ($this->createTime===null || $this->createTime===1234567890))
                $this->createTime=time();
        }
        else
        {
            if(isset($this->tableSchema->columns['updateTime']))
                $this->updateTime=time();
        }
        return true;
    }

    /**
     * Find all active records.
     * @param array of additional conditions
     * @return array of active record objects
     */
    /*public function findAllActiveRecords($conditions=array())
    {
        $id=(isset($conditions[0]) && ctype_digit($conditions[0]) && $conditions[0]>=1) ? $conditions[0] : null;
        $criteria=new CDbCriteria;
        $t=self::model()->tableName();
        $criteria->condition="`$t`.`isActive` IS NULL OR `$t`.`isActive` != '".self::IS_NOT_ACTIVE."'";
        if($id)
            $criteria->condition.=" OR `$t`.`id` = '$id'";
        return self::model()->findAll($criteria);
    }*/
}