<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_project_q_panel_unit".
 *
 * @property string $code
 * @property string $unit_name
 * @property int|null $created_by
 * @property string $created_at
 *
 * @property ProjectQPanels[] $projectQPanels
 */
class RefProjectQPanelUnit extends \yii\db\ActiveRecord {

    CONST DEFAULT_Code = "unit";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_project_q_panel_unit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code', 'unit_name'], 'required'],
            [['created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['code'], 'string', 'max' => 10],
            [['unit_name'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'unit_name' => 'Unit Name',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[ProjectQPanels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQPanels() {
        return $this->hasMany(ProjectQPanels::className(), ['unit_code' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefProjectQPanelUnit::find()->orderBy(['unit_name' => SORT_ASC])->all(), "code", "unit_name");
    }

}
