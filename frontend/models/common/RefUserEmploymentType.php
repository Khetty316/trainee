<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_user_employment_type".
 *
 * @property string $code
 * @property string $employment_type
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property User[] $users
 */
class RefUserEmploymentType extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_user_employment_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code', 'employment_type'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['code'], 'string', 'max' => 10],
            [['employment_type'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'employment_type' => 'Employment Type',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        return $this->hasMany(User::className(), ['employment_type' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefUserEmploymentType::find()->orderBy(['employment_type' => SORT_ASC])->all(), "code", "employment_type");
    }

}
