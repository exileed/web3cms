<?php

class Location extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'Location':
     * @var integer $id
     * @var string $title
     * @var string $excerpt
     * @var string $excerptMarkup
     * @var string $content
     * @var string $contentMarkup
     * @var string $address1
     * @var string $address2
     * @var string $city
     * @var string $state
     * @var string $country
     * @var string $zipcode
     * @var string $latitude
     * @var string $longitude
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
        return 'location';
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
            array('address1', 'length', 'max'=>255),
            array('address2', 'length', 'max'=>255),
            array('city', 'length', 'max'=>128),
            array('state', 'length', 'max'=>111),
            array('country', 'length', 'max'=>111),
            array('zipcode', 'length', 'max'=>64),
            array('latitude', 'length', 'max'=>11),
            array('longitude', 'length', 'max'=>11),
            array('createTime, updateTime', 'numerical', 'integerOnly'=>true),
            array('address1, address2, city, content, contentMarkup, country, latitude, longitude, state, title, zipcode', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // many location has many 'company' records associated
            'allCompany' => array(self::MANY_MANY,'Company',
                Location2Record::model()->tableName().'(locationId,recordId)',
                'on'=>"`allCompany_Location_Company`.`record`='Company'",
                'order'=>"`allCompany_Location_Company`.`recordPriority` ASC, `allCompany_Location_Company`.`id` ASC",
                'alias'=>'Location_Company'
            ),
            // one location has many 'location2record' records associated
            'allLocation2Record' => array(self::HAS_MANY,'Location2Record','locationId',
                'order'=>"`t`.`recordPriority` ASC, `t`.`id` ASC",
                'alias'=>'Location_Location2Record'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'address1'=>Yii::t('t','Address line 1'),
            'address2'=>Yii::t('t','Address line 2'),
            'city'=>Yii::t('t','City'),
            'content'=>Yii::t('t','Content'),
            'country'=>Yii::t('t','Country'),
            'createTime'=>Yii::t('t','Record created'),
            'id'=>Yii::t('t','ID'),
            'latitude'=>Yii::t('t','Latitude'),
            'longitude'=>Yii::t('t','Longitude'),
            'state'=>Yii::t('t','State'),
            'title'=>Yii::t('t','Title'),
            'updateTime'=>Yii::t('t','Update date'),
            'zipcode'=>Yii::t('t','ZIP code'),
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
    /*public function getAttributeData($attribute)
    {
    }*/
}