<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "ref_test_accessory".
 *
 * @property string $code
 * @property string|null $name
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property TestDetailComponent[] $testDetailComponents
 */
class RefTestAccessory extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_test_accessory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['code'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
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
        return $this->hasMany(TestDetailComponent::class, ['accessory' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(self::find()->all(), "code", "name");
    }

}
