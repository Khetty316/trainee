<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_company_details".
 *
 * @property string $code
 * @property string|null $description
 * @property string|null $value
 */
class RefCompanyDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_company_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code'], 'string', 'max' => 50],
            [['description', 'value'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Code',
            'description' => 'Description',
            'value' => 'Value',
        ];
    }
}
