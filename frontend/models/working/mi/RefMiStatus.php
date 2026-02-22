<?php

namespace frontend\models\working\mi;

use Yii;

/**
 * This is the model class for table "ref_mi_status".
 *
 * @property int $mi_status_code
 * @property string $status
 * @property int $active
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property MasterIncomings[] $masterIncomings
 */
class RefMiStatus extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_mi_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['mi_status_code', 'status'], 'required'],
            [['mi_status_code', 'active'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'string', 'max' => 255],
            [['mi_status_code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'mi_status_code' => 'Mi Status Code',
            'status' => 'Status',
            'active' => 'Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[MasterIncomings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMasterIncomings() {
        return $this->hasMany(MasterIncomings::className(), ['mi_status' => 'mi_status_code']);
    }

    public static function getActiveDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefMiStatus::find()->orderBy(['mi_status_code' => SORT_ASC])->all(), "mi_status_code", "status");
    }

}
