<?php

namespace frontend\models\working\mi;

use Yii;

/**
 * This is the model class for table "ref_mi_filetypes".
 *
 * @property int $file_type_id
 * @property string $file_type_name
 * @property int $active
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property MasterIncomings[] $masterIncomings
 */
class RefMiFiletypes extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_mi_filetypes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['file_type_name'], 'required'],
            [['active'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['file_type_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'file_type_id' => 'File Type ID',
            'file_type_name' => 'File Type Name',
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
        return $this->hasMany(MasterIncomings::className(), ['file_type_id' => 'file_type_id']);
    }

    public static function getActiveDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefMiFiletypes::findAll(["active" => "1"]), "file_type_id", "file_type_name");
    }

}
