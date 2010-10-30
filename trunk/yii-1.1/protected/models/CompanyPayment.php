<?php

class CompanyPayment extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'w3companypayment':
     * @var integer $id
     * @var integer $companyId
     * @var string $title
     * @var string $excerpt
     * @var string $excerptMarkup
     * @var string $content
     * @var string $contentMarkup
     * @var string $paymentDate
     * @var integer $amount
     * @var string $paymentMethod
     * @var string $paymentNumber
     * @var integer $createTime
     * @var integer $updateTime
     */

    const TEXT='text';
    const CASH='cash';
    const CHECK='check';
    const CREDIT_CARD='creditCard';
    const PAYPAL='paypal';
    const WIRE='wire';

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
        return 'company_payment';
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
            array('paymentMethod', 'length', 'max'=>32),
            array('paymentNumber', 'length', 'max'=>32),
            array('companyId, createTime, updateTime', 'numerical', 'integerOnly'=>true),
            array('amount', 'numerical'),
            array('amount, companyId, content, contentMarkup, paymentDate, paymentMethod, paymentNumber, title', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // one companyPayment belongs to one 'company' record associated
            'company' => array(self::BELONGS_TO,'Company','companyId',
                'alias'=>'CompanyPayment_Company'
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
            'companyId'=>Yii::t('t','Company'),
            'content'=>Yii::t('t','Details'),
            'createTime'=>Yii::t('t','Record created'),
            'Date'=>Yii::t('t','Date'),
            'id'=>Yii::t('t','ID'),
            'Method'=>Yii::t('payment','Method'),
            'Number'=>Yii::t('payment','Number'),
            'paymentDate'=>Yii::t('t','Payment date'),
            'paymentMethod'=>Yii::t('t','Payment method'),
            'paymentNumber'=>Yii::t('t','Payment number'),
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
        if(isset($_POST[__CLASS__]['contentMarkup']) && $this->contentMarkup!==self::TEXT)
            // allow defined markups only
            $this->contentMarkup=self::CHECK;
        if(isset($_POST[__CLASS__]['content'],$_POST[__CLASS__]['contentMarkup']) && empty($this->content))
            // empty text needs no markup
            $this->contentMarkup=null;
        if(isset($_POST[__CLASS__]['paymentMethod']) && $this->paymentMethod!==self::CASH && $this->paymentMethod!==self::CHECK
            && $this->paymentMethod!==self::CREDIT_CARD && $this->paymentMethod!==self::PAYPAL && $this->paymentMethod!==self::WIRE
        )
            // allow defined methods only
            $this->paymentMethod=self::CHECK;
        // avoid sql error 'incorrect date value'
        isset($_POST[__CLASS__]['paymentDate']) && ($this->paymentDate=MDate::formatToDb($this->paymentDate,'date'));
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
            case 'paymentMethod':
                switch($this->paymentMethod)
                {
                    case self::CASH:
                        return Yii::t('payment','Cash');
                    case self::CHECK:
                        return Yii::t('payment','Check');
                    case self::CREDIT_CARD:
                        return Yii::t('payment','Credit card');
                    case self::PAYPAL:
                        return Yii::t('payment','Paypal');
                    case self::WIRE:
                        return Yii::t('payment','Wire');
                    default:
                        return $this->paymentMethod;
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
            case 'companyId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(Company::model()->findAllActiveRecords(array($this->$attribute)),'id','title');
            case 'contentMarkup':
                return array(
                    self::TEXT=>Yii::t('t','Text'),
                );
            case 'paymentMethod':
                return array(
                    self::CASH=>Yii::t('payment','Cash'),
                    self::CHECK=>Yii::t('payment','Check'),
                    self::CREDIT_CARD=>Yii::t('payment','Credit card'),
                    self::PAYPAL=>Yii::t('payment','Paypal'),
                    self::WIRE=>Yii::t('payment','Wire'),
                );
            default:
                return $this->$attribute;
        }
    }
}