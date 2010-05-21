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
			array('parentId, topicCount, postCount, position, isActive, accessLevel', 'numerical', 'integerOnly'=>true),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, parentId, name, description, topicCount, postCount, position, isActive, accessLevel', 'safe', 'on'=>'search'),
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
                    'sectionId'=>array(self::HAS_MANY,'forumTopics','sectionId')
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
			'topicCount' => 'Topic Count',
			'postCount' => 'Post Count',
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
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('parentId',$this->parentId);

		$criteria->compare('name',$this->name,true);

		$criteria->compare('description',$this->description,true);

		$criteria->compare('topicCount',$this->topicCount);

		$criteria->compare('postCount',$this->postCount);

		$criteria->compare('position',$this->position);

		$criteria->compare('isActive',$this->isActive);

		$criteria->compare('accessLevel',$this->accessLevel);

		return new CActiveDataProvider('w3_forum_sections', array(
			'criteria'=>$criteria,
		));
	}
}