<?php

namespace frontend\models\covid\form;

use Yii;

/**
 * This is the model class for table "ref_covid_testkit_type".
 *
 * @property int $id
 * @property string|null $description
 * @property int|null $additional_action
 *
 * @property CovidStatusForm[] $covidStatusForms
 */
class RefCovidTestkitType extends \yii\db\ActiveRecord {

    const COMPANY_KIT = 3;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_covid_testkit_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['additional_action'], 'integer'],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'additional_action' => 'Additional Action',
        ];
    }

    /**
     * Gets query for [[CovidStatusForms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCovidStatusForms() {
        return $this->hasMany(CovidStatusForm::className(), ['self_test_kit_type' => 'id']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefCovidTestkitType::find()->orderBy(['id' => SORT_ASC])->all(), "id", "description");
    }

}
