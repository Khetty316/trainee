<?php

namespace frontend\models\common;

use Yii;
use common\models\User;

/**
 * This is the model class for table "ref_user_sex".
 *
 * @property string $code
 * @property string $sex_name
 * @property string $created_at
 * @property int|null $created_by
 */
class RefUserSex extends \yii\db\ActiveRecord {

    const CODE_MALE = 'm';
    const CODE_FEMALE = 'f';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_user_sex';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code', 'sex_name'], 'required'],
            [['created_at'], 'safe'],
            [['created_by'], 'integer'],
            [['code'], 'string', 'max' => 1],
            [['sex_name'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'sex_name' => 'Sex Name',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefUserSex::find()->orderBy(['sex_name' => SORT_ASC])->all(), "code", "sex_name");
    }

}
