<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_general_references".
 *
 * @property string $code
 * @property string $value
 * @property string|null $remarks
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class RefGeneralReferences extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_general_references';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code', 'value'], 'required'],
            [['remarks'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['code'], 'string', 'max' => 20],
            [['value'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'value' => 'Value',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getValue($code) {
        return RefGeneralReferences::findOne($code);
    }

}
