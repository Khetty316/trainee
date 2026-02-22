<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "ref_test_attendees_roles".
 *
 * @property int $int
 * @property string|null $name
 * @property string|null $description
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class RefTestAttendeesRoles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_test_attendees_roles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['name', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'int' => 'Int',
            'name' => 'Name',
            'description' => 'Description',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }
}
