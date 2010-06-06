<?php

class forumSections extends _CActiveRecord
{
	/**
	 * The followings are the available columns in table 'w3_forum_sections':
	 * @var integer $id
	 * @var integer $parentId
	 * @var string $name
	 * @var string $description
	 * @var integer $topicCount
	 * @var integer $postCount
	 * @var integer $position
	 * @var integer $isActive
	 * @var integer $accessLevel
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
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'w3_forum_sections';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, position', 'required'),
			array('parentId, position, isActive, accessLevel', 'numerical', 'integerOnly'=>true),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, parentId, name, description, position, isActive, accessLevel', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
                    'topic'=>array(self::HAS_MANY,'forumTopics','sectionId'),
                    'post'=>array(self::HAS_MANY,'forumPosts','sectionId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'parentId' => 'Parent',
			'name' => 'Name',
			'description' => 'Description',
			'position' => 'Position',
			'isActive' => 'Is Active',
			'accessLevel' => 'Access Level',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{

	}
}