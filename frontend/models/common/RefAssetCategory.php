<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_asset_category".
 *
 * @property int $id
 * @property string|null $name
 * @property int $moveable
 * @property string|null $remarks
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property AssetMaster[] $assetMasters
 * @property RefAssetSubCategory[] $refAssetSubCategories
 */
class RefAssetCategory extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_asset_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['moveable'], 'required'],
            [['moveable', 'created_by'], 'integer'],
            [['remarks'], 'string'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'moveable' => 'Moveable',
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
        return $this->hasMany(AssetMaster::className(), ['asset_category' => 'id']);
    }

    /**
     * Gets query for [[RefAssetSubCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefAssetSubCategories() {
        return $this->hasMany(RefAssetSubCategory::className(), ['asset_category_id' => 'id']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefAssetCategory::find()->orderBy(['name' => SORT_ASC])->all(), "id", "name");
    }

}
