<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "test_item_attendees".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $org
 * @property string|null $designation
 * @property string|null $role
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class TestItemAttendees extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_item_attendees';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name', 'org', 'designation', 'role', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['name', 'org', 'designation', 'role'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'org' => 'Org',
            'designation' => 'Designation',
            'role' => 'Role',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public static function getAutoCompleteList() {
        $list = self::find()
                ->select(['name as value', 'id as id', 'name as label', 'org', 'designation'])
                ->groupBy('name')
                ->asArray()
                ->all();
        return $list;
    }

}
