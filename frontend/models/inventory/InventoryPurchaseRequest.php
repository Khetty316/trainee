<?php

namespace frontend\models\inventory;

use Yii;
use frontend\models\office\preReqForm\PrereqFormMaster;
use common\models\User;
use frontend\models\inventory\InventorySupplier;

/**
 * This is the model class for table "inventory_purchase_request".
 *
 * @property int $id
 * @property int|null $inventory_supplier_id
 * @property int|null $source_type 1 = new, 2 = reorder
 * @property int|null $source_id prf_master_id
 * @property string|null $quotation_no
 * @property string|null $quotation_date
 * @property string|null $quotation_filename
 * @property int|null $po_status 1 = no, 2 = got
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $uploaded_by
 * @property string|null $uploaded_at
 *
 * @property InventorySupplier $inventorySupplier
 * @property PrereqFormMaster $source
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $uploadedBy
 * @property InventoryPurchaseRequestItem[] $inventoryPurchaseRequestItems
 */
class InventoryPurchaseRequest extends \yii\db\ActiveRecord {

    public $quotation_file;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_purchase_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['inventory_supplier_id', 'source_type', 'source_id', 'po_status', 'created_by', 'updated_by', 'uploaded_by'], 'integer'],
            [['quotation_date', 'created_at', 'updated_at', 'uploaded_at'], 'safe'],
            [['quotation_no', 'quotation_filename'], 'string', 'max' => 255],
            [['inventory_supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventorySupplier::className(), 'targetAttribute' => ['inventory_supplier_id' => 'id']],
            [['source_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrereqFormMaster::className(), 'targetAttribute' => ['source_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['uploaded_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uploaded_by' => 'id']],
            [['quotation_file'], 'file',
                'skipOnEmpty' => true,
                'extensions' => 'pdf, doc, docx, xls, xlsx, jpg, jpeg, png',
                'maxSize' => 1024 * 1024 * 10, // 10MB
                'maxFiles' => 1,
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'inventory_supplier_id' => 'Inventory Supplier ID',
            'source_type' => 'Source Type',
            'source_id' => 'Source ID',
            'quotation_no' => 'Quotation No',
            'quotation_date' => 'Quotation Date',
            'quotation_filename' => 'Quotation Filename',
            'po_status' => 'Po Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'uploaded_by' => 'Uploaded By',
            'uploaded_at' => 'Uploaded At',
        ];
    }

    /**
     * Gets query for [[InventorySupplier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventorySupplier() {
        return $this->hasOne(InventorySupplier::className(), ['id' => 'inventory_supplier_id']);
    }

    /**
     * Gets query for [[Source]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSource() {
        return $this->hasOne(PrereqFormMaster::className(), ['id' => 'source_id']);
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
     * Gets query for [[UploadedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUploadedBy() {
        return $this->hasOne(User::className(), ['id' => 'uploaded_by']);
    }

    /**
     * Gets query for [[InventoryPurchaseRequestItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPurchaseRequestItems() {
        return $this->hasMany(InventoryPurchaseRequestItem::className(), ['inventory_pr_id' => 'id']);
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

    public function createPurchaseRequestItem($itemDetail, $sourceType) {
        $item = new InventoryPurchaseRequestItem();
        $item->inventory_pr_id = $this->id;
        $item->source_type = $sourceType;
        $item->source_id = $itemDetail->id;
        $item->department_code = $itemDetail->department_code;
        $item->brand_id = $itemDetail->brand_id;

        // Handle different field names based on source
        if ($sourceType == 1) {
            // From PrereqFormItem
            $item->model_type = $itemDetail->model_name;
            $item->model_group = $itemDetail->model_group;
            $item->model_description = $itemDetail->item_description;
            $item->quantity = $itemDetail->quantity;
            $item->unit_price = $itemDetail->unit_price_approved;
            $item->total_price = $itemDetail->total_price_approved;
            $item->unit_type = $itemDetail->model_unit_type;
            // Convert currency code to ID
            $currency = \frontend\models\common\RefCurrencies::findOne(['currency_code' => $itemDetail->currency]);
            $item->currency_id = $currency ? $currency->currency_id : null;
        } else {
            // From InventoryDetail (reorder)
            $item->model_type = $itemDetail->model ? $itemDetail->model->type : null;
            $item->model_group = $itemDetail->model ? $itemDetail->model->group : null;
            $item->model_description = $itemDetail->model ? $itemDetail->model->description : null;
            $item->quantity = $itemDetail->required_qty ?? $itemDetail->reorder_qty;
            $item->unit_price = $itemDetail->unit_price;
            $item->currency_id = $itemDetail->currency_id;
            $item->total_price = $item->unit_price * $item->quantity;
            $item->unit_type = $itemDetail->model ? $itemDetail->model->unit_type : null;
        }

        if (!$item->save()) {
            \Yii::error("Failed to save PR item (source_type={$sourceType}): " . json_encode($item->errors));
            return false;
        }

        return true;
    }

    //reorder
//    public function createPurchaseRequestReorderItem($itemDetail) {
//        $item = new InventoryPurchaseRequestItem();
//
//        $item->inventory_pr_id = $this->id;
//        $item->source_type = 1; // new
//        $item->source_id = $itemDetail->id;
//        $item->department_code = $itemDetail->department_code;
//        $item->brand_id = $itemDetail->brand_id;
//        $item->model_type = $itemDetail->model_name;// mo
//        $item->model_group = $itemDetail->model_group;// mo
//        $item->model_description = $itemDetail->item_description;// mo
//        $item->quantity = $itemDetail->quantity;// detail
//        $item->currency = $itemDetail->currency;
//        $item->unit_price = $itemDetail->unit_price_approved;
//        $item->total_price = $itemDetail->total_price_approved; // mo
//        
//        if (!$item->save()) {
//            return false;
//        }
//        return true;
//    }
}
