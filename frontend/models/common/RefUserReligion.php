<?php

namespace frontend\models\common;

use Yii;
use common\models\User;
/**
 * This is the model class for table "ref_user_religion".
 *
 * @property int $id
 * @property string $religion_name
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property User[] $users
 */
class RefUserReligion extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_user_religion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['religion_name'], 'required'],
            [['created_at'], 'safe'],
            [['created_by'], 'integer'],
            [['religion_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'religion_name' => 'Religion Name',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        return $this->hasMany(User::className(), ['religion_id' => 'id']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefUserReligion::find()->orderBy(['religion_name' => SORT_ASC])->all(), "id", "religion_name");
    }

}
