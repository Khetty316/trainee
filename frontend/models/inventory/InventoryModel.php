<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;

/**
 * This is the model class for table "inventory_model".
 *
 * @property int $id
 * @property string|null $type
 * @property string|null $group
 * @property string|null $description
 * @property string|null $unit_type
 * @property string|null $image
 * @property int|null $active_sts
 * @property int|null $inventory_brand_id
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $total_stock_on_hand
 * @property int|null $total_stock_reserved
 * @property int|null $total_stock_available
 * @property int|null $minimum_qty
 * @property int|null $stock_level_sts
 *
 * @property BomDetails[] $bomDetails
 * @property InventoryDetail[] $inventoryDetails
 * @property User $createdBy
 * @property User $updatedBy
 * @property InventoryBrand $inventoryBrand
 * @property InventoryOrderRequest[] $inventoryOrderRequests
 * @property InventoryReorderRequest[] $inventoryReorderRequests
 * @property StockOutboundDetails[] $stockOutboundDetails
 */
class InventoryModel extends \yii\db\ActiveRecord {

    const PREFIX_MODEL_CODE = 'MOD';
    const RUNNING_NO_LENGTH = 5;

    public $scannedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_model';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['active_sts', 'inventory_brand_id', 'created_by', 'updated_by', 'total_stock_on_hand', 'total_stock_reserved', 'total_stock_available', 'minimum_qty', 'stock_level_sts'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['type', 'group', 'description', 'image'], 'string', 'max' => 255],
            [['unit_type'], 'string', 'max' => 100],
//            [['type'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['inventory_brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryBrand::className(), 'targetAttribute' => ['inventory_brand_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'type' => 'Model Type',
            'group' => 'Group',
            'description' => 'Description',
            'unit_type' => 'Unit Type',
            'image' => 'Image',
            'active_sts' => 'Active',
            'inventory_brand_id' => 'Brand',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'total_stock_on_hand' => 'Stock On Hand',
            'total_stock_reserved' => 'Stock Reserved',
            'total_stock_available' => 'Stock Available',
            'minimum_qty' => 'Minimum Qty',
            'stock_level_sts' => 'Stock Level Sts',
        ];
    }

    /**
     * Gets query for [[BomDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBomDetails() {
        return $this->hasMany(BomDetails::className(), ['inventory_model_id' => 'id']);
    }

    /**
     * Gets query for [[InventoryDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryDetails() {
        return $this->hasMany(InventoryDetail::className(), ['model_id' => 'id']);
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
     * Gets query for [[InventoryBrand]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryBrand() {
        return $this->hasOne(InventoryBrand::className(), ['id' => 'inventory_brand_id']);
    }

    /**
     * Gets query for [[InventoryOrderRequests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryOrderRequests() {
        return $this->hasMany(InventoryOrderRequest::className(), ['inventory_model_id' => 'id']);
    }

    /**
     * Gets query for [[InventoryReorderRequests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReorderRequests() {
        return $this->hasMany(InventoryReorderRequest::className(), ['inventory_model_id' => 'id']);
    }

    /**
     * Gets query for [[StockOutboundDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStockOutboundDetails() {
        return $this->hasMany(StockOutboundDetails::className(), ['inventory_model_id' => 'id']);
    }

    public static function getBomDropdownlist() {
        return \yii\helpers\ArrayHelper::map(self::find()->where(['active_sts' => 2])->orderBy(['type' => SORT_ASC])->all(), "id", "type");
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

    public function getAllDropDownModelList() {
        return \yii\helpers\ArrayHelper::map(self::find()->where(['active_sts' => 2])->orderBy(['type' => SORT_ASC])->all(), "id", "type");
    }

    /**
     * NORMALIZE NAME and Brand
     * Normalizes a string by converting to uppercase and removing special characters
     */
    public static function normalizeName($text) {
        $text = strtoupper($text);
        $text = preg_replace('/[^A-Z0-9 ]/', '', $text);
        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * CHECK SIMILAR MODEL
     * Checks if a similar model exists in the database based on name and brand
     * @param string $name Model name
     * @param string $brand Brand name
     * @param int $threshold Similarity threshold (0-100)
     * @return array
     */
    public static function findSimilarModel($name, $brand, $threshold = 80) {
        // Normalize the input (combine name and brand)
        $normalizedInput = self::normalizeName($name . ' ' . $brand);

        // Get all models with their brand information
        $models = self::find()
                ->joinWith('inventoryBrand')
                ->all();

        foreach ($models as $model) {
            // Get brand name from relationship
            $dbBrandName = $model->inventoryBrand ? $model->inventoryBrand->name : '';

            // Normalize the database record (combine name and brand)
            $normalizedDb = self::normalizeName($model->type . ' ' . $dbBrandName);

            similar_text($normalizedInput, $normalizedDb, $percent);

            if ($percent >= $threshold) {
                return [
                    'match' => true,
                    'percent' => round($percent, 2),
                    'existing_name_model' => $model->type,
                    'existing_name_brand' => $dbBrandName,
                ];
            }
        }

        return ['match' => false];
    }

    /**
     * Prepare form data for create/update views
     */
    public function prepareFormData($model) {
        // Get combined model-brand list for dropdown
        $modelBrandList = $this->getModelBrandCombinations();

        // Check if this is a legacy record
        $isLegacy = $model->isLegacyRecord();

        if (!$isLegacy) {
            $model->model_type_input = $model->inventory_model_id;
            $model->brand_input = $model->inventory_brand_id;
        }
        
        return [
            'model' => $model,
            'modelBrandList' => $modelBrandList,
            'isLegacy' => $isLegacy,
        ];
    }

    /**
     * Get all model-brand combinations for the dropdown
     * @return array
     */
    public function getModelBrandCombinations() {
        $combinations = [];

        // Get all active inventory models with their brands
        $models = InventoryModel::find()
                ->with('inventoryBrand')
                ->where(['active_sts' => 2])
                ->orderBy(['type' => SORT_ASC])
                ->all();

        foreach ($models as $inventoryModel) {
            if ($inventoryModel->inventoryBrand) {
                $combinations[] = [
                    'model_id' => $inventoryModel->id,
                    'brand_id' => $inventoryModel->inventory_brand_id,
                    'model_name' => $inventoryModel->type,
                    'brand_name' => $inventoryModel->inventoryBrand->name,
                    'description' => $inventoryModel->description,
                ];
            }
        }

        return $combinations;
    }
}
