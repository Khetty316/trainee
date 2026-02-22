<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_asset_sub_category".
 *
 * @property int $id
 * @property int|null $asset_category_id
 * @property string|null $name
 * @property string|null $remarks
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property AssetMaster[] $assetMasters
 * @property RefAssetCategory $assetCategory
 */
class RefAssetSubCategory extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_asset_sub_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['asset_category_id', 'created_by'], 'integer'],
            [['remarks'], 'string'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['asset_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefAssetCategory::className(), 'targetAttribute' => ['asset_category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'asset_category_id' => 'Asset Category ID',
            'name' => 'Name',
            'remarks' => 'Remarks',
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
        return $this->hasMany(AssetMaster::className(), ['asset_sub_category' => 'id']);
    }

    /**
     * Gets query for [[AssetCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAssetCategory() {
        return $this->hasOne(RefAssetCategory::className(), ['id' => 'asset_category_id']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefAssetSubCategory::find()->orderBy(['name' => SORT_ASC])->all(), "id", "name");
    }

}
