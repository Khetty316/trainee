<?php

namespace frontend\models\working\hrdoc;

use Yii;

/**
 * This is the model class for table "hr_public_documents_read".
 *
 * @property int $id
 * @property int|null $hr_public_doc_id
 * @property int|null $employee_id
 * @property int|null $is_read
 * @property string|null $read_at
 *
 * @property User $employee
 */
class HrPublicDocumentsRead extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hr_public_documents_read';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hr_public_doc_id', 'employee_id', 'is_read'], 'integer'],
            [['read_at'], 'safe'],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['employee_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hr_public_doc_id' => 'Hr Public Doc ID',
            'employee_id' => 'Employee ID',
            'is_read' => 'Is Read',
            'read_at' => 'Read At',
        ];
    }

    /**
     * Gets query for [[Employee]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(User::className(), ['id' => 'employee_id']);
    }
}
