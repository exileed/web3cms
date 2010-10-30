<?php

class Company extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'W3Company':
     * @var integer $id
     * @var string $title
     * @var string $titleAbbr
     * @var string $excerpt
     * @var string $excerptMarkup
     * @var string $content
     * @var string $contentMarkup
     * @var string $contactName
     * @var string $contactEmail
     * @var integer $invoiceDueDay
     * @var string $isActive
     * @var integer $deactivationTime
     * @var integer $createTime
     * @var integer $updateTime
     */

    const IS_ACTIVE='1';
    const IS_NOT_ACTIVE='0';
    const OWNER='owner';

    /**
     * @var integer counters used in the sql queries
     */
    public $countCompanyPayment;
    public $countExpense;
    public $countInvoice;
    public $countProject;
    public $countTask;
    public $countTime;

    private $_user2Company;

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
        return 'company';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $retval=array();
        $retval[]=array('title', 'length', 'max'=>255);
        $retval[]=array('titleAbbr', 'length', 'max'=>128);
        $retval[]=array('excerptMarkup', 'length', 'max'=>32);
        $retval[]=array('contentMarkup', 'length', 'max'=>32);
        $retval[]=array('contactName', 'length', 'max'=>255);
        $retval[]=array('contactEmail', 'length', 'max'=>255);
        $retval[]=array('deactivationTime, invoiceDueDay, createTime, updateTime', 'numerical', 'integerOnly'=>true);
        // contactEmail has to be a valid email address
        $retval[]=array('contactEmail', 'email');
        $retval[]=array('contactEmail, contactName, content, contentMarkup, title, titleAbbr', 'safe');
        if(Yii::app()->user->checkAccess(User::MANAGER) || Yii::app()->user->checkAccess(User::ADMINISTRATOR))
            $retval[]=array('deactivationTime, invoiceDueDay, isActive', 'safe');
        return $retval;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // one company has many 'company2project' records associated
            'allCompany2Project' => array(self::HAS_MANY,'Company2Project','companyId',
                'order'=>"`t`.`projectPriority` ASC, `t`.`id` ASC",
                'alias'=>'Company_Company2Project'
            ),
            // many company has many 'location' records associated
            'allLocation' => array(self::MANY_MANY,'Location',
                Location2Record::model()->tableName().'(recordId,locationId)',
                'on'=>"`allLocation_Company_Location`.`record`='Company'",
                'order'=>"`allLocation_Company_Location`.`locationPriority` ASC, `allLocation_Company_Location`.`id` ASC",
                'alias'=>'Company_Location'
            ),
            // one company has many 'location2record' records associated
            'allLocation2Record' => array(self::HAS_MANY,'Location2Record','recordId',
                'on'=>"`t`.`record`='Company'",
                'order'=>"`t`.`locationPriority` ASC, `t`.`id` ASC",
                'alias'=>'Company_Location2Record'
            ),
            // many company has many 'project' records associated
            'allProject' => array(self::MANY_MANY,'Project',
                Company2Project::model()->tableName().'(companyId,projectId)',
                'order'=>"`allProject_Company_Project`.`projectPriority` ASC, `allProject_Company_Project`.`id` ASC",
                'alias'=>'Company_Project'
            ),
            // one company has many 'task' records associated
            'allTask' => array(self::HAS_MANY,'Task','companyId',
                'order'=>"`t`.`id` ASC",
                'alias'=>'Company_Task'
            ),
            // many company has many 'user' records associated
            'allUser' => array(self::MANY_MANY,'User',
                User2Company::model()->tableName().'(companyId,userId)',
                'order'=>"`allUser_Company_User`.`userPriority` ASC, `allUser_Company_User`.`id` ASC",
                'alias'=>'Company_User'
            ),
            // one company has many 'user2company' records associated
            'allUser2Company' => array(self::HAS_MANY,'User2Company','companyId',
                //'order'=>"`t`.`userPriority` ASC, `t`.`id` ASC", // not working on task/grid page. did it work at all?
                'order'=>"`Company_User2Company`.`userPriority` ASC, `Company_User2Company`.`id` ASC",
                'alias'=>'Company_User2Company'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'Abbr'=>Yii::t('t','Abbr.'),
            'contactName'=>Yii::t('t','Contact person'),
            'contactEmail'=>Yii::t('t','Email address'),
            'content'=>Yii::t('t','Description'),
            'createTime'=>Yii::t('t','Record created'),
            'Closed'=>Yii::t('t','Closed[company]'),
            'deactivationTime'=>Yii::t('t','Deactivation date'),
            'id'=>Yii::t('t','ID'),
            'invoiceDueDay'=>Yii::t('t','Invoice due'),
            'isActive'=>Yii::t('t','Company is active'),
            'Opened'=>Yii::t('t','Opened[company]'),
            'title'=>Yii::t('t','Title'),
            'titleAbbr'=>Yii::t('t','Title abbr.'),
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
        if(isset($_POST[__CLASS__]['isActive']))
        {
            if($this->isActive!==self::IS_ACTIVE && $this->isActive!==self::IS_NOT_ACTIVE)
                // enum('0','1') null
                $this->isActive=null;
            if($this->isActive===self::IS_ACTIVE && $this->deactivationTime!==null)
                // unset time on activate
                $this->deactivationTime=null;
            else if(($this->isActive===self::IS_NOT_ACTIVE || $this->isActive===null) && empty($this->deactivationTime))
                // set time on deactivate
                $this->deactivationTime=time();
        }
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
            case 'isActive':
                switch($this->isActive)
                {
                    case self::IS_ACTIVE:
                        return Yii::t('attr','Yes (The record will be shown in search results)');
                    case self::IS_NOT_ACTIVE:
                        return Yii::t('attr','No (The record will not be shown in search results)');
                    case null:
                        return Yii::t('attr','By default (The record will not be shown in search results)');
                    default:
                        return $this->isActive;
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
            case 'isActive':
                return array(
                    null=>Yii::t('attr','By default (The record will not be shown in search results)'),
                    self::IS_NOT_ACTIVE=>Yii::t('attr','No (The record will not be shown in search results)'),
                    self::IS_ACTIVE=>Yii::t('attr','Yes (The record will be shown in search results)'),
                );
            default:
                return $this->$attribute;
        }
    }

    /**
     * Find all active companies.
     * @param array of additional conditions
     * @return array of Company objects
     */
    public function findAllActiveRecords($conditions=array())
    {
        $id=(isset($conditions[0]) && ctype_digit($conditions[0]) && $conditions[0]>=1) ? $conditions[0] : null;
        $criteria=new CDbCriteria;
        $t=self::model()->tableName();
        $criteria->condition="`$t`.`isActive`='".self::IS_ACTIVE."'";
        if($id)
            $criteria->condition.=" OR `$t`.`id` = '$id'";
        return self::model()->findAll($criteria);
    }

    /**
     * Whether user is company owner.
     * @param integer company id
     * @param integer user id
     * @return boolean
     */
    public function isOwner($companyId=null,$userId=null)
    {
        // set and validate parameters
        if($companyId===null)
            $companyId=$this->id;
        if($userId===null && !Yii::app()->user->isGuest)
            $userId=Yii::app()->user->id;
        if($companyId===null || $userId===null)
            return false;
        if(!isset($this->_user2Company[$userId][$companyId]))
        {
            // save relation between user and company to access it in the future
            if(($user2Company=User2Company::model()->findByAttributes(array('userId'=>$userId,'companyId'=>$companyId)))!==null)
                $this->_user2Company[$userId][$companyId]['position']=$user2Company->position;
            else
                $this->_user2Company[$userId][$companyId]=null;
        }
        // relation must exist and user position should be owner
        return $this->_user2Company[$userId][$companyId]!==null && $this->_user2Company[$userId][$companyId]['position']===self::OWNER;
    }
}