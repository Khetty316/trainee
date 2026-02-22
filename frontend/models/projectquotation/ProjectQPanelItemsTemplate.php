<?php

namespace frontend\models\projectquotation;

use Yii;
use frontend\models\common\RefProjectQPanelUnit;

/**
 * This is the model class for table "project_q_panel_items_template".
 *
 * @property int $id
 * @property int $panel_template_id
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
 * @property ProjectQPanelsTemplate $panelTemplate
 * @property RefProjectQPanelUnit $unitCode
 */
class ProjectQPanelItemsTemplate extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_q_panel_items_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['panel_template_id'], 'required'],
            [['panel_template_id', 'product_id', 'sort', 'created_by', 'updated_by'], 'integer'],
            [['cost', 'markup', 'amount', 'quantity'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['item_description'], 'string', 'max' => 255],
            [['unit_code'], 'string', 'max' => 10],
            [['panel_template_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectQPanelsTemplate::class, 'targetAttribute' => ['panel_template_id' => 'id']],
            [['unit_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectQPanelUnit::class, 'targetAttribute' => ['unit_code' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'panel_template_id' => 'Panel Template ID',
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
     * Gets query for [[PanelTemplate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPanelTemplate() {
        return $this->hasOne(ProjectQPanelsTemplate::class, ['id' => 'panel_template_id']);
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

    public function setAsTemplate($item, $panelTemplateId) {
        $this->panel_template_id = $panelTemplateId;
        $this->item_description = $item->item_description;
        $this->cost = $item->cost;
        $this->markup = $item->markup;
        $this->amount = $item->amount;
        $this->sort = $item->sort;
        $this->product_id = $item->product_id;
        $this->quantity = $item->quantity;
        $this->unit_code = $item->unit_code;
        return $this->save();
    }

    public function cloneTemplate($itemTemplate, $panelTemplateId) {
        $this->panel_template_id = $panelTemplateId;
        $this->item_description = $itemTemplate->item_description;
        $this->cost = $itemTemplate->cost;
        $this->markup = $itemTemplate->markup;
        $this->amount = $itemTemplate->amount;
        $this->sort = $itemTemplate->sort;
        $this->product_id = $itemTemplate->product_id;
        return $this->save();
    }

    public function clonePanelItemFromMother($thisPanelId) {
        $newItem = new ProjectQPanelItemsTemplate();
        $newItem->panel_template_id = $thisPanelId;
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

    public function processNewItem($panelId, $itemDesc, $itemPrice) {
        $this->panel_template_id = $panelId;
        $this->item_description = $itemDesc;
        $this->amount = $itemPrice;
        $max = ProjectQPanelItemsTemplate::find()->where(['panel_template_id' => $this->panel_template_id])->max('sort');
        $this->sort = $max + 1;
        return $this->save();
    }

    public function updateSort($sorting) {
        $this->sort = $sorting;
        $this->update();
    }

}
