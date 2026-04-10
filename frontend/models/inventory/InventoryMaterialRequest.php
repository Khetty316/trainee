<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;

/**
 * This is the model class for table "inventory_material_request".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $reference_type 1 = proj, 2 = cm, 3 = pm, 4 = others
 * @property string|null $reference_id
 * @property string|null $desc
 * @property int|null $inventory_detail_id
 * @property int|null $request_qty
 * @property int|null $approved_qty
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $approved_by
 * @property string|null $approved_at
 * @property int|null $status 0 = waiting for approval, 1 = approved, 2 = rejected
 *
 * @property User $user
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $approvedBy
 * @property InventoryDetail $inventoryDetail
 */
class InventoryMaterialRequest extends \yii\db\ActiveRecord {

    public $inventory_brand_id;
    public $inventory_model_id;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_material_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'reference_type', 'inventory_detail_id', 'request_qty', 'approved_qty', 'created_by', 'updated_by', 'approved_by', 'status'], 'integer'],
            [['inventory_model_id', 'inventory_brand_id', 'created_at', 'updated_at', 'approved_at'], 'safe'],
            [['reference_id', 'desc'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approved_by' => 'id']],
            [['inventory_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryDetail::className(), 'targetAttribute' => ['inventory_detail_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'Request For',
            'reference_type' => 'Reference Type',
            'reference_id' => 'Reference ID',
            'desc' => 'Description',
            'inventory_detail_id' => 'Inventory Detail ID',
            'request_qty' => 'Request Qty',
            'approved_qty' => 'Issued Qty',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'approved_by' => 'Verified By',
            'approved_at' => 'Verified At',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
     * Gets query for [[ApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedBy() {
        return $this->hasOne(User::className(), ['id' => 'approved_by']);
    }

    /**
     * Gets query for [[InventoryDetail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryDetail() {
        return $this->hasOne(InventoryDetail::className(), ['id' => 'inventory_detail_id']);
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
}
