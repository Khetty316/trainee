<?php

namespace frontend\models\working\mi;

use Yii;

/**
 * This is the model class for table "ref_mi_matrices".
 *
 * @property int $matrix_id
 * @property int $doc_type_id
 * @property int $step
 * @property int $task_id
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class RefMiMatrices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_mi_matrices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doc_type_id', 'step', 'task_id'], 'required'],
            [['doc_type_id', 'step', 'task_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'matrix_id' => 'Matrice ID',
            'doc_type_id' => 'Doc Type ID',
            'step' => 'Step',
            'task_id' => 'Task ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
