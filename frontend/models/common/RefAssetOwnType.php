<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_asset_own_type".
 *
 * @property string $code
 * @property string|null $description
 * @property int|null $order
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property AssetMaster[] $assetMasters
 */
class RefAssetOwnType extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_asset_own_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code'], 'required'],
            [['order', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['code'], 'string', 'max' => 15],
            [['description'], 'string', 'max' => 200],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'description' => 'Description',
            'order' => 'Order',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[AssetMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssetMasters() {
        return $this->hasMany(AssetMaster::className(), ['own_type' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefAssetOwnType::find()->orderBy(['order' => SORT_ASC])->all(), "code", "description");
    }

}
