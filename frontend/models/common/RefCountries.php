<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_countries".
 *
 * @property int $country_id
 * @property string $country_code
 * @property string $country_name
 * @property string|null $alpha_3
 * @property int $calling_code
 * @property string|null $continent_name
 *
 * @property ContactMaster[] $contactMasters
 */
class RefCountries extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_countries';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['country_id', 'country_code', 'country_name', 'calling_code'], 'required'],
            [['country_id', 'calling_code'], 'integer'],
            [['country_code'], 'string', 'max' => 2],
            [['country_name'], 'string', 'max' => 80],
            [['alpha_3'], 'string', 'max' => 3],
            [['continent_name'], 'string', 'max' => 30],
            [['country_code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'country_id' => 'Country ID',
            'country_code' => 'Country Code',
            'country_name' => 'Country Name',
            'alpha_3' => 'Alpha 3',
            'calling_code' => 'Calling Code',
            'continent_name' => 'Continent Name',
        ];
    }

    /**
     * Gets query for [[ContactMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContactMasters() {
        return $this->hasMany(ContactMaster::className(), ['country' => 'country_code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefCountries::find()->orderBy(['country_name' => SORT_ASC])->all(), "country_code", "country_name");
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->id;
        } else {
            $this->updated_by = Yii::$app->user->id;
            $this->updated_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

    public function createNew($countryName, $countryCode = '') {
        $this->country_name = $countryName;

        $this->country_id = RefCountries::find()->count("1") + 1;
        if ($countryCode == "") {
            $this->country_code = $this->country_id;
        } else {
            $this->country_code = $countryCode;
        }
        return $this->save();
    }

}
