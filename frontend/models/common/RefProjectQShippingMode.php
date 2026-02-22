<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_project_q_shipping_mode".
 *
 * @property string $code
 * @property string|null $short_description
 * @property string|null $description
 */
class RefProjectQShippingMode extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_project_q_shipping_mode';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code'], 'required'],
            [['code'], 'string', 'max' => 10],
            [['short_description', 'description'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'short_description' => 'Short Description',
            'description' => 'Description',
        ];
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefProjectQShippingMode::find()->orderBy(['code' => SORT_ASC])->all(), "code", "short_description");
    }

    public static function getAutocompleteList() {
        return RefProjectQShippingMode::find()->select(['code as value', 'code as id', 'CONCAT(code," - ",short_description," - ",description) as label'])
                        ->orderBy(['code' => SORT_ASC])
                        ->asArray()
                        ->all();
    }

}
