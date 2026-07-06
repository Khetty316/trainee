<?php

namespace frontend\models\client;

use common\models\User;
use Yii;

/**
 * This is the model class for table "client_reminder_letter_template".
 *
 * @property int $id
 * @property string|null $letter_name
 * @property string|null $content
 * @property int|null $active_sts 0 = yes , 1 = no
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 */
class ClientReminderLetterTemplate extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public $template_id;

    public static function tableName() {
        return 'client_reminder_letter_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['content'], 'string'],
            [['active_sts'], 'integer'],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
            [['letter_name'], 'string', 'max' => 255],
            [['letter_name', 'content', 'active_sts'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'letter_name' => 'Letter Name',
            'content' => 'Content',
            'active_sts' => 'Active',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $userId = Yii::$app->user->id ?? 1;

        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->id;
        } else {
            $this->updated_by = Yii::$app->user->id ?? 1;
            $this->updated_at = new \yii\db\Expression('NOW()');
        }

        return true;
    }

    public function getCreator() {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdater() {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
