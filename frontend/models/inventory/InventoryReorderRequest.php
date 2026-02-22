<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;
use frontend\models\inventory\InventoryModel;
use frontend\models\inventory\InventoryBrand;

/**
 * This is the model class for table "inventory_reorder_request".
 *
 * @property int $id
 * @property int|null $inventory_model_id
 * @property int|null $inventory_brand_id
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property int|null $qty
 * @property int|null $created_by
 * @property string|null $created_at
 *
 * @property InventoryModel $inventoryModel
 * @property InventoryBrand $inventoryBrand
 * @property User $createdBy
 */
class InventoryReorderRequest extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_reorder_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['inventory_model_id', 'inventory_brand_id', 'reference_id', 'qty', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['reference_type'], 'string', 'max' => 100],
            [['inventory_model_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryModel::className(), 'targetAttribute' => ['inventory_model_id' => 'id']],
            [['inventory_brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryBrand::className(), 'targetAttribute' => ['inventory_brand_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'inventory_model_id' => 'Inventory Model ID',
            'inventory_brand_id' => 'Inventory Brand ID',
            'reference_type' => 'Reference Type',
            'reference_id' => 'Reference ID',
            'qty' => 'Qty',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
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
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }
}
