<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "ref_inventory_status".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $active_sts 0 = no, 1 = yes
 */
class RefInventoryStatus extends \yii\db\ActiveRecord {

    CONST STATUS_Approved = 1;
    CONST STATUS_Available = 2;
    CONST STATUS_LowStock = 3;
    CONST STATUS_OutOfStock = 4;
    CONST STATUS_QuotationRequested = 5;
    CONST STATUS_QuotationReceived = 6;
    CONST STATUS_PoCreated = 7;
    CONST STATUS_AwaitingDelivery = 8;
    CONST STATUS_PartiallyReceived = 9;
    CONST STATUS_FullyReceived = 10;
    CONST STATUS_Closed = 11;
    CONST STATUS_Rejected = 12;
    
    CONST STATUS_PendingPo = [
        self::STATUS_AwaitingDelivery,
        self::STATUS_PartiallyReceived,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_inventory_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['active_sts'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'active_sts' => 'Active Sts',
        ];
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefInventoryStatus::find()->orderBy(['id' => SORT_ASC])->all(), "id", "name");
    }

    public static function getDropDownListPo() {
        $statusIds = [
            self::STATUS_PoCreated,
            self::STATUS_AwaitingDelivery,
            self::STATUS_PartiallyReceived,
            self::STATUS_FullyReceived,
        ];

        return \yii\helpers\ArrayHelper::map(
                        RefInventoryStatus::find()
                                ->where(['id' => $statusIds])
                                ->orderBy(['id' => SORT_ASC])
                                ->all(),
                        'id',
                        'name'
                );
    }
}
