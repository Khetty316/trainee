<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;
use frontend\models\inventory\InventoryDetail;

/**
 * This is the model class for table "inventory_stockoutbound".
 *
 * @property int $id
 * @property int|null $inventory_detail_id
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property int|null $qty
 * @property int|null $created_by
 * @property string|null $created_at
 *
 * @property InventoryDetail $inventoryDetail
 * @property User $createdBy
 */
class InventoryStockoutbound extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_stockoutbound';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['inventory_detail_id', 'reference_id', 'qty', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['reference_type'], 'string', 'max' => 100],
            [['inventory_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryDetail::className(), 'targetAttribute' => ['inventory_detail_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'inventory_detail_id' => 'Inventory Detail ID',
            'reference_type' => 'Reference Type',
            'reference_id' => 'Reference ID',
            'qty' => 'Qty',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[InventoryDetail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryDetail() {
        return $this->hasOne(InventoryDetail::className(), ['id' => 'inventory_detail_id']);
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
