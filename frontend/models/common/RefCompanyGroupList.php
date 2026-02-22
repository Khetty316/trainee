<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_company_group_list".
 *
 * @property string $code
 * @property string|null $company_name
 * @property string|null $company_id
 * @property string|null $logo_name
 * @property string|null $company_addr_1
 * @property string|null $company_addr_2
 * @property string|null $company_addr_3
 * @property string|null $company_addr_4
 * @property string|null $email
 * @property string|null $fax
 * @property string|null $tel
 * @property string|null $sst_value
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property ProjectQMasters[] $projectQMasters
 * @property User[] $users
 */
class RefCompanyGroupList extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_company_group_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code'], 'required'],
            [['created_at'], 'safe'],
            [['created_by'], 'integer'],
            [['code'], 'string', 'max' => 10],
            [['company_name', 'company_id', 'logo_name', 'company_addr_1', 'company_addr_2', 'company_addr_3', 'company_addr_4', 'email', 'fax', 'tel', 'sst_value'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'company_name' => 'Company Name',
            'company_id' => 'Company ID',
            'logo_name' => 'Logo Name',
            'company_addr_1' => 'Company Addr 1',
            'company_addr_2' => 'Company Addr 2',
            'company_addr_3' => 'Company Addr 3',
            'company_addr_4' => 'Company Addr 4',
            'email' => 'Email',
            'fax' => 'Fax',
            'tel' => 'Tel',
            'sst_value' => 'Sst Value',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[ProjectQMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQMasters() {
        return $this->hasMany(ProjectQMasters::class, ['company_group_code' => 'code']);
}

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        return $this->hasMany(User::class, ['company_name' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefCompanyGroupList::find()->orderBy(['company_name' => SORT_ASC])->all(), "code", "company_name");
    }

}
