<?php

namespace frontend\models\projectquotation;

use Yii;
use frontend\models\projectquotation\ProjectQPanels;
use frontend\models\common\RefProjectQPanelUnit;

/**
 * This is the model class for table "project_q_panel_items".
 *
 * @property int $id
 * @property int $panel_id
 * @property string|null $item_description
 * @property float|null $cost
 * @property float|null $markup
 * @property float|null $amount
 * @property float|null $quantity
 * @property string|null $unit_code
 * @property int|null $product_id
 * @property int $sort
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property RefCurrencies $currencyCode
 * @property ProjectQPanels $panel
 * @property RefProjectQPanelUnit $unitCode
 */
class ProjectQPanelItems extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_q_panel_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['panel_id'], 'required'],
            [['panel_id', 'product_id', 'sort', 'created_by', 'updated_by'], 'integer'],
            [['cost', 'markup', 'amount', 'quantity'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['item_description'], 'string', 'max' => 255],
            [['unit_code'], 'string', 'max' => 10],
            [['panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectQPanels::class, 'targetAttribute' => ['panel_id' => 'id']],
            [['unit_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectQPanelUnit::class, 'targetAttribute' => ['unit_code' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'panel_id' => 'Panel ID',
            'item_description' => 'Item Description',
            'cost' => 'Cost',
            'markup' => 'Markup',
            'amount' => 'Amount',
            'quantity' => 'Quantity',
            'unit_code' => 'Unit Code',
            'product_id' => 'Product ID',
            'sort' => 'Sort',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Panel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPanel() {
        return $this->hasOne(ProjectQPanels::class, ['id' => 'panel_id']);
    }

    /**
     * Gets query for [[UnitCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnitCode() {
        return $this->hasOne(RefProjectQPanelUnit::class, ['code' => 'unit_code']);
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

    public function processNewItem($panelId, $itemDesc, $itemPrice, $itemQty, $itemUnit) {
        $this->panel_id = $panelId;
        $this->item_description = $itemDesc;
        $this->amount = $itemPrice;
        $this->quantity = $itemQty;
        $this->unit_code = $itemUnit;

        $max = ProjectQPanelItems::find()->where(['panel_id' => $this->panel_id])->max('sort');
        $this->sort = $max + 1;
        return $this->save();
    }

    public function updateSort($sorting) {
        $this->sort = $sorting;
        $this->update();
    }

    public function clonePanelItemFromMother($thisPanelId) {
        $newItem = new ProjectQPanelItems();
        $newItem->panel_id = $thisPanelId;
        $newItem->item_description = $this->item_description;
        $newItem->cost = $this->cost;
        $newItem->markup = $this->markup;
        $newItem->amount = $this->amount;
        $newItem->product_id = $this->product_id;
        $newItem->unit_code = $this->unit_code;
        $newItem->quantity = $this->quantity;
        $newItem->sort = $this->sort;
        return $newItem->save();
    }

    public function createFromTemplate($itemTemplate, $panelId) {
        $this->panel_id = $panelId;
        $this->item_description = $itemTemplate->item_description;
        $this->cost = $itemTemplate->cost;
        $this->amount = $itemTemplate->amount;
        $this->sort = $itemTemplate->sort;
        $this->markup = $itemTemplate->markup;
        $this->product_id = $itemTemplate->product_id;
        $this->quantity = $itemTemplate->quantity;
        $this->unit_code = $itemTemplate->unit_code;
        return $this->save();
    }

}
