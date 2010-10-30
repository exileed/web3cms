<?php

class User2Task extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'W3User2Task':
     * @var integer $id
     * @var integer $userId
     * @var integer $taskPriority
     * @var integer $taskId
     * @var integer $userPriority
     * @var string $role
     * @var integer $createTime
     * @var integer $updateTime
     */

    const CONSULTANT='consultant';
    const MANAGER='manager';

    /**
     * @var integer alias of userId
     */
    public $consultantId;

    /**
     * @var integer alias of userId
     */
    public $managerId;

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
        return 'user_2_task';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('role', 'length', 'max'=>64),
            array('userId, taskId', 'required'),
            array('userId, taskPriority, taskId, userPriority, createTime, updateTime', 'numerical', 'integerOnly'=>true),
            array('userId, taskPriority, taskId, userPriority, role', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // one user2task belongs to one 'consultant' record associated
            'consultant' => array(self::BELONGS_TO,'User','userId',
                'on'=>"`User2Task_Consultant`.`role`='consultant'",
                'alias'=>'User2Task_Consultant'
            ),
            // one user2task belongs to one 'manager' record associated
            'manager' => array(self::BELONGS_TO,'User','userId',
                'on'=>"`User2Task_Manager`.`role`='".User2Task::MANAGER."'",
                'alias'=>'User2Task_Manager'
            ),
            // one user2task belongs to one 'task' record associated
            'task' => array(self::BELONGS_TO,'Task','taskId',
                'alias'=>'User2Task_Task'
            ),
            // one user2task belongs to one 'user' record associated
            'user' => array(self::BELONGS_TO,'User','userId',
                'alias'=>'User2Task_User'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'consultantId'=>Yii::t('t','Leader'),//'Consultant'
            'id'=>Yii::t('t','ID'),
            'managerId'=>Yii::t('t','Manager'),
            'taskId'=>Yii::t('t','Task'),
            'userId' => 'User',
            'taskPriority' => 'Task Priority',
            'userPriority' => 'User Priority',
            'role' => 'Role',
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
            case 'consultantId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(User::model()->findAllActiveRecords(array($this->userId)),'id','screenName');
            case 'managerId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(User::model()->findAllActiveRecords(array($this->userId)),'id','screenName');
            case 'taskId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(Task::model()->findAllOpenRecords(array($this->$attribute)),'id','title');
            default:
                return $this->$attribute;
        }
    }
}