<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "ref_project_q_status".
 *
 * @property string $code
 * @property string $project_status_name
 *
 * @property ProjectQMasters[] $projectQMasters
 */
class RefProjectQStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_project_q_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'project_status_name'], 'required'],
            [['code'], 'string', 'max' => 10],
            [['project_status_name'], 'string', 'max' => 255],
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
            'project_status_name' => 'Project Status Name',
        ];
    }

    /**
     * Gets query for [[ProjectQMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQMasters()
    {
        return $this->hasMany(ProjectQMasters::className(), ['status' => 'code']);
    }
}
