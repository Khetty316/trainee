<?php

namespace frontend\models\covid\form;

use Yii;

/**
 * This is the model class for table "ref_covid_react".
 *
 * @property int $id
 * @property string|null $description
 * @property int|null $alert_level
 *
 * @property RefCovidPlaces[] $refCovidPlaces
 * @property RefCovidSymptoms[] $refCovidSymptoms
 */
class RefCovidReact extends \yii\db\ActiveRecord {

    const placeHaveReason = 3;
    const highestAlertLevel = 6;
    const alertLevel = 2;
    const waitForResult = 7;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_covid_react';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['alert_level'], 'integer'],
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
            'alert_level' => 'Alert Level',
        ];
    }

    /**
     * Gets query for [[RefCovidPlaces]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefCovidPlaces() {
        return $this->hasMany(RefCovidPlaces::className(), ['react_id' => 'id']);
    }

    /**
     * Gets query for [[RefCovidSymptoms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefCovidSymptoms() {
        return $this->hasMany(RefCovidSymptoms::className(), ['react_id' => 'id']);
    }

}
