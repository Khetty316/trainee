<?php

namespace frontend\models\ProjectProduction;

use Yii;

/**
 * This is the model class for table "ref_project_item_unit".
 *
 * @property string $code
 * @property string $unit_name_single
 * @property string $unit_name_plural
 * @property int|null $created_by
 * @property string $created_at
 *
 * @property ProjectProductionPanelItems[] $projectProductionPanelItems
 */
class RefProjectItemUnit extends \yii\db\ActiveRecord {

    CONST DEFAULT_Code = "unit";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_project_item_unit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code', 'unit_name_single', 'unit_name_plural'], 'required'],
            [['created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['code'], 'string', 'max' => 10],
            [['unit_name_single', 'unit_name_plural'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'unit_name_single' => 'Unit Name Single',
            'unit_name_plural' => 'Unit Name Plural',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[ProjectProductionPanelItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanelItems() {
        return $this->hasMany(ProjectProductionPanelItems::className(), ['unit_code' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(self::find()->orderBy(['unit_name_single' => SORT_ASC])->all(), "code", "unit_name_single");
    }

}
