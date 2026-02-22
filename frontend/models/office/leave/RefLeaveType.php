<?php

namespace frontend\models\office\leave;

use Yii;

/**
 * This is the model class for table "ref_leave_type".
 *
 * @property string $leave_type_code
 * @property string|null $leave_type_name
 * @property int $order
 * @property int $is_active
 * @property int|null $default_days
 * @property int|null $is_pro_rata
 * @property string $created_at
 * @property string|null $created_by
 *
 * @property LeaveEntitlementDetails[] $leaveEntitlementDetails
 * @property LeaveMaster[] $leaveMasters
 */
class RefLeaveType extends \yii\db\ActiveRecord {

    CONST codeAnnual = "annual";
    CONST codeCompassion = "compasion";
    CONST codeMatern = "matern";
    CONST codeMatrimonial = "matrim";
    CONST codePaternal = "patern";
    CONST codeUnpaid = "unpaid";
    CONST codeSick = "sick";
    CONST codeEmergency = "emergency";
    CONST codeTravel = "travel";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_leave_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['leave_type_code'], 'required'],
            [['order', 'is_active', 'default_days', 'is_pro_rata'], 'integer'],
            [['created_at'], 'safe'],
            [['leave_type_code'], 'string', 'max' => 10],
            [['leave_type_name'], 'string', 'max' => 255],
            [['created_by'], 'string', 'max' => 11],
            [['leave_type_code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'leave_type_code' => 'Leave Type Code',
            'leave_type_name' => 'Leave Type Name',
            'order' => 'Order',
            'is_active' => 'Is Active',
            'default_days' => 'Default Days',
            'is_pro_rata' => 'Is Pro Rata',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[LeaveEntitlementDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveEntitlementDetails() {
        return $this->hasMany(LeaveEntitlementDetails::className(), ['leave_type_code' => 'leave_type_code']);
    }

    /**
     * Gets query for [[LeaveMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveMasters() {
        return $this->hasMany(LeaveMaster::className(), ['leave_type_code' => 'leave_type_code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefLeaveType::find()->where(['is_active' => 1])->orderBy(['leave_type_name' => SORT_ASC])->all(), "leave_type_code", "leave_type_name");
    }

    public static function getDropDownListMale() {
        return \yii\helpers\ArrayHelper::map(RefLeaveType::find()
                                ->where(['is_active' => 1])
                                ->andWhere(['!=', 'leave_type_code', self::codeMatern])
                                ->orderBy(['leave_type_name' => SORT_ASC])
                                ->all(),
                        "leave_type_code",
                        "leave_type_name"
                );
    }

    public static function getDropDownListMaleWithoutWorkTravel() {
        return \yii\helpers\ArrayHelper::map(RefLeaveType::find()
                                ->where(['is_active' => 1])
                                ->andWhere(['NOT IN', 'leave_type_code', [self::codeMatern, self::codeTravel]])
                                ->orderBy(['leave_type_name' => SORT_ASC])
                                ->all(),
                        "leave_type_code",
                        "leave_type_name"
                );
    }

    public static function getDropDownListFemale() {
        return \yii\helpers\ArrayHelper::map(RefLeaveType::find()
                                ->where(['is_active' => 1])
                                ->andWhere(['!=', 'leave_type_code', self::codePaternal])
                                ->orderBy(['leave_type_name' => SORT_ASC])
                                ->all(),
                        "leave_type_code",
                        "leave_type_name"
                );
    }

    public static function getDropDownListFemaleWithoutWorkTravel() {
        return \yii\helpers\ArrayHelper::map(RefLeaveType::find()
                                ->where(['is_active' => 1])
                                ->andWhere(['NOT IN', 'leave_type_code', [self::codePaternal, self::codeTravel]])
                                ->orderBy(['leave_type_name' => SORT_ASC])
                                ->all(),
                        "leave_type_code",
                        "leave_type_name"
                );
    }

    //Haziq 15/12/2022 Return active leave type code in an array
    public static function getActiveLeaveType() {
        return RefLeaveType::find()->where(["is_active" => 1])->orderBy("order")->asArray()->all();
    }

    public function getLeaveTypeWithEntitlement() {
        return RefLeaveType::find()->where(["is_active" => 1, 'leave_type_code' => [RefLeaveType::codeAnnual, RefLeaveType::codeSick]])->orderBy("order")->asArray()->all();
    }
}
