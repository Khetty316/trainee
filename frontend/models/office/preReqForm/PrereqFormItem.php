<?php

namespace frontend\models\office\preReqForm;

use Yii;
use common\models\User;

/**
 * This is the model class for table "prereq_form_item".
 *
 * @property int $id
 * @property int $prereq_form_master_id
 * @property string|null $department_code
 * @property int|null $supplier_id
 * @property string|null $supplier_name
 * @property int|null $brand_id
 * @property string|null $brand_name
 * @property string|null $model_name
 * @property string|null $model_group
 * @property string|null $item_description
 * @property int $quantity
 * @property string|null $currency
 * @property float $unit_price
 * @property float $total_price
 * @property string|null $purpose_or_function
 * @property string|null $remark
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $is_deleted 0 = no, 1 = yes
 * @property int|null $status 0 = approved, 1 = rejected
 * @property int|null $quantity_approved
 * @property string|null $currency_approved
 * @property float|null $unit_price_approved
 * @property float|null $total_price_approved
 *
 * @property InventoryPurchaseRequestItem[] $inventoryPurchaseRequestItems
 * @property InventoryReorderItem[] $inventoryReorderItems
 * @property PrereqFormMaster $prereqFormMaster
 * @property User $createdBy
 * @property User $updatedBy
 * @property PrereqFormItemWorklist[] $prereqFormItemWorklists
 */
class PrereqFormItem extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'prereq_form_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['prereq_form_master_id', 'quantity', 'unit_price', 'total_price'], 'required'],
            [['prereq_form_master_id', 'supplier_id', 'brand_id', 'quantity', 'created_by', 'updated_by', 'is_deleted', 'status', 'quantity_approved'], 'integer'],
            [['item_description', 'purpose_or_function', 'remark'], 'string'],
            [['unit_price', 'total_price', 'unit_price_approved', 'total_price_approved'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['department_code'], 'string', 'max' => 50],
            [['supplier_name', 'brand_name', 'model_name', 'model_group', 'currency', 'currency_approved'], 'string', 'max' => 255],
            [['prereq_form_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrereqFormMaster::className(), 'targetAttribute' => ['prereq_form_master_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'prereq_form_master_id' => 'Prereq Form Master ID',
            'department_code' => 'Department Code',
            'supplier_id' => 'Supplier ID',
            'supplier_name' => 'Supplier Name',
            'brand_id' => 'Brand ID',
            'brand_name' => 'Brand Name',
            'model_name' => 'Model Name',
            'model_group' => 'Model Group',
            'item_description' => 'Item Description',
            'quantity' => 'Quantity',
            'currency' => 'Currency',
            'unit_price' => 'Unit Price',
            'total_price' => 'Total Price',
            'purpose_or_function' => 'Purpose Or Function',
            'remark' => 'Remark',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
            'status' => 'Status',
            'quantity_approved' => 'Quantity Approved',
            'currency_approved' => 'Currency Approved',
            'unit_price_approved' => 'Unit Price Approved',
            'total_price_approved' => 'Total Price Approved',
        ];
    }

    /**
     * Gets query for [[InventoryPurchaseRequestItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPurchaseRequestItems() {
        return $this->hasMany(InventoryPurchaseRequestItem::className(), ['source_id' => 'id']);
    }

    /**
     * Gets query for [[InventoryReorderItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReorderItems() {
        return $this->hasMany(InventoryReorderItem::className(), ['prereq_form_item_id' => 'id']);
    }

    /**
     * Gets query for [[PrereqFormMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrereqFormMaster() {
        return $this->hasOne(PrereqFormMaster::className(), ['id' => 'prereq_form_master_id']);
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
     * Gets query for [[PrereqFormItemWorklists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrereqFormItemWorklists() {
        return $this->hasMany(PrereqFormItemWorklist::className(), ['prereq_form_item_id' => 'id']);
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

    public static function checkInventoryDuplicate($department, $supplierId, $brandId, $modelName) {

        // Normalize model name and find model ID
        $existingModel = \frontend\models\inventory\InventoryModel::find()
                ->where(['LOWER(type)' => mb_strtolower(trim($modelName))])
                ->andWhere(['active_sts' => 2])
                ->one();

        if (!$existingModel) {
            return false;
        }

        // Check if inventory detail exists
        $query = \frontend\models\inventory\InventoryDetail::find()
                ->where([
                    'department_code' => $department,
                    'supplier_id' => $supplierId,
                    'brand_id' => $brandId,
                    'model_id' => $existingModel->id,
                    'active_sts' => 2,
        ]);

        return $query->exists();
    }

    public function syncSourceModule() {
        if (!$this->reference_type || !$this->reference_id) {
            return;
        }

        switch ($this->reference_type) {
            case 'bom_detail':
                $this->syncBomDetail();
                break;
            // add other modules here (CMMS, Work Orders)
        }
    }

    private function syncBomDetail() {
        $bom = \frontend\models\bom\BomDetails::findOne($this->reference_id);
        if (!$bom)
            throw new \Exception('BOM detail not found');

        if ($this->status == 0) {
            $bom->inventory_sts = 4; // approved but pending requestor confirmation
        } else {
            $bom->inventory_sts = 3; // rejected
        }

        if (!$bom->save(false)) {
            throw new \Exception('Failed to update BOM status');
        }
    }

//    private static function syncCmmsWorkOrder(PrereqFormItem $item): void {
//        $workOrder = CmmsWorkOrder::findOne($item->reference_id);
//
//        if (!$workOrder) {
//            throw new \Exception('Work order not found');
//        }
//
//        if ($item->status == 0) {
//            $workOrder->material_status = CmmsStatus::MATERIAL_APPROVED;
//        } else {
//            $workOrder->material_status = CmmsStatus::MATERIAL_REJECTED;
//        }
//
//        $workOrder->save(false);
//    }
}
