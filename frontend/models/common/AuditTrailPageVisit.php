<?php

namespace frontend\models\common;

use Yii;
use common\models\User;
/**
 * This is the model class for table "audit_trail_page_visit".
 *
 * @property int $id
 * @property string $page
 * @property int|null $user_id
 * @property string $created_at
 *
 * @property User $user
 */
class AuditTrailPageVisit extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'audit_trail_page_visit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['page'], 'required'],
            [['user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['page'], 'string', 'max' => 100],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'page' => 'Page',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
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

    public static function addRecord($page, $userId) {
        $trail = new AuditTrailPageVisit();
        $trail->page = $page;
        $trail->user_id = $userId;
        $trail->save();
    }

}
