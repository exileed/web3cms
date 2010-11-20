<?php

class User2Company extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'W3User2Company':
     * @var integer $id
     * @var integer $userId
     * @var integer $companyPriority
     * @var integer $companyId
     * @var integer $userPriority
     * @var string $position
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
        return 'user_2_company';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('position', 'length', 'max'=>128),
            array('userId, companyId', 'required'),
            array('userId, companyPriority, companyId, userPriority, createTime, updateTime', 'numerical', 'integerOnly'=>true),
            array('userId, companyPriority, companyId, userPriority, position', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // one user2company belongs to one 'company' record associated
            'company' => array(self::BELONGS_TO,'Company','companyId',
                'alias'=>'User2Company_Company'
            ),
            // one user2company belongs to one 'user' record associated
            'user' => array(self::BELONGS_TO,'User','userId',
                'alias'=>'User2Company_User'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'companyId'=>Yii::t('t','Company'),
            'id'=>Yii::t('t','ID'),
            'position'=>Yii::t('t','Position'),
            'userId'=>Yii::t('t','Owner'),//'Representative'
            'companyPriority' => 'Company Priority',
            'userPriority' => 'User Priority',
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
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(Company::model()->findAllActiveRecords(array($this->$attribute)),'id','title');
            case 'userId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(User::model()->findAllActiveRecords(array($this->$attribute)),'id','screenName');
            default:
                return $this->$attribute;
        }
    }
}