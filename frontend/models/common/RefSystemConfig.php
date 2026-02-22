<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_system_config".
 *
 * @property string $code
 * @property string $value
 * @property string|null $description
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $update_at
 * @property int|null $updated_by
 */
class RefSystemConfig extends \yii\db\ActiveRecord {

    const SYS_Code_defaultAnnualLeaveBringOverExec = 'default_bring_forward_days_exec';
    const SYS_Code_defaultAnnualLeaveBringOverOffice = 'default_bring_forward_days_office';
    const SYS_Code_defaultEmailRetraceTime = 'default_email_retrace_time';
    const SYS_Code_defaultHrEmail = 'default_hr_email';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_system_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code', 'value'], 'required'],
            [['description'], 'string'],
            [['created_at', 'update_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['code'], 'string', 'max' => 50],
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
            'description' => 'Description',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'update_at' => 'Update At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getValue_defaultBringOverExec() {
        return ((RefSystemConfig::findOne(self::SYS_Code_defaultAnnualLeaveBringOverExec))->value) ?? null;
    }

    public static function getValue_defaultBringOverOffice() {
        return ((RefSystemConfig::findOne(self::SYS_Code_defaultAnnualLeaveBringOverOffice))->value) ?? null;
    }

    public static function getValue_defaultEmailRetraceTime() {
        return ((RefSystemConfig::findOne(self::SYS_Code_defaultEmailRetraceTime))->value) ?? null;
    }

    public static function getValue_defaultHrEmail() {
        return (RefSystemConfig::findOne(self::SYS_Code_defaultHrEmail)) ?? null;
    }

}
