<?php

namespace frontend\models\resume;

use Yii;
use common\models\User;

/**
 * This is the model class for table "resume_project_ref".
 *
 * @property int $id
 * @property int $user_id
 * @property string $project_detail
 * @property int|null $sort
 * @property int $active_sts
 * @property string $created_at
 * @property int $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property User $user
 */
class ResumeProjectRef extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'resume_project_ref';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'project_detail', 'created_by'], 'required'],
            [['user_id', 'sort', 'active_sts', 'created_by', 'updated_by'], 'integer'],
            [['project_detail'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
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
            'project_detail' => 'Project Description',
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
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public function processAndSave() {
        $sort = (ResumeProjectRef::find()->where("user_id=" . Yii::$app->user->id)->count()) + 1;
        $this->user_id = Yii::$app->user->id;
        $this->sort = $sort;
        return $this->save(false);
    }

    public function updateSort($sorting) {
        $this->sort = $sorting;
        $this->update(false);
    }

}
