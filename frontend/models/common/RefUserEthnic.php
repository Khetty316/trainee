<?php

namespace frontend\models\common;

use Yii;
use common\models\User;

/**
 * This is the model class for table "ref_user_ethnic".
 *
 * @property int $id
 * @property string $ethnic_name
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property User[] $users
 */
class RefUserEthnic extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_user_ethnic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['ethnic_name'], 'required'],
            [['created_at'], 'safe'],
            [['created_by'], 'integer'],
            [['ethnic_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'ethnic_name' => 'Ethnic Name',
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
        return $this->hasMany(User::className(), ['ethnic_id' => 'id']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefUserEthnic::find()->orderBy(['ethnic_name' => SORT_ASC])->all(), "id", "ethnic_name");
    }

}
