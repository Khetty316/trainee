<?php

namespace frontend\models\report;

use Yii;

/**
 * This is the model class for table "v_quotation_masters_view".
 *
 * @property int $id
 * @property string|null $quotation_display_no
 * @property string|null $project_name
 * @property string|null $company_group_code
 * @property int|null $project_coordinator
 * @property string|null $project_coordinator_fullname
 * @property string|null $quotation_remark
 * @property int $type_id
 * @property string $q_type
 * @property string $q_type_name
 * @property string|null $type_remark
 * @property int $is_finalized
 * @property int|null $active_revision_id
 * @property int|null $active_client_id
 * @property string|null $client_name
 * @property int|null $proj_prod_id
 * @property float|null $active_revision_amount
 * @property string $created_at
 */
class VQuotationMastersView extends \yii\db\ActiveRecord {

    public $totalPanels, $totalAmount;

    public static function primaryKey() {
        return ["id"];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_quotation_masters_view';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'project_coordinator', 'type_id', 'is_finalized', 'active_revision_id', 'active_client_id', 'proj_prod_id'], 'integer'],
            [['quotation_remark', 'type_remark'], 'string'],
            [['q_type', 'q_type_name'], 'required'],
            [['active_revision_amount', 'totalPanels', 'totalAmount'], 'number'],
            [['created_at'], 'safe'],
            [['quotation_display_no', 'project_name', 'project_coordinator_fullname', 'q_type_name', 'client_name'], 'string', 'max' => 255],
            [['company_group_code', 'q_type'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'quotation_display_no' => 'Quotation Display No',
            'project_name' => 'Project Name',
            'company_group_code' => 'Company Group Code',
            'project_coordinator' => 'Project Coordinator',
            'project_coordinator_fullname' => 'Project Coordinator Fullname',
            'quotation_remark' => 'Quotation Remark',
            'type_id' => 'Type ID',
            'q_type' => 'Q Type',
            'q_type_name' => 'Q Type Name',
            'type_remark' => 'Type Remark',
            'is_finalized' => 'Is Finalized',
            'active_revision_id' => 'Active Revision ID',
            'active_client_id' => 'Active Client ID',
            'client_name' => 'Client Name',
            'proj_prod_id' => 'Proj Prod ID',
            'active_revision_amount' => 'Active Revision Amount',
            'created_at' => 'Created At',
        ];
    }

    public static function getDropDownListCoordinatorPeriod($dateFrom, $dateTo) {
        return \yii\helpers\ArrayHelper::map(VQuotationMastersView::find()->where(['between', 'created_at', $dateFrom, $dateTo])
                                ->groupBy('project_coordinator_fullname')
                                ->orderBy(['project_coordinator_fullname' => SORT_ASC])
                                ->all(), "id", "project_coordinator_fullname");
    }

}
