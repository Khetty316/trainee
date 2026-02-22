<?php

namespace frontend\models\office\prodOtMealRecord;

use Yii;
use common\models\User;

/**
 * This is the model class for table "prod_ot_meal_record_item".
 *
 * @property int $id
 * @property int|null $prod_ot_meal_record_detail_id
 * @property int|null $user_id
 * @property int|null $created_by
 * @property string|null $created_at
 *
 * @property User $user
 * @property User $createdBy
 */
class ProdOtMealRecordItem extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'prod_ot_meal_record_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['prod_ot_meal_record_detail_id', 'user_id', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'prod_ot_meal_record_detail_id' => 'Prod Ot Meal Record Detail ID',
            'user_id' => 'User ID',
            'created_by' => 'Created By',
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

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function beforeSave($insert) {
        $this->created_at = new \yii\db\Expression('NOW()');
        $this->created_by = Yii::$app->user->identity->id;

        return parent::beforeSave($insert);
    }
}
