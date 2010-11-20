<?php

class Project extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'Project':
     * @var integer $id
     * @var string $title
     * @var string $excerpt
     * @var string $excerptMarkup
     * @var string $content
     * @var string $contentMarkup
     * @var double $priority
     * @var double $hourlyRate
     * @var string $openDate
     * @var string $closeDate
     * @var integer $createTime
     * @var integer $updateTime
     */

    /**
     * @var integer counters used in the sql queries
     */
    public $countExpense;
    public $countTask;
    public $countTime;

    const PRIORITY_HIGHEST=1;
    const PRIORITY_HIGH=2;
    const PRIORITY_MEDIUM=3;
    const PRIORITY_LOW=4;
    const PRIORITY_LOWEST=5;

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
        return 'project';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('title', 'length', 'max'=>255),
            array('excerptMarkup', 'length', 'max'=>32),
            array('contentMarkup', 'length', 'max'=>32),
            array('priority, createTime, updateTime', 'numerical', 'integerOnly'=>true),
            array('hourlyRate', 'numerical'),
            array('closeDate, content, contentMarkup, hourlyRate, openDate, priority, title', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // many project has many 'company' records associated
            'allCompany' => array(self::MANY_MANY,'Company',
                Company2Project::model()->tableName().'(projectId,companyId)',
                'order'=>"`allCompany_Project_Company`.`companyPriority` ASC, `allCompany_Project_Company`.`id` ASC",
                'alias'=>'Project_Company'
            ),
            // one project has many 'company2project' records associated
            'allCompany2Project' => array(self::HAS_MANY,'Company2Project','projectId',
                'order'=>"`t`.`companyPriority` ASC, `t`.`id` ASC",
                'alias'=>'Project_Company2Project'
            ),
            // many project has many 'consultant' records associated
            'allConsultant' => array(self::MANY_MANY,'User',
                User2Project::model()->tableName().'(projectId,userId)',
                'on'=>"`allConsultant_Project_Consultant`.`role`='".User2Project::CONSULTANT."'",
                'order'=>"`allConsultant_Project_Consultant`.`userPriority` ASC, `allConsultant_Project_Consultant`.`id` ASC",
                'alias'=>'Project_Consultant'
            ),
            // one project has many 'consultant2project' records associated
            'allConsultant2Project' => array(self::HAS_MANY,'User2Project','projectId',
                'on'=>"`t`.`role`='".User2Project::CONSULTANT."'",
                'order'=>"`t`.`userPriority` ASC, `t`.`id` ASC",
                'alias'=>'Project_Consultant2Project'
            ),
            // many project has many 'manager' records associated
            'allManager' => array(self::MANY_MANY,'User',
                User2Project::model()->tableName().'(projectId,userId)',
                'on'=>"`allManager_Project_Manager`.`role`='".User2Project::MANAGER."'",
                'order'=>"`allManager_Project_Manager`.`userPriority` ASC, `allManager_Project_Manager`.`id` ASC",
                'alias'=>'Project_Manager'
            ),
            // one project has many 'manager2project' records associated
            'allManager2Project' => array(self::HAS_MANY,'User2Project','projectId',
                'on'=>"`t`.`role`='".User2Project::MANAGER."'",
                'order'=>"`t`.`userPriority` ASC, `t`.`id` ASC",
                'alias'=>'Project_Manager2Project'
            ),
            // one project has many 'task' records associated
            'allTask' => array(self::HAS_MANY,'Task','projectId',
                'order'=>"`t`.`id` ASC",
                'alias'=>'Project_Task'
            ),
            // many project has many 'user' records associated
            'allUser' => array(self::MANY_MANY,'User',
                User2Project::model()->tableName().'(projectId,userId)',
                'order'=>"`allUser_Project_User`.`userPriority` ASC, `allUser_Project_User`.`id` ASC",
                'alias'=>'Project_User'
            ),
            // one project has many 'user2project' records associated
            'allUser2Project' => array(self::HAS_MANY,'User2Project','projectId',
                'order'=>"`t`.`userPriority` ASC, `t`.`id` ASC",
                'alias'=>'Project_User2Project'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'closeDate'=>Yii::t('t','Close date'),
            'Closed'=>Yii::t('t','Closed[project]'),
            'content'=>Yii::t('t','Description'),
            'createTime'=>Yii::t('t','Record created'),
            'hourlyRate'=>Yii::t('t','Hourly rate'),
            'id'=>Yii::t('t','ID'),
            'openDate'=>Yii::t('t','Open date'),
            'Opened'=>Yii::t('t','Opened[project]'),
            'priority'=>Yii::t('t','Priority'),
            'Rate'=>Yii::t('t','Rate[hourlyRate]'),
            'title'=>Yii::t('t','Title'),
            'updateTime'=>Yii::t('t','Update date'),
            'excerpt' => 'Excerpt',
            'excerptMarkup' => 'Excerpt Markup',
            'contentMarkup' => 'Content Markup',
        );
    }

    /**
     * Prepares attributes before performing validation.
     */
    protected function beforeValidate()
    {
        $scenario=$this->getScenario();
        $priority=(int)$this->priority;
        if(isset($_POST[__CLASS__]['priority']) && $priority!==self::PRIORITY_HIGHEST && $priority!==self::PRIORITY_HIGH
            && $priority!==self::PRIORITY_MEDIUM && $priority!==self::PRIORITY_LOW && $priority!==self::PRIORITY_LOWEST
        )
            // allow defined priorities only
            $this->priority=self::PRIORITY_MEDIUM;
        // avoid sql error 'incorrect date value'
        isset($_POST[__CLASS__]['closeDate']) && ($this->closeDate=MDate::formatToDb($this->closeDate,'date'));
        isset($_POST[__CLASS__]['openDate']) && ($this->openDate=MDate::formatToDb($this->openDate,'date'));
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
    public function getAttributeView($attribute)
    {
        switch($attribute)
        {
            case 'priority':
                switch($this->priority)
                {
                    case self::PRIORITY_HIGHEST:
                        return Yii::t('t','Highest[priority]');
                    case self::PRIORITY_HIGH:
                        return Yii::t('t','High[priority]');
                    /*case null:*/
                    case self::PRIORITY_MEDIUM:
                        return Yii::t('t','Medium[priority]');
                    case self::PRIORITY_LOW:
                        return Yii::t('t','Low[priority]');
                    case self::PRIORITY_LOWEST:
                        return Yii::t('t','Lowest[priority]');
                    default:
                        return $this->priority;
                }
            default:
                return $this->$attribute;
        }
    }

    /**
     * Returns data array of the attribute for create/update.
     * @param string the attribute name
     * @return array the attribute's data
     */
    public function getAttributeData($attribute)
    {
        switch($attribute)
        {
            case 'priority':
                return array(
                    self::PRIORITY_HIGHEST=>Yii::t('t','Highest[priority]'),
                    self::PRIORITY_HIGH=>Yii::t('t','High[priority]'),
                    self::PRIORITY_MEDIUM=>Yii::t('t','Medium[priority]'),
                    self::PRIORITY_LOW=>Yii::t('t','Low[priority]'),
                    self::PRIORITY_LOWEST=>Yii::t('t','Lowest[priority]'),
                );
            default:
                return $this->$attribute;
        }
    }

    /**
     * Find all open projects.
     * @param array of additional conditions
     * @return array of Project objects
     */
    public function findAllOpenRecords($conditions=array())
    {
        $id=(isset($conditions[0]) && ctype_digit($conditions[0]) && $conditions[0]>=1) ? $conditions[0] : null;
        $criteria=new CDbCriteria;
        $t=self::model()->tableName();
        // close date is not set, or it is later than today
        $criteria->condition="`$t`.`closeDate` IS NULL OR TO_DAYS('".MDate::formatToDb(time(),'date')."') < TO_DAYS(`$t`.`closeDate`)";
        if($id)
            $criteria->condition.=" OR `$t`.`id` = '$id'";
        return self::model()->findAll($criteria);
    }
}