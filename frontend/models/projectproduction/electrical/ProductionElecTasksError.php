<?php

namespace frontend\models\projectproduction\electrical;

use Yii;
use frontend\models\projectproduction\electrical\ProductionElecTasks;
use frontend\models\projectproduction\RefProjProdTaskErrors;

/**
 * This is the model class for table "production_elec_tasks_error".
 *
 * @property int $id
 * @property int $production_elec_task_id
 * @property int $error_code
 * @property string|null $remark
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property RefProjProdTaskErrors $errorCode
 * @property ProductionElecTasks $productionElecTask
 */
class ProductionElecTasksError extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'production_elec_tasks_error';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['remark', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['production_elec_task_id', 'error_code', 'remark'], 'required'],
            [['production_elec_task_id', 'error_code', 'created_by', 'updated_by'], 'integer'],
            [['remark'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['production_elec_task_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionElecTasks::class, 'targetAttribute' => ['production_elec_task_id' => 'id']],
            [['error_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjProdTaskErrors::class, 'targetAttribute' => ['error_code' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'production_elec_task_id' => 'Production Elec Task ID',
            'error_code' => 'Error Code',
            'remark' => 'Remark',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ErrorCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getErrorCode() {
        return $this->hasOne(RefProjProdTaskErrors::class, ['id' => 'error_code']);
    }

    /**
     * Gets query for [[ProductionElecTask]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionElecTask() {
        return $this->hasOne(ProductionElecTasks::class, ['id' => 'production_elec_task_id']);
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

    public function saveProductionElecTasksErrorStaff() {
        $taskAssign = TaskAssignElec::findOne([
            'prod_elec_task_id' => $this->production_elec_task_id,
            'deactivated_by' => null,
            'deactivated_at' => null
        ]);

        if (!$taskAssign) {
            return false; // No task assignment found
        }

        $taskAssignedStaffs = $taskAssign->taskAssignElecStaff;
        foreach ($taskAssignedStaffs as $staff) {
            $taskErrorStaff = new ProductionElecTasksErrorStaff();
            $taskErrorStaff->production_elec_tasks_error_id = $this->id;
            $taskErrorStaff->staff_id = $staff->user_id;
            $taskErrorStaff->is_read = 1; // default unread
            $taskErrorStaff->read_at = null;

            if (!$taskErrorStaff->save()) {
                  Yii::error('Failed to save ProductionElecTasksErrorStaff: ' . json_encode($taskErrorStaff->errors));
                return false;
            }
        }

        return true;
    }

    public function updateProductionElecTasksErrorStaff() {
        $taskErrorStaffs = ProductionElecTasksErrorStaff::findAll(['production_elec_tasks_error_id' => $this->id]);

        foreach ($taskErrorStaffs as $staff) {
            $staff->is_read = 1; // reset to unread
            $staff->read_at = null;

            if (!$staff->save()) {
                return false;
            }
        }

        return true;
    }
}
