<?php

namespace frontend\models\office\employeeHandbook;

use Yii;
use common\models\User;
use frontend\models\RefTravelLocation;
use frontend\models\RefStaffGrade;
use frontend\models\office\employeeHandbook\EmployeeHandbookMaster;

/**
 * This is the model class for table "eh_travel_allowance_detail".
 *
 * @property int $id
 * @property int|null $eh_travel_allowance_master_id
 * @property int|null $eh_master_id
 * @property string|null $grade
 * @property string|null $location_type
 * @property float|null $amount_per_day
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property EmployeeHandbookMaster $ehTravelAllowanceMaster
 * @property RefTravelLocation $locationType
 * @property User $createdBy
 * @property User $updatedBy
 * @property RefStaffGrade $grade0
 * @property EmployeeHandbookMaster $ehMaster
 */
class EhTravelAllowanceDetail extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'eh_travel_allowance_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['eh_travel_allowance_master_id', 'eh_master_id', 'created_by', 'updated_by'], 'integer'],
            [['amount_per_day'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['grade', 'location_type'], 'string', 'max' => 100],
            [['eh_travel_allowance_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmployeeHandbookMaster::className(), 'targetAttribute' => ['eh_travel_allowance_master_id' => 'id']],
            [['location_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefTravelLocation::className(), 'targetAttribute' => ['location_type' => 'code']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['grade'], 'exist', 'skipOnError' => true, 'targetClass' => RefStaffGrade::className(), 'targetAttribute' => ['grade' => 'code']],
            [['eh_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmployeeHandbookMaster::className(), 'targetAttribute' => ['eh_master_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'eh_travel_allowance_master_id' => 'Eh Travel Allowance Master ID',
            'eh_master_id' => 'Eh Master ID',
            'grade' => 'Grade',
            'location_type' => 'Location Type',
            'amount_per_day' => 'Amount Per Day',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[EhTravelAllowanceMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEhTravelAllowanceMaster() {
        return $this->hasOne(EmployeeHandbookMaster::className(), ['id' => 'eh_travel_allowance_master_id']);
    }

    /**
     * Gets query for [[LocationType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocationType() {
        return $this->hasOne(RefTravelLocation::className(), ['code' => 'location_type']);
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

    /**
     * Gets query for [[Grade0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGrade0() {
        return $this->hasOne(RefStaffGrade::className(), ['code' => 'grade']);
    }

    /**
     * Gets query for [[EhMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEhMaster() {
        return $this->hasOne(EmployeeHandbookMaster::className(), ['id' => 'eh_master_id']);
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
}
