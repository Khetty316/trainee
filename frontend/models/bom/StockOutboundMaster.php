<?php

namespace frontend\models\bom;

use Yii;
use frontend\models\ProjectProduction\ProjectProductionPanels;

/**
 * This is the model class for table "stock_outbound_master".
 *
 * @property int $id
 * @property int $production_panel_id
 * @property int|null $bom_master_id
 * @property int|null $order
 * @property int $fully_dispatched_status
 * @property string|null $created_at
 * @property int|null $created_by
 *
 * @property StockOutboundDetails[] $stockOutboundDetails
 * @property ProjectProductionPanels $productionPanel
 * @property BomMaster $bomMaster
 */
class StockOutboundMaster extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'stock_outbound_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['production_panel_id'], 'required'],
            [['production_panel_id', 'bom_master_id', 'order', 'fully_dispatched_status', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['production_panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanels::className(), 'targetAttribute' => ['production_panel_id' => 'id']],
            [['bom_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => BomMaster::className(), 'targetAttribute' => ['bom_master_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'production_panel_id' => 'Production Panel ID',
            'bom_master_id' => 'Bom Master ID',
            'order' => 'Order',
            'fully_dispatched_status' => 'Fully Dispatched Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    public function getStockOutboundDetails() {
        return $this->hasMany(StockOutboundDetails::className(), ['stock_outbound_master_id' => 'id']);
    }

    public function getProductionPanel() {
        return $this->hasOne(ProjectProductionPanels::className(), ['id' => 'production_panel_id']);
    }

    public function getBomMaster() {
        return $this->hasOne(BomMaster::className(), ['id' => 'bom_master_id']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
//            $this->updated_at = new \yii\db\Expression('NOW()');
//            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

//    public function copyDetail($bomDetails) {
//        foreach ($bomDetails as $key => $bomDetail) {
//            $stockOutboundDetails = new StockOutboundDetails();
//            $stockOutboundDetails->stock_outbound_master_id = $this->id;
//            $stockOutboundDetails->bom_detail_id = $bomDetail->id;
//            $stockOutboundDetails->model_type = $bomDetail->model_type;
//            $stockOutboundDetails->brand = $bomDetail->brand;
//            $stockOutboundDetails->descriptions = $bomDetail->description;
//            $stockOutboundDetails->qty = $bomDetail->qty;
//            $stockOutboundDetails->engineer_remark = $bomDetail->remark;
//            $stockOutboundDetails->fully_dispatch_status = 0;
//            $stockOutboundDetails->qty_stock_available = 0;
//            $stockOutboundDetails->inventory_model_id = $bomDetail->inventory_model_id;
//            $stockOutboundDetails->inventory_brand_id = $bomDetail->inventory_brand_id;
//            $stockOutboundDetails->save();
//        }
//    }

    /**
     * Copy selected BOM details to stock outbound details
     * @param array $selectedItems Array of BomDetails models
     * @return bool True on success
     * @throws \Exception on failure
     */
    public function copyDetail($selectedItems) {
        // Validate input
        if (empty($selectedItems)) {
            throw new \Exception('No items provided to copy.');
        }

        if (!$this->id) {
            throw new \Exception('Stock Outbound Master must be saved before copying details.');
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $copiedCount = 0;

            foreach ($selectedItems as $key => $bomDetail) {
                // Validate bomDetail is an object
                if (!is_object($bomDetail)) {
                    throw new \Exception("Invalid item at index {$key}: not an object.");
                }

                // Check if this detail already exists to prevent duplicates
                $existingDetail = StockOutboundDetails::findOne([
                    'stock_outbound_master_id' => $this->id,
                    'bom_detail_id' => $bomDetail->id
                ]);

                if ($existingDetail) {
                    // Update existing detail instead of creating duplicate
                    $stockOutboundDetails = $existingDetail;
//                    Yii::info("Updating existing outbound detail ID {$existingDetail->id} for BOM detail {$bomDetail->id}");
                } else {
                    // Create new detail
                    $stockOutboundDetails = new StockOutboundDetails();
                    $stockOutboundDetails->stock_outbound_master_id = $this->id;
                    $stockOutboundDetails->bom_detail_id = $bomDetail->id;
                }

                // Set/update all fields
                $stockOutboundDetails->inventory_model_id = $bomDetail->inventory_model_id;
                $stockOutboundDetails->model_type = $bomDetail->model_type;
                $stockOutboundDetails->inventory_brand_id = $bomDetail->inventory_brand_id;
                $stockOutboundDetails->brand = $bomDetail->brand;
                $stockOutboundDetails->descriptions = $bomDetail->description;
                $stockOutboundDetails->qty = $bomDetail->qty;
                $stockOutboundDetails->engineer_remark = $bomDetail->remark;

                // Only set these if creating new record
                if ($stockOutboundDetails->isNewRecord) {
                    $stockOutboundDetails->fully_dispatch_status = 0;
                    $stockOutboundDetails->qty_stock_available = 0;
                }

                // Save the detail
                if (!$stockOutboundDetails->save(false)) {
                    throw new \Exception("Failed to save stock outbound detail for BOM detail {$bomDetail->id}");
                }

                $copiedCount++;
                Yii::info("Successfully copied BOM detail {$bomDetail->id} to stock outbound detail {$stockOutboundDetails->id}");
            }

            // Commit transaction
            $transaction->commit();

//            Yii::info("Successfully copied {$copiedCount} items to stock outbound master {$this->id}");
            return true;
        } catch (\Throwable $e) {
            // Rollback on any error
            $transaction->rollBack();

            Yii::error("Failed to copy details for stock outbound master {$this->id}: " . $e->getMessage());
            throw new \Exception("Failed to copy details: " . $e->getMessage());
        }
    }

    public function getStockDispatchStatus($productionPanelId) {
        $status = self::find()->where(['production_panel_id' => $productionPanelId, 'fully_dispatched_status' => 0])->exists();
        return $status;
    }

    public function getReceivers($productionPanelId) {
        return (new \yii\db\Query())
                        ->select(['u.id', 'u.fullname'])
                        ->distinct()
                        ->from('production_elec_tasks pet')
                        ->leftJoin('task_assign_elec tae', 'pet.id = tae.prod_elec_task_id')
                        ->leftJoin('task_assign_elec_staff tas', 'tae.id = tas.task_assign_elec_id')
                        ->leftJoin('user u', 'tas.user_id = u.id')
                        ->where(['pet.proj_prod_panel_id' => $productionPanelId, 'tae.active_sts' => 1])
                        ->andWhere(['IN', 'pet.elec_task_code', ['mount', 'wire']])
                        ->orderBy(['u.fullname' => SORT_ASC])
                        ->all();
    }
}
