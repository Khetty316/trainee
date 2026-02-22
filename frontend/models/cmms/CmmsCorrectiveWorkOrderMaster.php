<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "cmms_corrective_work_order_master".
 *
 * @property int $id
 * @property int|null $machine_priority_id
 * @property int|null $active_sts
 * @property string|null $start_date
 * @property string $end_date
 * @property int|null $duration
 * @property string|null $remarks
 * @property int|null $cmms_fault_list_id
 * @property int|null $progress_status_id
 * @property int|null $assigned_by
 *
 * @property RefMachinePriority $machinePriority
 * @property CmmsFaultList $cmmsFaultList
 * @property RefProgressStatus $progressStatus
 * @property CmmsFaultList[] $cmmsFaultLists
 * @property RefAssignedPic[] $refAssignedPics
 */
class CmmsCorrectiveWorkOrderMaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_corrective_work_order_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['machine_priority_id', 'active_sts', 'duration', 'cmms_fault_list_id', 'progress_status_id', 'assigned_by'], 'integer'],
            [['start_date', 'end_date'], 'safe'],
            [
                'end_date',
                'compare',
                'compareAttribute' => 'start_date',
                'operator' => '>=',
                'type' => 'date',
                'message' => 'End date must be the same as or later than start date.',
            ],
            [['end_date'], 'required'],
            [['remarks'], 'string', 'max' => 255],
            [['machine_priority_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefMachinePriority::className(), 'targetAttribute' => ['machine_priority_id' => 'id']],
            [['cmms_fault_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsFaultList::className(), 'targetAttribute' => ['cmms_fault_list_id' => 'id']],
            [['progress_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefProgressStatus::className(), 'targetAttribute' => ['progress_status_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'machine_priority_id' => 'Machine Priority ID',
            'active_sts' => 'Active Sts',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'duration' => 'Duration',
            'remarks' => 'Remarks',
            'cmms_fault_list_id' => 'Cmms Fault List ID',
            'progress_status_id' => 'Progress Status ID',
            'assigned_by' => 'Assigned By',
        ];
    }

    /**
     * Gets query for [[MachinePriority]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMachinePriority()
    {
        return $this->hasOne(RefMachinePriority::className(), ['id' => 'machine_priority_id']);
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
     * Gets query for [[ProgressStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgressStatus()
    {
        return $this->hasOne(RefProgressStatus::className(), ['id' => 'progress_status_id']);
    }

    /**
     * Gets query for [[CmmsFaultLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsFaultLists()
    {
        return $this->hasMany(CmmsFaultList::className(), ['cmms_work_order_id' => 'id']);
    }

    /**
     * Gets query for [[RefAssignedPics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssignedPic()
    {
        return $this->hasMany(RefAssignedPic::className(), ['work_order_master_id' => 'id']);
    }
    
    public function getSelectedParts() 
    {
        $parts = [];
        foreach ($this->cmmsFaultLists as $fault) {
            if ($fault->partList) {
                $parts[] = $fault->partList->inventory->brand_model;
            }
        }
        
        return array_unique($parts);
    }
    
    public function getSelectedTools() 
    {
        $tools = [];
        foreach ($this->cmmsFaultLists as $fault) {
            if ($fault->toolList) {
                $tools[] = $fault->toolList->inventory->brand_model;
            }
        }
        
        return array_unique($tools);
    }
}