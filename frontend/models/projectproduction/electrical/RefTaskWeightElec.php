<?php

namespace frontend\models\projectproduction\electrical;

use Yii;
use frontend\models\common\RefProjectQTypes;
use frontend\models\projectproduction\electrical\RefProjProdTaskElec;

/**
 * This is the model class for table "ref_task_weight_elec".
 *
 * @property int $id
 * @property string $task_code
 * @property string $panel_type
 * @property float|null $task_weight
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property RefProjProdTaskElec $taskCode
 * @property RefProjectQTypes $panelType
 */
class RefTaskWeightElec extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_task_weight_elec';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['task_code', 'panel_type'], 'required'],
            [['task_weight'], 'number'],
            [['updated_at'], 'safe'],
            [['updated_by'], 'integer'],
            [['task_code', 'panel_type'], 'string', 'max' => 10],
            [['task_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjProdTaskElec::className(), 'targetAttribute' => ['task_code' => 'code']],
            [['panel_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectQTypes::className(), 'targetAttribute' => ['panel_type' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'task_code' => 'Task Code',
            'panel_type' => 'Panel Type',
            'task_weight' => 'Task Weight',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[TaskCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskCode() {
        return $this->hasOne(RefProjProdTaskElec::className(), ['code' => 'task_code']);
    }

    /**
     * Gets query for [[PanelType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPanelType() {
        return $this->hasOne(RefProjectQTypes::className(), ['code' => 'panel_type']);
    }

    public function beforeSave($insert) {
        $this->updated_at = new \yii\db\Expression('NOW()');
        $this->updated_by = Yii::$app->user->identity->id;

        return parent::beforeSave($insert);
    }
    
    /**
     * Updates the default task weight for each task code
     */
    public function updateRefTaskWeight($data, $paneltype) {
        foreach ($data as $taskCode => $value) {
            $model = RefTaskWeightElec::find()->where(['task_code' => $taskCode, 'panel_type' => $paneltype])->one();
            $model->task_weight = $value['weight'];
            if (!$model->update()) {
                return false;
            }
        }
        return true;
    }
}
