<?php

namespace frontend\models\ProjectProduction;

use Yii;
use frontend\models\ProjectProduction\RefProjectItemUnit;

/**
 * This is the model class for table "project_production_panel_proc_dispatch_items".
 *
 * @property int $id
 * @property int $proc_dispatch_master_id
 * @property int|null $proj_prod_panel_item_id
 * @property string|null $item_description
 * @property float|null $quantity
 * @property string|null $unit_code
 * @property int $sort
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProjectProductionPanelProcDispatchMaster $procDispatchMaster
 * @property ProjectProductionPanelItems $projProdPanelItem
 * @property RefProjectItemUnit $unitCode
 */
class ProjectProductionPanelProcDispatchItems extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_production_panel_proc_dispatch_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proc_dispatch_master_id'], 'required'],
            [['proc_dispatch_master_id', 'proj_prod_panel_item_id', 'sort', 'created_by', 'updated_by'], 'integer'],
            [['quantity'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['item_description', 'unit_code'], 'string', 'max' => 255],
            [['proc_dispatch_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanelProcDispatchMaster::className(), 'targetAttribute' => ['proc_dispatch_master_id' => 'id']],
            [['proj_prod_panel_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanelItems::className(), 'targetAttribute' => ['proj_prod_panel_item_id' => 'id']],
            [['unit_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectItemUnit::className(), 'targetAttribute' => ['unit_code' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'proc_dispatch_master_id' => 'Proc Dispatch Master ID',
            'proj_prod_panel_item_id' => 'Proj Prod Panel Item ID',
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

    /**
     * Gets query for [[ProcDispatchMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProcDispatchMaster() {
        return $this->hasOne(ProjectProductionPanelProcDispatchMaster::className(), ['id' => 'proc_dispatch_master_id']);
    }

    /**
     * Gets query for [[ProjProdPanelItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjProdPanelItem() {
        return $this->hasOne(ProjectProductionPanelItems::className(), ['id' => 'proj_prod_panel_item_id']);
    }

    /**
     * Gets query for [[UnitCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnitCode() {
        return $this->hasOne(RefProjectItemUnit::className(), ['code' => 'unit_code']);
    }

    public function processDispatchItem($dispatchMaster, $item, $dispatchQty) {
        $this->quantity = ($dispatchQty > $item->balance) ? $item->balance : $dispatchQty;
        if ($this->quantity <= 0) {
            return false;
        }
        $this->proc_dispatch_master_id = $dispatchMaster->id;
        $this->proj_prod_panel_item_id = $item->id;
        $this->item_description = $item->item_description;
        $this->unit_code = $item->unit_code;
        $item->adjustBalance($item->balance - $this->quantity);
        return $this->save();
    }

    public function restoreBalance() {
        $panelItem = $this->projProdPanelItem;
        $panelItem->balance += $this->quantity;
        if ($panelItem->update()) {
            return true;
        } else {
            \common\models\myTools\Mydebug::dumpFileA($panelItem->errors);
            return false;
        }
    }

}
