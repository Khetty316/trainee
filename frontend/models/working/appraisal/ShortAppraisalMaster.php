<?php

namespace frontend\models\working\appraisal;

use Yii;
use common\models\User;

/**
 * This is the model class for table "short_appraisal_master".
 *
 * @property int $id
 * @property int $user_id
 * @property string $date
 * @property string|null $content
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property User $user
 */
class ShortAppraisalMaster extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'short_appraisal_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'date','content'], 'required'],
            [['user_id', 'created_by'], 'integer'],
            [['date', 'created_at'], 'safe'],
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
            'date' => 'Date',
            'content' => 'Content',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
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

    public function processAndSave() {
        $this->date = \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($this->date);
        $this->created_by = Yii::$app->user->identity->id;
        $this->created_at = new \yii\db\Expression('NOW()');
        $this->user_id = Yii::$app->user->identity->id;
        return $this->save();
    }



}
