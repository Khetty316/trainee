<?php

namespace frontend\models\working\mi;

use Yii;

/**
 * This is the model class for table "ref_mi_doctypes".
 *
 * @property int $doc_type_id
 * @property string $doc_type_name
 * @property int $active
 * @property int $calculate_cost
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property MasterIncomings[] $masterIncomings
 * @property RefMiMatrices[] $refMiMatrices
 */
class RefMiDoctypes extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_mi_doctypes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['doc_type_name'], 'required'],
            [['active', 'calculate_cost'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['doc_type_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'doc_type_id' => 'Doc Type ID',
            'doc_type_name' => 'Doc Type Name',
            'active' => 'Active',
            'calculate_cost' => 'Calculate Cost',
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
        return $this->hasMany(MasterIncomings::className(), ['doc_type_id' => 'doc_type_id']);
    }

    /**
     * Gets query for [[RefMiMatrices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefMiMatrices() {
        return $this->hasMany(RefMiMatrices::className(), ['doc_type_id' => 'doc_type_id']);
    }

    public static function getActiveDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefMiDoctypes::find(["active" => "1"])->orderBy(['doc_type_name'=>SORT_ASC])->all(), "doc_type_id", "doc_type_name");
    }
    public static function getInvoiceDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefMiDoctypes::find(["active" => "1"])->where('doc_type_id in (2,3,4)')->orderBy(['doc_type_name'=>SORT_ASC])->all(), "doc_type_id", "doc_type_name");
    }

}
