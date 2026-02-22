<?php

namespace frontend\models\common;

use Yii;
use frontend\models\working\project\ProspectMaster;

/**
 * This is the model class for table "ref_project_type".
 *
 * @property string $code
 * @property string $project_type_name
 *
 * @property ProspectMaster[] $prospectMasters
 */
class RefProjectType extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_project_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code', 'project_type_name'], 'required'],
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
        ];
    }

    /**
     * Gets query for [[ProspectMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProspectMasters() {
        return $this->hasMany(ProspectMaster::className(), ['project_type' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefProjectType::find()->orderBy(['project_type_name' => SORT_ASC])->all(), "code", "project_type_name");
    }

}
