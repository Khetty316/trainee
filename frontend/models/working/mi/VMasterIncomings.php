<?php

namespace frontend\models\working\mi;

use Yii;

/**
 * This is the model class for table "v_master_incomings".
 *
 * @property int $id
 * @property string|null $index_no
 * @property int $uploader_id
 * @property string|null $uploader_username
 * @property string|null $uploader_fullname
 * @property int $doc_type_id
 * @property string|null $doc_type_name
 * @property int|null $sub_doc_type_id
 * @property string|null $sub_doc_type_name
 * @property string|null $doc_due_date
 * @property string|null $grn_no
 * @property string|null $reference_no
 * @property string|null $particular
 * @property float|null $amount
 * @property int|null $po_id
 * @property string|null $po_number
 * @property int $isUrgent
 * @property int $isPerforma
 * @property int $file_type_id
 * @property string|null $file_type_name
 * @property string $received_from
 * @property string|null $remarks
 * @property string|null $filename
 * @property string $project_code
 * @property string|null $project_name
 * @property string|null $project_description
 * @property int $requestor_id
 * @property string|null $requestor_username
 * @property string|null $requestor_fullname
 * @property int|null $current_step
 * @property int|null $current_step_task_id
 * @property string|null $task_name
 * @property string|null $task_description
 * @property int $mi_status
 * @property string|null $status
 * @property string $created_at
 * @property string|null $updated_at
 */
class VMasterIncomings extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_master_incomings';
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey() {
        return ["id"];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'uploader_id', 'doc_type_id', 'sub_doc_type_id', 'po_id', 'isUrgent', 'isPerforma', 'file_type_id', 'requestor_id', 'current_step', 'current_step_task_id', 'mi_status'], 'integer'],
            [['uploader_id', 'doc_type_id', 'file_type_id', 'received_from', 'project_code', 'requestor_id'], 'required'],
            [['doc_due_date', 'created_at', 'updated_at'], 'safe'],
            [['amount'], 'number'],
            [['remarks'], 'string'],
            [['index_no', 'project_code'], 'string', 'max' => 20],
            [['uploader_username', 'uploader_fullname', 'doc_type_name', 'sub_doc_type_name', 'grn_no', 'reference_no', 'particular', 'file_type_name', 'received_from', 'filename', 'project_name', 'project_description', 'requestor_username', 'requestor_fullname', 'task_name', 'task_description', 'status'], 'string', 'max' => 255],
            [['po_number'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'index_no' => 'Index No',
            'uploader_id' => 'Uploader ID',
            'uploader_username' => 'Uploader Username',
            'uploader_fullname' => 'Uploader Fullname',
            'doc_type_id' => 'Doc Type ID',
            'doc_type_name' => 'Doc Type',
            'sub_doc_type_id' => 'Sub Doc Type ID',
            'sub_doc_type_name' => 'Sub Doc Type',
            'doc_due_date' => 'Doc. Date',
            'grn_no' => 'Grn No',
            'reference_no' => 'Reference No',
            'particular' => 'Particular',
            'amount' => 'Amount',
            'po_id' => 'Po ID',
            'po_number' => 'Po Number',
            'isUrgent' => 'Is Urgent',
            'isPerforma' => 'Is Performa',
            'file_type_id' => 'File Type ID',
            'file_type_name' => 'File Type Name',
            'received_from' => 'Received From',
            'remarks' => 'Remarks',
            'filename' => 'Filename',
            'project_code' => 'Project Code',
            'project_name' => 'Project Name',
            'project_description' => 'Project Description',
            'requestor_id' => 'Requestor ID',
            'requestor_username' => 'Requestor Username',
            'requestor_fullname' => 'Requestor Fullname',
            'current_step' => 'Current Step',
            'current_step_task_id' => 'Current Step Task ID',
            'task_name' => 'Task Name',
            'task_description' => 'Task Desc',
            'mi_status' => 'Mi Status',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getTotalAmountCurrency() {
        
        $amt = MiProjects::find()->where('mi_id='.$this->id)->sum('amount');
        
        
        return $amt;
    }

}
