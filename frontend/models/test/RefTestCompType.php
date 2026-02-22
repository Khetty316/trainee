<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "ref_test_comp_type".
 *
 * @property string $code
 * @property string|null $name
 * @property int|null $order
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TestDetailComponent[] $testDetailComponents
 */
class RefTestCompType extends \yii\db\ActiveRecord {

    const TYPE_BREAKER = 'breaker';
    const TYPE_MCT = 'mct';
    const TYPE_PCT = 'pct';
    const TYPE_METER = 'meter';
    const TYPE_POWER = 'power';
    const TYPE_PRORELAY = 'prorelay';
    const TYPE_SURGE = 'surge';
    const TYPE_BUSBAR = 'busbar';
    const TYPE_OTHER = 'other';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_test_comp_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name', 'order', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['code'], 'required'],
            [['order', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['code', 'name'], 'string', 'max' => 255],
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
            'order' => 'Order',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[TestDetailComponents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestDetailComponents() {
        return $this->hasMany(TestDetailComponent::class, ['comp_type' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(self::find()->orderBy(['order' => SORT_ASC])->all(), "code", "name");
    }

}
