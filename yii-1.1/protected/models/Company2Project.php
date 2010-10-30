<?php

class Company2Project extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'W3Company2Project':
     * @var integer $id
     * @var integer $companyId
     * @var integer $projectPriority
     * @var integer $projectId
     * @var integer $companyPriority
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
        return 'company_2_project';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('companyId, projectId', 'required'),
            array('companyId, projectPriority, projectId, companyPriority, createTime, updateTime', 'numerical', 'integerOnly'=>true),
            array('companyId, projectPriority, projectId, companyPriority', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // one company2project belongs to one 'project' record associated
            'company' => array(self::BELONGS_TO,'Company','companyId',
                'alias'=>'Company2Project_Company'
            ),
            // one company2project belongs to one 'company' record associated
            'project' => array(self::BELONGS_TO,'Project','projectId',
                'alias'=>'Company2Project_Project'
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
            'projectId'=>Yii::t('t','Project'),
            'projectPriority' => 'Project Priority',
            'companyPriority' => 'Company Priority',
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
            case 'projectId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(Project::model()->findAllOpenRecords(array($this->$attribute)),'id','title');
            default:
                return $this->$attribute;
        }
    }
}