<?php

namespace frontend\models\ProjectProduction\fabrication;

use Yii;
use frontend\models\ProjectProduction\ProjectProductionPanels;
use frontend\models\projectproduction\fabrication\TaskAssignFab;
use frontend\models\projectproduction\fabrication\TaskAssignFabComplete;

/**
 * This is the model class for table "production_fab_tasks".
 *
 * @property int $id
 * @property int $proj_prod_panel_id
 * @property string $fab_task_code
 * @property float|null $qty_total
 * @property float|null $qty_assigned
 * @property float|null $qty_completed
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property int|null $parent_id to track clone data for weld and grind
 *
 * @property ProductionFabTaskTrail[] $productionFabTaskTrails
 * @property RefProjProdTaskFab[] $fabTaskCodes
 * @property ProjectProductionPanels $projProdPanel
 * @property RefProjProdTaskFab $fabTaskCode
 * @property TaskAssignFab[] $taskAssignFabs
 */
class ProductionFabTasks extends \yii\db\ActiveRecord {

    public $staffIds = [];
    public $isVacantTask;
    public $taskName;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'production_fab_tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proj_prod_panel_id', 'fab_task_code'], 'required'],
            [['proj_prod_panel_id', 'created_by', 'updated_by', 'parent_id'], 'integer'],
            [['qty_total', 'qty_assigned', 'qty_completed'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['fab_task_code'], 'string', 'max' => 10],
            ['staffIds', 'each', 'rule' => ['int']],
            [['proj_prod_panel_id', 'fab_task_code'], 'unique', 'targetAttribute' => ['proj_prod_panel_id', 'fab_task_code']],
            [['proj_prod_panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanels::className(), 'targetAttribute' => ['proj_prod_panel_id' => 'id']],
            [['fab_task_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjProdTaskFab::className(), 'targetAttribute' => ['fab_task_code' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'proj_prod_panel_id' => 'Proj Prod Panel ID',
            'fab_task_code' => 'Fab Task Code',
            'qty_total' => 'Qty Total',
            'qty_assigned' => 'Qty Assigned',
            'qty_completed' => 'Qty Completed',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'parent_id' => 'Parent ID',
        ];
    }

    /**
     * Gets query for [[ProductionFabTaskTrails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionFabTaskTrails() {
        return $this->hasMany(ProductionFabTaskTrail::className(), ['prod_fab_task_id' => 'id']);
    }

    /**
     * Gets query for [[FabTaskCodes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFabTaskCodes() {
        return $this->hasMany(RefProjProdTaskFab::className(), ['code' => 'fab_task_code'])->viaTable('production_fab_task_trail', ['prod_fab_task_id' => 'id']);
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
     * Gets query for [[FabTaskCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFabTaskCode() {
        return $this->hasOne(RefProjProdTaskFab::className(), ['code' => 'fab_task_code']);
    }

    /**
     * Gets query for [[TaskAssignFabs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignFabs() {
        return $this->hasMany(TaskAssignFab::className(), ['prod_fab_task_id' => 'id']);
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
     * Check if the assigned qty exceeds the total qty
     * @return boolean
     */
    public function checkAndUpdateAssign() {
        $this->qty_assigned = (float) $this->getTaskAssignedTotal();
        if ($this->qty_assigned > $this->qty_total) {
            return false;
        } else {
            if (!empty($this->getDirtyAttributes())) {
                return $this->update();
            } else {
                return true;
            }
        }
    }

    /**
     * Check if the assigned qty exceeds the total qty
     * @return boolean
     */
    public function checkAndUpdateAssignCorrection() {
        $this->qty_assigned = (float) $this->getTaskAssignedTotal();
        if (!empty($this->getDirtyAttributes())) {
            return $this->update();
        } else {
            return true;
        }
    }

    /**
     * PRIVATE FUNCTION
     * return total assigned, including the new record minus the completed task
     * @return type
     */
    private function getTaskAssignedTotal() {
        $taskAssignFab = TaskAssignFab::find()->where(['prod_fab_task_id' => $this->id, 'active_sts' => 1])->sum('quantity') ?? 0;
//        $taskAssignFabComplete = TaskAssignFabComplete::find()->where(['task_assign_fab_id' => $this->id])->sum('quantity') ?? 0;
//        return ($taskAssignFab - $taskAssignFabComplete);
        return ($taskAssignFab);
    }

    /**
     * Check if the completed qty exceeds the assigned qty
     * @return boolean
     */
    public function checkAndUpdateComplete() {
        $this->qty_completed = (float) $this->getTaskCompletedTotal();
        if ($this->qty_completed > $this->qty_assigned) {
            \common\models\myTools\Mydebug::dumpFileA("Wrong again 1");
            return false;
        } else {
            if (!empty($this->getDirtyAttributes())) {
                return $this->update();
            } else {
                \common\models\myTools\Mydebug::dumpFileA("Wrong again 2");

                return true;
            }
        }
    }

    /**
     * PRIVATE FUNCTION
     * return total completed, including the new record
     * @return type
     */
    private function getTaskCompletedTotal() {
        $taskAssignFabs = TaskAssignFab::find()->where(['prod_fab_task_id' => $this->id, 'active_sts' => 1])->sum('complete_qty');
        return $taskAssignFabs;
    }

    static function getPanelTasks($panelId) {
        return ProductionFabTasks::find()
                        ->select(['production_fab_tasks.*', 'ref_proj_prod_task_fab.`name` AS taskName', 'IF(qty_total-qty_assigned>0,1,0) AS isVacantTask'])
                        ->join("INNER JOIN", "ref_proj_prod_task_fab", "ref_proj_prod_task_fab.code=production_fab_tasks.fab_task_code")
                        ->where(['proj_prod_panel_id' => $panelId, 'active_sts' => 1])
                        ->orderBy(['sort' => SORT_ASC])
//                        ->asArray()
                        ->all();
//        SELECT
//  *,
//  IF(qty_total-qty_assigned>0,1,0) AS isVacantTask
//FROM
//  production_fab_tasks
//WHERE production_fab_tasks.proj_prod_panel_id = 348
    }

    public function getFabTaskValue($panelId) {
//        $fabTaskCodes = ProductionFabTasks::find()->select('fab_task_code')->where(['proj_prod_panel_id' => $panelId])->column();
        $fabTaskCodes = ProductionFabTasks::find()
                ->select('fab_task_code')
                ->leftJoin('ref_proj_prod_task_fab ref', 'ref.code = production_fab_tasks.fab_task_code')
                ->where(['proj_prod_panel_id' => $panelId])
                ->andWhere(['ref.active_sts' => 1])
                ->orderBy(['ref.sort' => SORT_ASC])
                ->column();
        
        if (empty($fabTaskCodes)) {
            return null;
        } else {
            $fabTaskWeight = ProdFabTaskWeight::find()->select($fabTaskCodes)->where(['proj_prod_panel_id' => $panelId])->asArray()->one();
            return $fabTaskWeight;
        }
    }
}
