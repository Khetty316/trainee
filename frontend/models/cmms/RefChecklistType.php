<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "ref_checklist_type".
 *
 * @property int $id
 * @property string|null $type_name
 */
class RefChecklistType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_checklist_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_name' => 'Type Name',
        ];
    }
}
