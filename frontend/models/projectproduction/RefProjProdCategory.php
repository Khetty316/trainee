<?php

namespace frontend\models\ProjectProduction;

use Yii;

/**
 * This is the model class for table "ref_proj_prod_category".
 *
 * @property string $code
 * @property string|null $name
 * @property string|null $name_long
 * @property int|null $sort
 * @property int $allow_multiple
 * @property int $need_itp
 * @property int $active_sts
 *
 * @property ProjectProductionMaster[] $projectProductionMasters
 */
class RefProjProdCategory extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_proj_prod_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code'], 'required'],
            [['sort', 'allow_multiple', 'need_itp', 'active_sts'], 'integer'],
            [['code'], 'string', 'max' => 10],
            [['name', 'name_long'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'name' => 'Name',
            'name_long' => 'Name Long',
            'sort' => 'Sort',
            'allow_multiple' => 'Allow Multiple',
            'need_itp' => 'Need Itp',
            'active_sts' => 'Active Sts',
        ];
    }

    /**
     * Gets query for [[ProjectProductionMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionMasters() {
        return $this->hasMany(ProjectProductionMaster::className(), ['proj_prod_category' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefProjProdCategory::find()->orderBy(['sort' => SORT_ASC])->all(), "code", "name");
    }

}
