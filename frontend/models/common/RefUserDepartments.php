<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_user_departments".
 *
 * @property string $code
 * @property string $department_name
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TaskAssignmentMaster[] $taskAssignmentMasters
 */
class RefUserDepartments extends \yii\db\ActiveRecord {

    CONST FAB_DEPARTMENT = 'fab';
    CONST ELEC_DEPARTMENT = 'elec';
    CONST HRADMIN_DEPARTMENT = 'hradmin';
    CONST IT_DEPARTMENT = 'it';
    CONST MECHANICAL_DEPARTMENT = 'mecha';
    CONST PROCUREMENT_DEPARTMENT = 'procure';
    CONST PROJECT_DEPARTMENT = 'proj';
    CONST TESTING_DEPARTMENT = 'test';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_user_departments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code', 'department_name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['code'], 'string', 'max' => 50],
            [['department_name'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'department_name' => 'Department Name',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[TaskAssignmentMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignmentMasters() {
        return $this->hasMany(TaskAssignmentMaster::className(), ['department' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(self::find()->orderBy(['department_name' => SORT_ASC])->all(), "code", "department_name");
    }

}
