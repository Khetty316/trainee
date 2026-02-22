<?php

namespace frontend\models\office\prodOtMealRecord;

use Yii;
use common\models\User;

/**
 * This is the model class for table "prod_ot_meal_record_detail".
 *
 * @property int $id
 * @property int|null $prod_ot_meal_record_master_id
 * @property string $receipt_date
 * @property float $receipt_total_amount
 * @property int|null $total_staff
 * @property int|null $deleted_by
 * @property string|null $deleted_at
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property ProdOtMealRecordMaster $prodOtMealRecordMaster
 * @property User $deletedBy
 * @property User $createdBy
 * @property User $updatedBy
 */
class ProdOtMealRecordDetail extends \yii\db\ActiveRecord {

    public $staff;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'prod_ot_meal_record_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['prod_ot_meal_record_master_id', 'total_staff', 'deleted_by', 'created_by', 'updated_by'], 'integer'],
            [['receipt_date', 'receipt_total_amount'], 'required'],
            [['receipt_date', 'deleted_at', 'created_at', 'updated_at'], 'safe'],
            [['receipt_total_amount'], 'number'],
            [['prod_ot_meal_record_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProdOtMealRecordMaster::className(), 'targetAttribute' => ['prod_ot_meal_record_master_id' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deleted_by' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['staff'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'prod_ot_meal_record_master_id' => 'Prod Ot Meal Record Master ID',
            'receipt_date' => 'Receipt Date',
            'receipt_total_amount' => 'Receipt Total Amount (RM)',
            'total_staff' => 'Total Staff',
            'deleted_by' => 'Deleted By',
            'deleted_at' => 'Deleted At',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[ProdOtMealRecordMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdOtMealRecordMaster() {
        return $this->hasOne(ProdOtMealRecordMaster::className(), ['id' => 'prod_ot_meal_record_master_id']);
    }

    /**
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy() {
        return $this->hasOne(User::className(), ['id' => 'deleted_by']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
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

    public function checkReceiptDate() {
        return !self::find()
                        ->where([
                            'created_by' => Yii::$app->user->id,
                            'receipt_date' => $this->receipt_date,
                            'deleted_by' => null,
                            'deleted_at' => null,
                        ])
                        ->exists();
    }
}
