<?php

namespace frontend\models\profile;

use Yii;

/**
 * This is the model class for table "ref_user_doctypes".
 *
 * @property string $code
 * @property string|null $doc_name
 * @property int $doc_limit_1
 * @property string|null $descriptions
 *
 * @property UserDocuments[] $userDocuments
 */
class RefUserDoctypes extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_user_doctypes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code'], 'required'],
            [['doc_limit_1'], 'integer'],
            [['descriptions'], 'string'],
            [['code'], 'string', 'max' => 50],
            [['doc_name'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'doc_name' => 'Doc Name',
            'doc_limit_1' => 'Doc Limit 1',
            'descriptions' => 'Descriptions',
        ];
    }

    /**
     * Gets query for [[UserDocuments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserDocuments() {
        return $this->hasMany(UserDocuments::className(), ['doctype_code' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefUserDoctypes::find()->orderBy(['doc_name' => SORT_ASC])->all(), "code", "doc_name");
    }

}
