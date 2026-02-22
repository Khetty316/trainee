<?php

namespace frontend\models\office\employeeHandbook;

use Yii;
use common\models\User;
use common\models\myTools\MyFormatter;
use frontend\models\office\employeeHandbook\RefEmployeeHandbookContent;

/**
 * This is the model class for table "employee_handbook_master".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $edition_no
 * @property string|null $edition_date
 * @property int|null $is_active 0 = yes, 1 = no
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property EhOutpatientMedDetail[] $ehOutpatientMedDetails
 * @property EhOutpatientMedMaster[] $ehOutpatientMedMasters
 * @property EhTravelAllowanceDetail[] $ehTravelAllowanceDetails
 * @property EhTravelAllowanceDetail[] $ehTravelAllowanceDetails0
 * @property EhTravelAllowanceMaster[] $ehTravelAllowanceMasters
 * @property User $createdBy
 * @property User $updatedBy
 */
class EmployeeHandbookMaster extends \yii\db\ActiveRecord {

//    const SUPERUSER_USER_MANUAL_FILENAME = "T4B6a-Digital Employee Handbook Module-01.pdf";
    const SUPERUSER_USER_MANUAL_FILENAME = "T4B6a-Employee Handbook Module-00.pdf";
    CONST IS_ACTIVE = [0 => 'No', 1 => 'Yes'];
    CONST IS_ACTIVE_HTML = [
        0 => '<span class="text-danger">No</span>',
        1 => '<span class="text-success">Yes</span>',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'employee_handbook_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['edition_no', 'is_active', 'created_by', 'updated_by'], 'integer'],
            [['edition_date', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['edition_no', 'is_active', 'name', 'edition_date'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Title',
            'edition_no' => 'Edition No',
            'edition_date' => 'Edition Date',
            'is_active' => 'Is Active?',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[EhOutpatientMedDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEhOutpatientMedDetails() {
        return $this->hasMany(EhOutpatientMedDetail::className(), ['eh_master_id' => 'id']);
    }

    /**
     * Gets query for [[EhOutpatientMedMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEhOutpatientMedMasters() {
        return $this->hasMany(EhOutpatientMedMaster::className(), ['eh_master_id' => 'id']);
    }

    /**
     * Gets query for [[EhTravelAllowanceDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEhTravelAllowanceDetails() {
        return $this->hasMany(EhTravelAllowanceDetail::className(), ['eh_travel_allowance_master_id' => 'id']);
    }

    /**
     * Gets query for [[EhTravelAllowanceDetails0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEhTravelAllowanceDetails0() {
        return $this->hasMany(EhTravelAllowanceDetail::className(), ['eh_master_id' => 'id']);
    }

    /**
     * Gets query for [[EhTravelAllowanceMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEhTravelAllowanceMasters() {
        return $this->hasMany(EhTravelAllowanceMaster::className(), ['eh_master_id' => 'id']);
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

    private function getContentTypeRoutes($id, $superUser) {
        return [
            RefEmployeeHandbookContent::TRAVEL_ALLOWANCE_CODE => 'office/eh-travel-allowance/view?ehId=' . $id . '&superUser=' . $superUser,
            RefEmployeeHandbookContent::OUTPATIENT_MEDICAL_CODE => 'office/eh-outpatient-med-benefit/view?ehId=' . $id . '&superUser=' . $superUser,
            RefEmployeeHandbookContent::EXEC_OT_MEAL_CODE => 'office/eh-exec-ot-meal/view?ehId=' . $id . '&superUser=' . $superUser,
                // Add more mappings here
        ];
    }

    public function getRedirectAction($id, $contentTypeCode, $superUser) {
        $routes = $this->getContentTypeRoutes($id, $superUser);

        return isset($routes[$contentTypeCode]) ? $routes[$contentTypeCode] : null;
    }
}
