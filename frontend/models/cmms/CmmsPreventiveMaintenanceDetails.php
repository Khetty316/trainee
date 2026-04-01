<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "cmms_preventive_maintenance_details".
 *
 * @property int $id
 * @property int|null $cmms_preventive_maintenance_id
 * @property int|null $active_sts
 * @property int|null $maintenance_category_id
 * @property string|null $remarks
 * @property int|null $cmms_fault_list_id
 * @property int|null $completion_status
 * @property int|null $section_head
 *
 * @property CmmsPmCategoryDesc[] $cmmsPmCategoryDescs
 * @property CmmsPreventiveWorkOrderMaster $cmmsPreventiveMaintenance
 * @property CmmsFaultList $cmmsFaultList
 * @property RefPmCategory $maintenanceCategory
 */
class CmmsPreventiveMaintenanceDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_preventive_maintenance_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cmms_preventive_maintenance_id', 'active_sts', 'maintenance_category_id', 'cmms_fault_list_id', 'completion_status', 'section_head'], 'integer'],
            [['remarks'], 'string', 'max' => 255],
            [['cmms_preventive_maintenance_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsPreventiveWorkOrderMaster::className(), 'targetAttribute' => ['cmms_preventive_maintenance_id' => 'id']],
            [['cmms_fault_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsFaultList::className(), 'targetAttribute' => ['cmms_fault_list_id' => 'id']],
            [['maintenance_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefPmCategory::className(), 'targetAttribute' => ['maintenance_category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cmms_preventive_maintenance_id' => 'Cmms Preventive Maintenance ID',
            'active_sts' => 'Active Sts',
            'maintenance_category_id' => 'Maintenance Category ID',
            'remarks' => 'Remarks',
            'cmms_fault_list_id' => 'Cmms Fault List ID',
            'completion_status' => 'Completion Status',
            'section_head' => 'Section Head',
        ];
    }

    /**
     * Gets query for [[CmmsPmCategoryDescs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsPmCategoryDescs()
    {
        return $this->hasMany(CmmsPmCategoryDesc::className(), ['cmms_pm_category_id' => 'id']);
    }

    /**
     * Gets query for [[CmmsPreventiveMaintenance]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsPreventiveMaintenance()
    {
        return $this->hasOne(CmmsPreventiveWorkOrderMaster::className(), ['id' => 'cmms_preventive_maintenance_id']);
    }

    /**
     * Gets query for [[CmmsFaultList]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsFaultList()
    {
        return $this->hasOne(CmmsFaultList::className(), ['id' => 'cmms_fault_list_id']);
    }

    /**
     * Gets query for [[MaintenanceCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaintenanceCategory()
    {
        return $this->hasOne(RefPmCategory::className(), ['id' => 'maintenance_category_id']);
    }
}
