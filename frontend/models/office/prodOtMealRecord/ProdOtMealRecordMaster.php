<?php

namespace frontend\models\office\prodOtMealRecord;

use Yii;
use common\models\User;
use frontend\models\office\prodOtMealRecord\ProdOtMealRecordDetail;

/**
 * This is the model class for table "prod_ot_meal_record_master".
 *
 * @property int $id
 * @property string|null $ref_code
 * @property int|null $month
 * @property int|null $year
 * @property string|null $dateFrom
 * @property string|null $dateTo
 * @property float|null $total_amount
 * @property int|null $status 0 = not finalize, 1 = finalize, 2 = claim submitted, 3 = deleted
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $deleted_by
 * @property string|null $deleted_at
 *
 * @property ProdOtMealRecordDetail[] $prodOtMealRecordDetails
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $deletedBy
 */
class ProdOtMealRecordMaster extends \yii\db\ActiveRecord
{
    CONST Prefix_RefCode = "POTM";
    CONST runningNoLength = 5;
    
    CONST STATUS_NOT_FINALIZE = 0;
    CONST STATUS_FINALIZE = 1;
    CONST STATUS_CLAIM_SUBMITTED = 2;
    CONST STATUS_DELETED = 3;
    
    const PERSONAL_USER_MANUAL_FILENAME = "T6B2c-Production Overtime Meal Record-00.pdf"; 
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prod_ot_meal_record_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['month', 'year', 'status', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['dateFrom', 'dateTo', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['total_amount'], 'number'],
            [['ref_code'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deleted_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ref_code' => 'Reference Code',
            'month' => 'Month',
            'year' => 'Year',
            'dateFrom' => 'Date From',
            'dateTo' => 'Date To',
            'total_amount' => 'Total Amount',
            'status' => 'Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'deleted_by' => 'Deleted By',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * Gets query for [[ProdOtMealRecordDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdOtMealRecordDetails()
    {
        return $this->hasMany(ProdOtMealRecordDetail::className(), ['prod_ot_meal_record_master_id' => 'id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'deleted_by']);
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
    
    public function generateRefCode() {
        $currentYear = date("Y");
        $currentMonth = date("m");
        $currentYearShort = date("y");

        $initialRefCode = self::Prefix_RefCode;
        $query = self::find()->where(['year' => $currentYear]);

        $runningNo = $query->count() + 1;
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        $refCode = $initialRefCode . $runningNo . "-" . $currentMonth . $currentYearShort; 

        return $refCode;
    }
    
    public function updateTotalAmountMaster(){
        $totalAmount = ProdOtMealRecordDetail::find()->where(['prod_ot_meal_record_master_id' => $this->id])->sum('receipt_total_amount');
        $this->total_amount = $totalAmount;
        
        if(!$this->update()){
            return false;
        }else{
            return true;
        }
    }
}
