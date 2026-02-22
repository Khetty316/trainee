<?php

namespace frontend\models\working\mi;

use Yii;

/**
 * This is the model class for table "ref_mi_subdoctypes".
 *
 * @property int $sub_doc_type_id
 * @property string $sub_doc_type_name
 * @property int $active
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property MasterIncomings[] $masterIncomings
 */
class RefMiSubdoctypes extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_mi_subdoctypes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['sub_doc_type_name'], 'required'],
            [['active'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['sub_doc_type_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'sub_doc_type_id' => 'Sub Doc Type ID',
            'sub_doc_type_name' => 'Sub Doc Type Name',
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
        return $this->hasMany(MasterIncomings::className(), ['sub_doc_type_id' => 'sub_doc_type_id']);
    }

    public static function getActiveDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefMiSubdoctypes::findAll(["active" => "1"]), "sub_doc_type_id", "sub_doc_type_name");
    }

}
