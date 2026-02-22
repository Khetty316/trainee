<?php

namespace frontend\models\common;

use Yii;
use frontend\models\working\hrdoc\HrEmployeeDocuments;

/**
 * This is the model class for table "ref_hr_doctypes".
 *
 * @property int $doc_type_id
 * @property string $doc_type_name
 * @property int $active
 * @property int $is_multiple
 * @property string|null $file_path
 * @property int|null $group_index
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property HrEmployeeDocuments[] $hrEmployeeDocuments
 */
class RefHrDoctypes extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_hr_doctypes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['doc_type_name'], 'required'],
            [['active', 'is_multiple', 'group_index'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['doc_type_name', 'file_path'], 'string', 'max' => 255],
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
            'is_multiple' => 'Is Multiple',
            'file_path' => 'File Path',
            'group_index' => 'Group Index',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[HrEmployeeDocuments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHrEmployeeDocuments() {
        return $this->hasMany(HrEmployeeDocuments::className(), ['hr_doctype' => 'doc_type_id']);
    }

    public static function getDropDownListActiveOnly() {
        return \yii\helpers\ArrayHelper::map(RefHrDoctypes::find()->where('active=1')->orderBy(['doc_type_name' => SORT_ASC])->all(), "doc_type_id", "doc_type_name");
    }

}
