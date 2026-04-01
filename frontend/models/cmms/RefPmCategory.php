<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "ref_pm_category".
 *
 * @property int $id
 * @property string|null $category_name
 * @property int|null $active_sts
 *
 * @property CmmsPmCategoryDesc[] $cmmsPmCategoryDescs
 * @property CmmsPreventiveMaintenanceDetails[] $cmmsPreventiveMaintenanceDetails
 */
class RefPmCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_pm_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active_sts'], 'integer'],
            [['category_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_name' => 'Category Name',
            'active_sts' => 'Active Sts',
        ];
    }

    /**
     * Gets query for [[CmmsPmCategoryDescs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsPmCategoryDescs()
    {
        return $this->hasMany(CmmsPmCategoryDesc::className(), ['ref_pm_category_id' => 'id']);
    }

    /**
     * Gets query for [[CmmsPreventiveMaintenanceDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsPreventiveMaintenanceDetails()
    {
        return $this->hasMany(CmmsPreventiveMaintenanceDetails::className(), ['maintenance_category_id' => 'id']);
    }
    
    public static function getActiveDropdownlist_by_id() {
        return \yii\helpers\ArrayHelper::map(RefPmCategory::findAll(["active_sts" => 1]), "id", "category_name");
    }
}
