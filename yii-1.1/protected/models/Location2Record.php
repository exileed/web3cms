<?php

class Location2Record extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'w3location2record':
     * @var integer $id
     * @var integer $locationId
     * @var integer $recordPriority
     * @var string $record
     * @var integer $recordId
     * @var integer $locationPriority
     * @var integer $createTime
     * @var integer $updateTime
     */

    /**
     * Returns the static model of the specified AR class.
     * @return CActiveRecord the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name (without prefix)
     */
    protected function _tableName()
    {
        return 'location_2_record';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('record', 'length', 'max'=>64),
            array('locationId, record, recordId', 'required'),
            array('locationId, recordPriority, recordId, locationPriority, createTime, updateTime', 'numerical', 'integerOnly'=>true),
            array('locationId, recordPriority, record, recordId, locationPriority, position', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // one location2record belongs to one 'company' record associated
            'company' => array(self::BELONGS_TO,'Company','recordId',
                'on'=>"`Location2Record_Company`.`record`='Company'",
                'alias'=>'Location2Record_Company'
            ),
            // one location2record belongs to one 'location' record associated
            'location' => array(self::BELONGS_TO,'Location','locationId',
                'alias'=>'Location2Record_Location'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'Id',
            'locationId' => 'Location',
            'recordPriority' => 'Record Priority',
            'record' => 'Record',
            'recordId' => 'Record',
            'locationPriority' => 'Location Priority',
            'createTime' => 'Create Time',
            'updateTime' => 'Update Time',
        );
    }

    /**
     * Prepares attributes before performing validation.
     */
    protected function beforeValidate()
    {
        $scenario=$this->getScenario();
        // parent does all common work
        return parent::beforeValidate();
    }

    /**
     * Last model processing before save in db.
     */
    /*protected function beforeSave()
    {
        return true;
    }*/

    /**
     * Returns i18n (translated) representation of the attribute value for view.
     * @param string the attribute name
     * @return string the attribute value's translation
     */
    /*public function getAttributeView($attribute)
    {
    }*/

    /**
     * Returns data array of the attribute for create/update.
     * @param string the attribute name
     * @return array the attribute's data
     */
    public function getAttributeData($attribute)
    {
        switch($attribute)
        {
            case 'companyId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(Company::model()->findAllActiveRecords(array($this->recordId)),'id','title');
            case 'locationId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(Location::model()->findAll(array($this->$attribute)),'id','title');
            default:
                return $this->$attribute;
        }
    }
}