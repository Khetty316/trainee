<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "test_detail_attendance".
 *
 * @property int $id
 * @property int|null $form_attendance_id
 * @property string|null $name
 * @property string|null $org
 * @property string|null $designation
 * @property resource|null $signature
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TestFormAttendance $formAttendance
 */
class TestDetailAttendance extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_detail_attendance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['form_attendance_id', 'name', 'org', 'designation', 'signature', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['form_attendance_id', 'created_by', 'updated_by'], 'integer'],
            [['signature'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'org', 'designation'], 'string', 'max' => 255],
            [['form_attendance_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestFormAttendance::class, 'targetAttribute' => ['form_attendance_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'form_attendance_id' => 'Form Attendance ID',
            'name' => 'Name',
            'org' => 'Org',
            'designation' => 'Designation',
            'signature' => 'Signature',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[FormAttendance]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFormAttendance() {
        return $this->hasOne(TestFormAttendance::class, ['id' => 'form_attendance_id']);
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
                ->select(['name as value', 'id as id', 'name as label', 'org', 'designation', 'signature'])
                ->groupBy('name')
                ->asArray()
                ->all();
        return $list;
    }

}
