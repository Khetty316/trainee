<?php

namespace frontend\models\ProjectProduction;

use Yii;

/**
 * This is the model class for table "project_production_panel_fab_bq_items".
 *
 * @property int $id
 * @property int $fab_bq_master_id
 * @property string $item_description
 * @property float $quantity
 * @property float $balance
 * @property string $unit_code
 * @property int $sort
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProjectProductionPanelFabBqMaster $fabBqMaster
 * @property RefProjectItemUnit $unitCode
 */
class ProjectProductionPanelFabBqItems extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_production_panel_fab_bq_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['fab_bq_master_id', 'item_description', 'quantity', 'balance', 'unit_code'], 'required'],
            [['fab_bq_master_id', 'sort', 'created_by', 'updated_by'], 'integer'],
            [['quantity', 'balance'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['item_description'], 'string', 'max' => 255],
            [['unit_code'], 'string', 'max' => 10],
            [['fab_bq_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanelFabBqMaster::className(), 'targetAttribute' => ['fab_bq_master_id' => 'id']],
            [['unit_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectItemUnit::className(), 'targetAttribute' => ['unit_code' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'fab_bq_master_id' => 'BQ master',
            'item_description' => 'Item Description',
            'quantity' => 'Quantity',
            'balance' => 'Balance',
            'unit_code' => 'Unit',
            'sort' => 'Sort',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[FabBqMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFabBqMaster() {
        return $this->hasOne(ProjectProductionPanelFabBqMaster::className(), ['id' => 'fab_bq_master_id']);
    }

    /**
     * Gets query for [[UnitCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnitCode() {
        return $this->hasOne(RefProjectItemUnit::className(), ['code' => 'unit_code']);
    }

    public function adjustBalance($newBalance) {
        $this->balance = $newBalance;
        $this->update();
    }

}
