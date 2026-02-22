<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_project_q_types".
 *
 * @property string $code
 * @property string $project_type_name
 * @property float|null $fab_dept_percentage
 * @property float|null $elec_dept_percentage
 *
 * @property ProjectQTypes[] $projectQTypes
 */
class RefProjectQTypes extends \yii\db\ActiveRecord {
    
    const CODE_AUTO = 'auto';
    const CODE_ENC = 'enc';
    const CODE_LV = 'lv';
    const CODE_MECH = 'mech';
    const CODE_SERV = 'serv';
    const CODE_TRADE = 'trade';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_project_q_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code', 'project_type_name'], 'required'],
            [['fab_dept_percentage', 'elec_dept_percentage'], 'number'],
            [['code'], 'string', 'max' => 10],
            [['project_type_name'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'project_type_name' => 'Project Type Name',
            'fab_dept_percentage' => 'Fabrication Percentage',
            'elec_dept_percentage' => 'Electrical Percentage',
        ];
    }

    /**
     * Gets query for [[ProjectQTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQTypes() {
        return $this->hasMany(ProjectQTypes::class, ['type' => 'code']);
    }

    /**
     * by Khetty, 15/1/2024
     * **** Calculate the total percentage ****
     */
    public function calculateTotalPercentage($data) {
        $totalPercentage = (float) $data['fab_dept_percentage'] + $data['elec_dept_percentage'];

        return $totalPercentage <= 100;
    }

    /**
     * Get Dropdown List
     * @return type
     */
    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(self::find()->orderBy(['project_type_name' => SORT_ASC])->all(), "code", "project_type_name");
    }

}
