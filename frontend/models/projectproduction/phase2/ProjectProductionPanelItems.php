<?php

namespace frontend\models\ProjectProduction;

use Yii;

/**
 * This is the model class for table "project_production_panel_items".
 *
 * @property int $id
 * @property int $proj_prod_panel_id
 * @property string|null $item_description
 * @property float $quantity
 * @property string|null $unit_code
 * @property float|null $stock_available
 * @property float|null $balance
 * @property float|null $cost
 * @property float|null $markup
 * @property float|null $amount
 * @property int|null $product_id
 * @property int $sort
 * @property string|null $remarks
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProjectProductionPanels $projProdPanel
 * @property RefProjectItemUnit $unitCode
 * @property ProjectProductionPanelProcDispatchItems[] $projectProductionPanelProcDispatchItems
 */
class ProjectProductionPanelItems extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_production_panel_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proj_prod_panel_id', 'item_description', 'quantity', 'unit_code'], 'required'],
            [['proj_prod_panel_id', 'product_id', 'sort', 'created_by', 'updated_by'], 'integer'],
            [['quantity', 'stock_available', 'balance', 'cost', 'markup', 'amount'], 'number'],
            [['remarks'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['item_description'], 'string', 'max' => 255],
            [['unit_code'], 'string', 'max' => 10],
            [['proj_prod_panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanels::className(), 'targetAttribute' => ['proj_prod_panel_id' => 'id']],
            [['unit_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectItemUnit::className(), 'targetAttribute' => ['unit_code' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'proj_prod_panel_id' => 'Proj Prod Panel ID',
            'item_description' => 'Item Description',
            'quantity' => 'Quantity',
            'unit_code' => 'Unit Code',
            'stock_available' => 'Stock Available',
            'balance' => 'Balance',
            'cost' => 'Cost',
            'markup' => 'Markup',
            'amount' => 'Amount',
            'product_id' => 'Product ID',
            'sort' => 'Sort',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ProjProdPanel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjProdPanel() {
        return $this->hasOne(ProjectProductionPanels::className(), ['id' => 'proj_prod_panel_id']);
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
     * Gets query for [[ProjectProductionPanelProcDispatchItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanelProcDispatchItems() {
        return $this->hasMany(ProjectProductionPanelProcDispatchItems::className(), ['proj_prod_panel_item_id' => 'id']);
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

    public function processAndSave() {
        $this->balance = $this->quantity;
        return $this->save();
    }

    public function adjustBalance($newBalance) {
        $this->balance = $newBalance;
        $this->update();
    }

}
