<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "ref_test_points".
 *
 * @property string $code
 * @property string|null $name
 * @property int|null $status
 * @property int|null $order
 * @property int|null $type
 * @property string|null $created_at
 * @property int|null $created_in
 * @property string|null $updated_at
 * @property int|null $updated_in
 *
 * @property TestDetailComponent[] $testDetailComponents
 * @property TestDetailFunctionality[] $testDetailFunctionalities
 */
class RefTestPoints extends \yii\db\ActiveRecord {

    const TYPE_METER = 1;
    const TYPE_BUSBAR = 2;
    const TYPE_PARTICULARFN = 3;
    const TYPE_PROTECTIONMODE = 4;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_test_points';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name', 'status', 'order', 'type', 'created_at', 'created_in', 'updated_at', 'updated_in'], 'default', 'value' => null],
            [['code'], 'required'],
            [['status', 'order', 'type', 'created_in', 'updated_in'], 'integer'],
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
            'status' => 'Status',
            'order' => 'Order',
            'type' => 'Type',
            'created_at' => 'Created At',
            'created_in' => 'Created In',
            'updated_at' => 'Updated At',
            'updated_in' => 'Updated In',
        ];
    }

    /**
     * Gets query for [[TestDetailComponents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestDetailComponents() {
        return $this->hasMany(TestDetailComponent::class, ['pou' => 'code']);
    }

    /**
     * Gets query for [[TestDetailFunctionalities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestDetailFunctionalities() {
        return $this->hasMany(TestDetailFunctionality::class, ['pot' => 'code']);
    }

    public static function getDropDownList($type) {
        return \yii\helpers\ArrayHelper::map(self::find()->where(['status' => 1, 'type' => $type])->orderBy(['order' => SORT_ASC])->all(), "code", "name");
    }

}
