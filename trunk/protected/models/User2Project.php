<?php

class User2Project extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'W3User2Project':
     * @var integer $id
     * @var integer $userId
     * @var integer $projectPriority
     * @var integer $projectId
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
        return 'user_2_project';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('role', 'length', 'max'=>64),
            array('userId, projectId', 'required'),
            array('userId, projectPriority, projectId, userPriority, createTime, updateTime', 'numerical', 'integerOnly'=>true),
            array('userId, projectPriority, projectId, userPriority, role', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // one user2project belongs to one 'consultant' record associated
            'consultant' => array(self::BELONGS_TO,'User','userId',
                'on'=>"`User2Project_Consultant`.`role`='".User2Project::CONSULTANT."'",
                'alias'=>'User2Project_Consultant'
            ),
            // one user2project belongs to one 'manager' record associated
            'manager' => array(self::BELONGS_TO,'User','userId',
                'on'=>"`User2Project_Manager`.`role`='".User2Project::MANAGER."'",
                'alias'=>'User2Project_Manager'
            ),
            // one user2project belongs to one 'project' record associated
            'project' => array(self::BELONGS_TO,'Project','projectId',
                'alias'=>'User2Project_Project'
            ),
            // one user2project belongs to one 'user' record associated
            'user' => array(self::BELONGS_TO,'User','userId',
                'alias'=>'User2Project_User'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'consultantId'=>Yii::t('t','Consultant'),
            'id'=>Yii::t('t','ID'),
            'managerId'=>Yii::t('t','Manager'),
            'projectId'=>Yii::t('t','Project'),
            'userId' => 'User',
            'projectPriority' => 'Project Priority',
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
            case 'projectId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(Project::model()->findAllOpenRecords(array($this->$attribute)),'id','title');
            default:
                return $this->$attribute;
        }
    }
}