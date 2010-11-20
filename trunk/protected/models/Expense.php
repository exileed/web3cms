<?php

class Expense extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'w3expense':
     * @var integer $id
     * @var integer $managerId
     * @var integer $companyId
     * @var integer $projectId
     * @var integer $invoiceId
     * @var string $title
     * @var string $excerpt
     * @var string $excerptMarkup
     * @var string $content
     * @var string $contentMarkup
     * @var string $expenseDate
     * @var integer $amount
     * @var string $billToCompany
     * @var integer $createTime
     * @var integer $updateTime
     */

    const BILL_TO_COMPANY='1';
    const DO_NOT_BILL_TO_COMPANY='0';

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
        return 'expense';
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
            array('managerId, companyId, projectId, invoiceId, createTime, updateTime', 'numerical', 'integerOnly'=>true),
            array('amount', 'numerical'),
            array('amount, billToCompany, companyId, content, contentMarkup, expenseDate, projectId, title', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // one expense belongs to one 'company' record associated
            'company' => array(self::BELONGS_TO,'Company','companyId',
                'alias'=>'Expense_Company'
            ),
            // one expense belongs to one 'invoice' record associated
            'invoice' => array(self::BELONGS_TO,'Invoice','invoiceId',
                'alias'=>'Expense_Invoice'
            ),
            // one expense belongs to one 'manager' record associated
            'manager' => array(self::BELONGS_TO,'User','managerId',
                'alias'=>'Expense_Manager'
            ),
            // one expense belongs to one 'project' record associated
            'project' => array(self::BELONGS_TO,'Project','projectId',
                'alias'=>'Expense_Project'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'amount'=>Yii::t('t','Amount'),
            'billToCompany'=>Yii::t('t','Bill to company'),
            'Bill'=>Yii::t('t','Bill[to company]'),
            'companyId'=>Yii::t('t','Company'),
            'content'=>Yii::t('t','Details'),
            'createTime'=>Yii::t('t','Record created'),
            'Date'=>Yii::t('t','Date'),
            'expenseDate'=>Yii::t('t','Expense date'),
            'id'=>Yii::t('t','ID'),
            'invoiceId'=>Yii::t('t','Invoice'),
            'managerId'=>Yii::t('t','Manager'),
            'projectId'=>Yii::t('t','Project'),
            'title'=>Yii::t('t','Note'),
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
        if(isset($_POST[__CLASS__]['billToCompany']) && $this->billToCompany!==self::BILL_TO_COMPANY && $this->billToCompany!==self::DO_NOT_BILL_TO_COMPANY)
            // enum('0','1') null
            $this->billToCompany=null;
        // avoid sql error 'incorrect date value'
        isset($_POST[__CLASS__]['expenseDate']) && ($this->expenseDate=MDate::formatToDb($this->expenseDate,'date'));
        // parent does all common work
        return parent::beforeValidate();
    }

    /**
     * Last model processing before save in db.
     */
    /*protected function beforeSave()
    {
    }*/

    /**
     * Returns i18n (translated) representation of the attribute value for view.
     * @param string the attribute name
     * @param string the action Id
     * @return string the attribute value's translation
     */
    public function getAttributeView($attribute,$actionId='show')
    {
        switch($attribute)
        {
            case 'billToCompany':
                switch($this->billToCompany)
                {
                    case self::BILL_TO_COMPANY:
                        return $actionId==='show' ? Yii::t('attr','Yes (Bill to company)') : Yii::t('t','Yes');
                    case self::DO_NOT_BILL_TO_COMPANY:
                        return $actionId==='show' ? Yii::t('attr','No (Do not bill to company)') : Yii::t('t','No');
                    case null:
                        return $actionId==='show' ? Yii::t('attr','By default (Do not bill to company)') : Yii::t('t','No');
                    default:
                        return $this->billToCompany;
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
            case 'billToCompany':
                return array(
                    null=>Yii::t('attr','By default (Do not bill to company)'),
                    self::DO_NOT_BILL_TO_COMPANY=>Yii::t('attr','No (Do not bill to company)'),
                    self::BILL_TO_COMPANY=>Yii::t('attr','Yes (Bill to company)'),
                );
            case 'companyId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(Company::model()->findAllActiveRecords(array($this->$attribute)),'id','title');
            case 'managerId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(User::model()->findAllActiveRecords(array($this->$attribute)),'id','screenName');
            case 'projectId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(Project::model()->findAllOpenRecords(array($this->$attribute)),'id','title');
            default:
                return $this->$attribute;
        }
    }
}