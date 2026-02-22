<?php

namespace frontend\models\bom;

use Yii;
use frontend\models\inventory\InventoryBrand;
use frontend\models\inventory\InventoryModel;

/**
 * This is the model class for table "bom_details".
 *
 * @property int $id
 * @property int $bom_master
 * @property int|null $inventory_model_id
 * @property int|null $inventory_brand_id
 * @property string $model_type
 * @property string|null $brand
 * @property string|null $description
 * @property float $qty
 * @property string|null $remark  
 * @property int|null $active_status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property int|null $is_finalized 1 = no, 2 = yes, 3 = outbound
 * @property int|null $inventory_sts 1 = pending approval, 2 = linked, 3 = rejected
 *
 * @property BomMaster $bomMaster
 * @property InventoryModel $inventoryModel
 * @property InventoryBrand $inventoryBrand
 * @property StockOutboundDetails[] $stockOutboundDetails
 */
class BomDetails extends \yii\db\ActiveRecord {

    // Virtual attributes for form input
    public $model_type_input;
    public $brand_input;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'bom_details';
    }

    /**
     * {@inheritdoc}
     */
//    public function rules() {
//        return [
//            [['bom_master', 'model_type', 'qty'], 'required'],
//            [['bom_master', 'inventory_model_id', 'inventory_brand_id', 'active_status', 'created_by'], 'integer'],
//            [['qty'], 'number'],
//            [['created_at'], 'safe'],
//            [['model_type', 'brand', 'description', 'remark'], 'string', 'max' => 1000],
//            [['bom_master'], 'exist', 'skipOnError' => true, 'targetClass' => BomMaster::className(), 'targetAttribute' => ['bom_master' => 'id']],
//            [['inventory_model_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryModel::className(), 'targetAttribute' => ['inventory_model_id' => 'id']],
//            [['inventory_brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryBrand::className(), 'targetAttribute' => ['inventory_brand_id' => 'id']],
//            // Custom validation: either use new system or legacy
//            ['model_type_input', 'required', 'when' => function ($model) {
//                    return empty($model->model_type) && empty($model->inventory_model_id);
//                }, 'message' => 'Model Type is required.'],
//            ['brand_input', 'required', 'when' => function ($model) {
//                    return empty($model->brand) && empty($model->inventory_brand_id);
//                }, 'message' => 'Brand is required.'],
//        ];
//    }

