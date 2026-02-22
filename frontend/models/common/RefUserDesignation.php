<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_user_designation".
 *
 * @property int $id
 * @property string $design_name
 * @property string $staff_type
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property User[] $users
 */
class RefUserDesignation extends \yii\db\ActiveRecord {

    const TYPE_Director = 'director';
    const TYPE_Production = 'prod';
    const TYPE_Office = 'office';
    const TYPE_Executive = 'exec';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_user_designation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['design_name', 'staff_type'], 'required'],
            [['created_at'], 'safe'],
            [['created_by'], 'integer'],
            [['design_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'design_name' => 'Design Name',
            'staff_type' => 'Staff Type',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        return $this->hasMany(User::className(), ['designation' => 'id']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefUserDesignation::find()->orderBy(['design_name' => SORT_ASC])->all(), "id", "design_name");
    }

    public static function getDropDownListNoDirector() {
        return [
            'prod' => 'Production',
            'exec' => 'Executive',
            'office' => 'Office',
        ];
    }

}
