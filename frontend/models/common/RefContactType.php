<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_contact_type".
 *
 * @property string $code
 * @property string|null $contact_type_name
 *
 * @property ContactMaster[] $contactMasters
 */
class RefContactType extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_contact_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code'], 'required'],
            [['code'], 'string', 'max' => 10],
            [['contact_type_name'], 'string', 'max' => 100],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'contact_type_name' => 'Contact Type Name',
        ];
    }

    /**
     * Gets query for [[ContactMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContactMasters() {
        return $this->hasMany(ContactMaster::className(), ['contact_type' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefContactType::find()->orderBy(['contact_type_name' => SORT_ASC])->all(), "code", "contact_type_name");
    }

}