public function rules() {
        return [
            [['bom_master', 'qty'], 'required'],
            [['bom_master', 'inventory_model_id', 'inventory_brand_id', 'active_status', 'created_by', 'is_finalized', 'inventory_sts'], 'integer'],
            [['qty'], 'number', 'min' => 1],
            [['created_at'], 'safe'],
            [['model_type', 'brand', 'description', 'remark'], 'string', 'max' => 1000],
            [['model_type_input', 'brand_input'], 'integer'],
            
            // Foreign key constraints
            [['bom_master'], 'exist', 'skipOnError' => true, 'targetClass' => BomMaster::className(), 'targetAttribute' => ['bom_master' => 'id']],
            [['inventory_model_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryModel::className(), 'targetAttribute' => ['inventory_model_id' => 'id']],
            [['inventory_brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryBrand::className(), 'targetAttribute' => ['inventory_brand_id' => 'id']],
            
            // Validation: Either use dropdown system (model_type_input + brand_input) OR legacy (model_type + brand)
            ['model_type', 'required', 'when' => function ($model) {
                // Model type is required if not using dropdown system
                return empty($model->model_type_input);
            }, 'message' => 'Model Type is required.'],
            
            ['brand', 'required', 'when' => function ($model) {
                // Brand is required if not using dropdown system
                return empty($model->brand_input);
            }, 'message' => 'Brand is required.'],
            
            ['model_type_input', 'required', 'when' => function ($model) {
                // Required if using new system (not legacy)
                return empty($model->model_type);
            }, 'message' => 'Please select a Model Type from the dropdown.'],
            
            ['brand_input', 'required', 'when' => function ($model) {
                // Required if using new system (not legacy)
                return empty($model->brand);
            }, 'message' => 'Please select a Brand from the dropdown.'],
            
            // Custom validation
            ['model_type_input', 'validateModelInput', 'skipOnEmpty' => false],
            ['brand_input', 'validateBrandInput', 'skipOnEmpty' => false],
        ];
    }

    /**
     * Custom validation for model type input
     */
    public function validateModelInput($attribute, $params) {
        // If model_type_input is provided, it must be a valid dropdown selection
        if (!empty($this->model_type_input)) {
            $inventoryModel = InventoryModel::findOne($this->model_type_input);
            if (!$inventoryModel) {
                $this->addError($attribute, 'Selected Model Type is invalid.');
            } else {
                // Set the inventory_model_id for saving
                $this->inventory_model_id = $this->model_type_input;
                
                // Auto-fill model_type from inventory
                $this->model_type = $inventoryModel->type;
                
                $this->inventory_sts = 2;
                
                // Auto-fill description from inventory
                if (!empty($inventoryModel->description)) {
                    $this->description = $inventoryModel->description;
                }
            }
        } else if (!empty($this->model_type)) {
            // Legacy record - clear inventory model ID
            $this->inventory_model_id = null;
        }
    }

    /**
     * Custom validation for brand input
     */
    public function validateBrandInput($attribute, $params) {
        // If brand_input is provided, it must be a valid dropdown selection
        if (!empty($this->brand_input)) {
            $inventoryBrand = InventoryBrand::findOne($this->brand_input);
            if (!$inventoryBrand) {
                $this->addError($attribute, 'Selected Brand is invalid.');
            } else {
                // Set the inventory_brand_id for saving
                $this->inventory_brand_id = $this->brand_input;
                
                // Auto-fill brand from inventory
                $this->brand = $inventoryBrand->name;
            }
        } else if (!empty($this->brand)) {
            // Legacy record - clear inventory brand ID
            $this->inventory_brand_id = null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'bom_master' => 'Bom Master',
            'inventory_model_id' => 'Inventory Model ID',
            'inventory_brand_id' => 'Inventory Brand ID',
            'model_type' => 'Model Type',
            'brand' => 'Brand',
            'description' => 'Description',
            'qty' => 'Quantity',
            'remark' => 'Remark',
            'active_status' => 'Active Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'is_finalized' => 'Is Finalized',
            'inventory_sts' => 'Inventory Sts',
        ];
    }

    /**
     * Gets query for [[BomMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBomMaster() {
        return $this->hasOne(BomMaster::className(), ['id' => 'bom_master']);
    }

    /**
     * Gets query for [[InventoryModel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryModel() {
        return $this->hasOne(InventoryModel::className(), ['id' => 'inventory_model_id']);
    }

    /**
     * Gets query for [[InventoryBrand]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryBrand() {
        return $this->hasOne(InventoryBrand::className(), ['id' => 'inventory_brand_id']);
    }

    /**
     * Gets query for [[StockOutboundDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockOutboundDetails() {
        return $this->hasMany(StockOutboundDetails::className(), ['bom_detail_id' => 'id']);
    }

//    public function beforeSave($insert) {
//        if (!$this->isNewRecord) {
////            $this->updated_at = new \yii\db\Expression('NOW()');
////            $this->updated_by = Yii::$app->user->identity->id;
//        } else {
//            $this->created_by = Yii::$app->user->identity->id;
//            $this->created_at = new \yii\db\Expression('NOW()');
//        }
//        return parent::beforeSave($insert);
//    }

    /**
     * Get display value for model type (works for both old and new records)
     *
     * @return string
     */
    public function getModelDisplay() {
        if ($this->inventory_model_id && $this->inventoryModel) {
            return $this->inventoryModel->type;
        }
        return $this->model_type; // Legacy free text
    }

    /**
     * Get display value for brand (works for both old and new records)
     *
     * @return string
     */
    public function getBrandDisplay() {
        if ($this->inventory_brand_id && $this->inventoryBrand) {
            return $this->inventoryBrand->name;
        }
        return $this->brand; // Legacy free text
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            // The custom validators already set inventory_model_id and inventory_brand_id
            // Get text values from inventory if using dropdown
            if (!empty($this->inventory_model_id)) {
                $inventoryModel = InventoryModel::findOne($this->inventory_model_id);
                if ($inventoryModel) {
                    $this->model_type = $inventoryModel->type;
                }
            }

            if (!empty($this->inventory_brand_id)) {
                $inventoryBrand = InventoryBrand::findOne($this->inventory_brand_id);
                if ($inventoryBrand) {
                    $this->brand = $inventoryBrand->name;
                }
            }

            // Clear temporary input fields (they've already been processed)
            $this->model_type_input = null;
            $this->brand_input = null;

            // Set created_by and created_at for new records
            if ($insert) {
                if (empty($this->created_by)) {
                    $this->created_by = Yii::$app->user->id;
                }
                if (empty($this->created_at)) {
                    $this->created_at = new \yii\db\Expression('NOW()');
                }
            }

            return true;
        }
        return false;
    }

    public function afterFind() {
        parent::afterFind();

        // Populate virtual input fields for the form
        if ($this->inventory_model_id) {
            $this->model_type_input = $this->inventory_model_id;
        } else if ($this->model_type) {
            // For legacy or manual entries, set empty string for input field
            $this->model_type_input = '';
        }

        if ($this->inventory_brand_id) {
            $this->brand_input = $this->inventory_brand_id;
        } else if ($this->brand) {
            // For legacy or manual entries, set empty string for input field
            $this->brand_input = '';
        }
    }

// Helper method to check if record is legacy
    public function isLegacyRecord() {
        return (!empty($this->model_type) || !empty($this->brand)) &&
                empty($this->inventory_model_id) &&
                empty($this->inventory_brand_id);
    }
}
