<?php

namespace frontend\models\ProjectProduction;

use Yii;

/**
 * This is the model class for table "project_production_panel_store_dispatch_items".
 *
 * @property int $id
 * @property int $store_dispatch_master_id
 * @property int $fab_bq_item_id
 * @property string|null $item_description
 * @property float|null $quantity
 * @property string|null $unit_code
 * @property int $sort
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProjectProductionPanelStoreDispatchMaster $storeDispatchMaster
 * @property RefProjectItemUnit $unitCode
 * @property ProjectProductionPanelFabBqItems $fabBqItem
 */
class ProjectProductionPanelStoreDispatchItems extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_production_panel_store_dispatch_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['store_dispatch_master_id', 'fab_bq_item_id'], 'required'],
            [['store_dispatch_master_id', 'fab_bq_item_id', 'sort', 'created_by', 'updated_by'], 'integer'],
            [['quantity'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['item_description'], 'string', 'max' => 255],
            [['unit_code'], 'string', 'max' => 10],
            [['store_dispatch_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanelStoreDispatchMaster::className(), 'targetAttribute' => ['store_dispatch_master_id' => 'id']],
            [['unit_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectItemUnit::className(), 'targetAttribute' => ['unit_code' => 'code']],
            [['fab_bq_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanelFabBqItems::className(), 'targetAttribute' => ['fab_bq_item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'store_dispatch_master_id' => 'Store Dispatch Master ID',
            'fab_bq_item_id' => 'Fab Bq Item ID',
            'item_description' => 'Item Description',
            'quantity' => 'Quantity',
            'unit_code' => 'Unit Code',
            'sort' => 'Sort',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
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

    /**
     * Gets query for [[StoreDispatchMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStoreDispatchMaster() {
        return $this->hasOne(ProjectProductionPanelStoreDispatchMaster::className(), ['id' => 'store_dispatch_master_id']);
    }

    /**
     * Gets query for [[UnitCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnitCode() {
        return $this->hasOne(RefProjectItemUnit::className(), ['code' => 'unit_code']);
    }

    /**
     * Gets query for [[FabBqItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFabBqItem() {
        return $this->hasOne(ProjectProductionPanelFabBqItems::className(), ['id' => 'fab_bq_item_id']);
    }

    public function processDispatchItem($dispatchMaster, $bqItem, $dispatchQty) {
        $this->quantity = $dispatchQty > $bqItem->balance ? $bqItem->balance : $dispatchQty;
        if ($this->quantity <= 0) {
            return false;
        }
        $this->store_dispatch_master_id = $dispatchMaster->id;
        $this->fab_bq_item_id = $bqItem->id;
        $this->item_description = $bqItem->item_description;
        $this->unit_code = $bqItem->unit_code;
        $bqItem->adjustBalance($bqItem->balance - $this->quantity);
        return $this->save();
    }

    public function restoreBalance() {
        $bqItem = $this->fabBqItem;
        $bqItem->balance += $this->quantity;
        return $bqItem->update();
    }

}
