<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_address".
 *
 * @property int $address_id
 * @property string|null $address_name
 * @property string|null $address_description
 * @property int|null $area_id
 * @property int $is_po_address
 * @property int $active
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $update_at
 * @property int|null $updated_by
 *
 * @property PurchaseOrderMaster[] $purchaseOrderMasters
 * @property RefArea $area
 */
class RefAddress extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['area_id', 'is_po_address', 'active', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'update_at'], 'safe'],
            [['address_name', 'address_description'], 'string', 'max' => 255],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefArea::className(), 'targetAttribute' => ['area_id' => 'area_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'address_id' => 'Address ID',
            'address_name' => 'Address Name',
            'address_description' => 'Address Description',
            'area_id' => 'Area ID',
            'is_po_address' => 'Is Po Address',
            'active' => 'Active',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'update_at' => 'Update At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[PurchaseOrderMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseOrderMasters() {
        return $this->hasMany(PurchaseOrderMaster::className(), ['po_address' => 'address_id']);
    }

    /**
     * Gets query for [[Area]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArea() {
        return $this->hasOne(RefArea::className(), ['area_id' => 'area_id']);
    }

    public static function getActiveDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefAddress::find(["active" => "1","is_po_address"=>"1"])->orderBy(['address_name'=>'SORT_ASC'])->all(), "address_id", "address_name");
    }

}
