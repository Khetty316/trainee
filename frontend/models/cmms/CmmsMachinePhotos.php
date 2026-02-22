<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "cmms_machine_photos".
 *
 * @property int $id
 * @property int|null $cmms_fault_list_details_id
 * @property string|null $file_name
 * @property string|null $uploaded_at
 * @property int|null $is_deleted
 */
class CmmsMachinePhotos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_machine_photos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cmms_fault_list_details_id', 'is_deleted'], 'integer'],
            [['uploaded_at'], 'safe'],
            [['file_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cmms_fault_list_details_id' => 'Cmms Fault List Details ID',
            'file_name' => 'File Name',
            'uploaded_at' => 'Uploaded At',
            'is_deleted' => 'Is Deleted',
        ];
    }
    public function getUrl()
    {
        return \Yii::getAlias('@web') . '/uploads/cmms-fault-list/' . $this->file_name;
    }
}
