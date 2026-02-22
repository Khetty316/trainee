<?php

namespace frontend\models\resume;

use Yii;
use common\models\User;

/**
 * This is the model class for table "resume_academic_qualifications".
 *
 * @property int $id
 * @property int $user_id
 * @property string $academic_level
 * @property string $academic_institution
 * @property string $academic_course
 * @property string $academic_period
 * @property string|null $academic_honour
 * @property int|null $sort
 * @property int $active_sts
 * @property string $created_at
 * @property int $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property User $user
 */
class ResumeAcademicQualifications extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'resume_academic_qualifications';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'academic_level', 'academic_institution', 'academic_course', 'academic_period', 'created_by'], 'required'],
            [['user_id', 'sort', 'active_sts', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['academic_level'], 'string', 'max' => 50],
            [['academic_institution', 'academic_course', 'academic_period', 'academic_honour'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'academic_level' => 'Academic Level',
            'academic_institution' => 'Academic Institution',
            'academic_course' => 'Academic Course',
            'academic_period' => 'Academic Period',
            'academic_honour' => 'Academic Honour',
            'sort' => 'Sort',
            'active_sts' => 'Active Sts',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public function processAndSave() {
        $sort = (ResumeAcademicQualifications::find()->where("user_id=" . Yii::$app->user->id)->count()) + 1;
        $this->user_id = Yii::$app->user->id;
        $this->sort = $sort;
        return $this->save(false);
    }

    public function updateSort($sorting) {
        $this->sort = $sorting;
        $this->update(false);
    }

}
