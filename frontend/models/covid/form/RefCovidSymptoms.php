<?php

namespace frontend\models\covid\form;

use Yii;

/**
 * This is the model class for table "ref_covid_symptoms".
 *
 * @property int $id
 * @property string|null $description
 * @property int|null $react_id
 *
 * @property RefCovidReact $react
 */
class RefCovidSymptoms extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_covid_symptoms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['react_id'], 'integer'],
            [['description'], 'string', 'max' => 100],
            [['react_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefCovidReact::className(), 'targetAttribute' => ['react_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'react_id' => 'React ID',
        ];
    }

    /**
     * Gets query for [[React]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReact() {
        return $this->hasOne(RefCovidReact::className(), ['id' => 'react_id']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefCovidSymptoms::find()->orderBy(['order' => SORT_ASC])->all(), "id", "description");
    }

}
