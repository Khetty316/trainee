<?php

namespace frontend\models\working\leavemgmt;

use Yii;

/**
 * This is the model class for table "leave_entitlement".
 *
 * @property int $id
 * @property int $user_id
 * @property int $year
 * @property float $annual_bring_forward_days
 * @property float $annual_bring_next_year_days
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property LeaveEntitlementDetails[] $leaveEntitlementDetails
 */
class LeaveEntitlement extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'leave_entitlement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'year'], 'required'],
            [['user_id', 'year', 'created_by', 'updated_by'], 'integer'],
            [['annual_bring_forward_days', 'annual_bring_next_year_days'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id', 'year'], 'unique', 'targetAttribute' => ['user_id', 'year']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'year' => 'Year',
            'annual_bring_forward_days' => 'Annual Bring Forward Days',
            'annual_bring_next_year_days' => 'Annual Bring Next Year Days',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[LeaveEntitlementDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveEntitlementDetails() {
        return $this->hasMany(LeaveEntitlementDetails::className(), ['leave_entitle_id' => 'id']);
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

    public function updateRecord($broughtForward, $annual, $sick, $updateBy) {
        if ($broughtForward != "") {
            $this->annual_bring_forward_days = $broughtForward;
        }
//        if ($annual != "") {
//            $this->annual_entitled_del = $annual;
//        }
//        if ($sick != "") {
//            $this->sick_entitled_del = $sick;
//        }
        $this->updated_by = $updateBy;
        $this->updated_at = new Expression('NOW()');
        return $this->update();
    }

    public function newRecord($userId, $year, $broughtForward, $annual, $sick, $createdBy) {//($userId, $year, $brought, $annual, $sick, Yii::$app->user->id){
        $this->user_id = $userId;
        $this->year = $year;
        $this->annual_bring_forward_days = empty($broughtForward) ? 0 : $broughtForward;
//        $this->annual_entitled_del = empty($annual) ? 0 : $annual;
//        $this->sick_entitled_del = empty($sick) ? 0 : $sick;
        $this->created_by = $createdBy;
        $this->created_at = new Expression('NOW()');
        return $this->save();
    }

//
//    public static function checkIfHasEntitlementRecord() {
//        LeaveEntitlement::find();
//    }

    public function processAndSave($post, $userId, $selectYear) {
        $this->user_id = $userId;
        $this->year = $selectYear;
        $this->annual_bring_forward_days = $post["4"];
        $this->created_at = new \yii\db\Expression('NOW()');
        $this->created_by = Yii::$app->user->identity->id;
        return $this->save();
    }

}
