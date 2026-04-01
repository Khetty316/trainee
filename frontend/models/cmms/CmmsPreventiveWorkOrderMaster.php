<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "cmms_preventive_work_order_master".
 *
 * @property int $id
 * @property string|null $next_date
 * @property string|null $start_time
 * @property string|null $end_time
 * @property int|null $active_sts
 * @property int|null $duration
 * @property string|null $remarks
 * @property int|null $progress_status_id
 * @property int|null $assigned_by
 * @property int|null $frequency_id
 * @property string|null $created_at
 * @property int|null $cmms_asset_list_id
 * @property int|null $part_list_id
 * @property int|null $tool_list_id
 *
 * @property CmmsFaultList[] $cmmsFaultLists
 * @property CmmsPreventiveMaintenanceDetails[] $cmmsPreventiveMaintenanceDetails
 * @property RefProgressStatus $progressStatus
 * @property RefFrequency $frequency
 * @property CmmsAssetList $cmmsAssetList
 * @property CmmsPartList $partList
 * @property CmmsToolList $toolList
 * @property RefAssignedPic[] $refAssignedPics
 */
class CmmsPreventiveWorkOrderMaster extends \yii\db\ActiveRecord
{
    public $part_name;
    public $tool_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_preventive_work_order_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['commencement_date', 'next_date', 'start_time', 'end_time', 'created_at', 'part_name', 'tool_name'], 'safe'],
            [['active_sts', 'progress_status_id', 'assigned_by', 'frequency_id', 'cmms_asset_list_id', 'part_list_id', 'tool_list_id'], 'integer'],
            [['remarks', 'duration'], 'string', 'max' => 255],
            [['progress_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefProgressStatus::className(), 'targetAttribute' => ['progress_status_id' => 'id']],
            [['frequency_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefFrequency::className(), 'targetAttribute' => ['frequency_id' => 'id']],
            [['cmms_asset_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsAssetList::className(), 'targetAttribute' => ['cmms_asset_list_id' => 'id']],
            [['part_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsPartList::className(), 'targetAttribute' => ['part_list_id' => 'id']],
            [['tool_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsToolList::className(), 'targetAttribute' => ['tool_list_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'next_date' => 'Next Date',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'active_sts' => 'Active Sts',
            'duration' => 'Duration',
            'remarks' => 'Remarks',
            'progress_status_id' => 'Progress Status ID',
            'assigned_by' => 'Assigned By',
            'frequency_id' => 'Frequency ID',
            'created_at' => 'Created At',
            'cmms_asset_list_id' => 'Cmms Asset List ID',
            'part_list_id' => 'Part List ID',
            'tool_list_id' => 'Tool List ID',
            'commencement_date' => 'Commencement Date'
        ];
    }

    /**
     * Gets query for [[CmmsFaultLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsFaultLists()
    {
        return $this->hasMany(CmmsFaultList::className(), ['cmms_preventive_work_order_id' => 'id']);
    }

    /**
     * Gets query for [[CmmsPreventiveMaintenanceDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsPreventiveMaintenanceDetails()
    {
        return $this->hasMany(CmmsPreventiveMaintenanceDetails::className(), ['cmms_preventive_maintenance_id' => 'id']);
    }

    /**
     * Gets query for [[ProgressStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgressStatus()
    {
        return $this->hasOne(RefProgressStatus::className(), ['id' => 'progress_status_id']);
    }

    /**
     * Gets query for [[Frequency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFrequency()
    {
        return $this->hasOne(RefFrequency::className(), ['id' => 'frequency_id']);
    }

    /**
     * Gets query for [[CmmsAssetList]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsAssetList()
    {
        return $this->hasOne(CmmsAssetList::className(), ['id' => 'cmms_asset_list_id']);
    }

    /**
     * Gets query for [[PartList]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartList()
    {
        return $this->hasOne(CmmsPartList::className(), ['id' => 'part_list_id']);
    }

    /**
     * Gets query for [[ToolList]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToolList()
    {
        return $this->hasOne(CmmsToolList::className(), ['id' => 'tool_list_id']);
    }

    /**
     * Gets query for [[RefAssignedPics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssignedPic()
    {
        return $this->hasMany(RefAssignedPic::className(), ['preventive_work_order_master_id' => 'id']);
    }
    
    public function getCmmsAssetCodes_by_Id()
    {
        return \yii\helpers\ArrayHelper::map(CmmsAssetList::findAll(["active_sts" => "1"]), "id", "asset_id");
    }
}
