<?php

namespace frontend\models\inventory;

use Yii;
use frontend\models\inventory\InventoryPurchaseOrderItem;
use common\models\User;

/**
 * This is the model class for table "inventory_purchase_order_item_doc".
 *
 * @property int $id
 * @property int|null $receive_batch_id
 * @property int|null $document_type 1 = DO, 2 = Invoice
 * @property string|null $document_no
 * @property string|null $filename
 * @property int|null $uploaded_by
 * @property string|null $uploaded_at
 * @property int|null $deleted_by
 * @property string|null $deleted_at
 *
 * @property InventoryPurchaseOrderItem $inventoryPoItem
 * @property User $uploadedBy
 * @property User $deletedBy
 * @property InventoryPurchaseOrderReceiveBatch $receiveBatch
 */
class InventoryPurchaseOrderItemDoc extends \yii\db\ActiveRecord {

    CONST receivingDocType = ['1' => 'Delivery Order', '2' => 'Invoice'];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_purchase_order_item_doc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['receive_batch_id', 'document_type', 'uploaded_by', 'deleted_by'], 'integer'],
            [['uploaded_at', 'deleted_at'], 'safe'],
            [['document_no', 'filename'], 'string', 'max' => 255],
            [['uploaded_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uploaded_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deleted_by' => 'id']],
                        [['receive_batch_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryPurchaseOrderReceiveBatch::className(), 'targetAttribute' => ['receive_batch_id' => 'id']],
            [['filename'], 'file',
                'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'],
                'maxSize' => 1024 * 1024 * 10, // 10MB
                'skipOnEmpty' => false,
                'on' => 'upload'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'receive_batch_id' => 'Receive Batch ID',
            'document_type' => 'Document Type',
            'document_no' => 'Document No',
            'filename' => 'Filename',
            'uploaded_by' => 'Uploaded By',
            'uploaded_at' => 'Uploaded At',
            'deleted_by' => 'Deleted By',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * Gets query for [[UploadedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUploadedBy() {
        return $this->hasOne(User::className(), ['id' => 'uploaded_by']);
    }

    /**
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy() {
        return $this->hasOne(User::className(), ['id' => 'deleted_by']);
    }

    /**
     * Gets query for [[ReceiveBatch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceiveBatch()
    {
        return $this->hasOne(InventoryPurchaseOrderReceiveBatch::className(), ['id' => 'receive_batch_id']);
    }

    public function beforeSave($insert) {
        $this->uploaded_at = new \yii\db\Expression('NOW()');
        $this->uploaded_by = Yii::$app->user->identity->id;

        return parent::beforeSave($insert);
    }
}
