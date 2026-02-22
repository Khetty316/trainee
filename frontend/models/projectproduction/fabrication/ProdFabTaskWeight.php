<?php

namespace frontend\models\projectproduction\fabrication;

use Yii;
use frontend\models\common\RefProjectQTypes;
use frontend\models\ProjectProduction\ProjectProductionPanels;
use frontend\models\ProjectProduction\fabrication\RefProjProdTaskFab;
use frontend\models\ProjectProduction\fabrication\ProductionFabTasks;

/**
 * This is the model class for table "prod_fab_task_weight".
 *
 * @property int $id
 * @property int|null $proj_prod_panel_id
 * @property string|null $panel_type
 * @property float|null $panel_type_weight
 * @property float|null $assemble
 * @property float|null $bend
 * @property float|null $cutnpunch
 * @property float|null $dispatch
 * @property float|null $powcoat
 * @property float|null $weldngrind
 * @property float|null $weld
 * @property float|null $grind
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProjectProductionPanels $projProdPanel
 * @property RefProjectQTypes $panelType
 */
class ProdFabTaskWeight extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'prod_fab_task_weight';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proj_prod_panel_id', 'created_by', 'updated_by'], 'integer'],
            [['panel_type_weight', 'assemble', 'bend', 'cutnpunch', 'dispatch', 'powcoat', 'weldngrind', 'weld', 'grind'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['panel_type'], 'string', 'max' => 10],
            [['proj_prod_panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanels::className(), 'targetAttribute' => ['proj_prod_panel_id' => 'id']],
            [['panel_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectQTypes::className(), 'targetAttribute' => ['panel_type' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'proj_prod_panel_id' => 'Proj Prod Panel ID',
            'panel_type' => 'Panel Type',
            'panel_type_weight' => 'Panel Type Weight',
            'assemble' => 'Assemble',
            'bend' => 'Bend',
            'cutnpunch' => 'Cutnpunch',
            'dispatch' => 'Dispatch',
            'powcoat' => 'Powcoat',
            'weldngrind' => 'Weldngrind',
            'weld' => 'Weld',
            'grind' => 'Grind',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ProjProdPanel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjProdPanel() {
        return $this->hasOne(ProjectProductionPanels::className(), ['id' => 'proj_prod_panel_id']);
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
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    /**
     * Save default value for both panel and task weights
     */
    public function saveDefaultFabPanelTaskWeight($panelId, $taskCode) {
        $panel = ProjectProductionPanels::findOne($panelId);
        if (!$panel) {
            return false;
        }

        $panelType = $panel->panelType;
        $taskWeight = RefProjProdTaskFab::findOne($taskCode);

        $model = ProdFabTaskWeight::find()->where(['proj_prod_panel_id' => $panelId])->one();
        if (!$model) {
            $model = new ProdFabTaskWeight();
        }

        if ($model->getAttribute($taskCode) === null) {
            $model->setAttribute($taskCode, $taskWeight->weight);
        }
        $model->proj_prod_panel_id = $panelId;
        $model->panel_type = $panelType->code;
        $model->panel_type_weight = $panelType->fab_dept_percentage;
        $model->save();

        return true;
    }

    /**
     * Updates panel weight
     * @param array $postData containing panel weights.
     * @return bool True if all weights are updated successfully, false otherwise.
     */
    public function updateFabPanelWeight($postData) {
        foreach ($postData['ProdFabPanelWeight'] as $panelId => $data) {
            $fabTaskWeight = ProdFabTaskWeight::find()->where(['proj_prod_panel_id' => $panelId])->one();
            if (!$fabTaskWeight) {
                $fabTaskWeight = new ProdFabTaskWeight();
                $fabTaskWeight->proj_prod_panel_id = $panelId;
            }
            foreach ($data as $attribute => $value) {
                $fabTaskWeight->panel_type_weight = $value['weight'];
            }
            if (!$fabTaskWeight->save()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Updates fabrication task weight
     * @param type $postData containing task weights.
     */
    public function updateFabTaskWeight($postData) {
        if (isset($postData['ProdFabTaskWeight'])) {
            foreach ($postData['ProdFabTaskWeight'] as $panelId => $data) {
                $fabTask = ProductionFabTasks::find()->where(['proj_prod_panel_id' => $panelId])->all();
                $fabTaskWeight = ProdFabTaskWeight::find()->where(['proj_prod_panel_id' => $panelId])->one();
                foreach ($data as $taskCode => $value) {
                    foreach ($fabTask as $fab) {
                        if ($taskCode === $fab['fab_task_code']) {
                            $fabTaskWeight->{$taskCode} = $value['weight'];
                        }
                    }
                    if (!$fabTaskWeight->save()) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Updates the task weight to null after the task has been deleted
     */
    public function updateFabTaskWeightAfterDeleteTask($panelId, $fabTaskCode) {
        $fabTaskWeights = ProdFabTaskWeight::find()->where(['proj_prod_panel_id' => $panelId])->one();
        if (!$fabTaskWeights) {
            return false;
        }

        $fabTaskWeights->{$fabTaskCode} = null;
        $fabTaskWeights->save();
        return true;
    }

    public function getTaskCompletionPercentage($panelDetail) {
        $taskCodes = RefProjProdTaskFab::find()->select(['code'])->asArray()->all();
        $totalAllTaskWeight = 0;
        $totalCompletedTaskWeight = 0;

        $amount = $panelDetail['amount'];
        $getWeights = self::find()->where(['proj_prod_panel_id' => $panelDetail['project_production_panel_id']])->one();

        if ($getWeights) {
            foreach ($taskCodes as $key => $detail) {
                foreach ($detail as $attribute => $taskCode) {
                    if (isset($getWeights->{$taskCode})) {
                        $totalAllTaskWeight += $getWeights->{$taskCode};
                    }
                }
            }

            if (isset($getWeights->{$panelDetail['task_code']})) {
                $completedTaskWeight = $getWeights->{$panelDetail['task_code']};
                $totalCompletedTaskWeight = $amount * $completedTaskWeight;
            }
        }

        $percentage = ($totalAllTaskWeight > 0) ? ($totalCompletedTaskWeight / $totalAllTaskWeight) * 100 : 0;

        return $percentage;
    }
}
