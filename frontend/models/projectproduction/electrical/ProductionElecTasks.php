<?php

namespace frontend\models\ProjectProduction\electrical;

use Yii;
use frontend\models\ProjectProduction\ProjectProductionPanels;
use frontend\models\projectproduction\electrical\TaskAssignElec;
use frontend\models\projectproduction\electrical\TaskAssignElecComplete;
use frontend\models\projectproduction\electrical\ProdElecTaskWeight;
/**
 * This is the model class for table "production_elec_tasks".
 *
 * @property int $id
 * @property int $proj_prod_panel_id
 * @property string $elec_task_code
 * @property float|null $qty_total
 * @property float|null $qty_assigned
 * @property float|null $qty_completed
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProductionElecTaskTrail[] $productionElecTaskTrails
 * @property RefProjProdTaskElec[] $elecTaskCodes
 * @property ProjectProductionPanels $projProdPanel
 * @property RefProjProdTaskElec $elecTaskCode
 * @property TaskAssignElec[] $taskAssignElecs
 */
class ProductionElecTasks extends \yii\db\ActiveRecord {

    public $staffIds = [];
    public $isVacantTask;
    public $taskName;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'production_elec_tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proj_prod_panel_id', 'elec_task_code'], 'required'],
            [['proj_prod_panel_id', 'created_by', 'updated_by'], 'integer'],
            [['qty_total', 'qty_assigned', 'qty_completed'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['elec_task_code'], 'string', 'max' => 10],
            ['staffIds', 'each', 'rule' => ['int']],
            [['proj_prod_panel_id', 'elec_task_code'], 'unique', 'targetAttribute' => ['proj_prod_panel_id', 'elec_task_code']],
            [['proj_prod_panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanels::className(), 'targetAttribute' => ['proj_prod_panel_id' => 'id']],
            [['elec_task_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjProdTaskElec::className(), 'targetAttribute' => ['elec_task_code' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'proj_prod_panel_id' => 'Proj Prod Panel ID',
            'elec_task_code' => 'Elecrical Task Code',
            'qty_total' => 'Qty Total',
            'qty_assigned' => 'Qty Assigned',
            'qty_completed' => 'Qty Completed',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ProductionElecTaskTrails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionElecTaskTrails() {
        return $this->hasMany(ProductionElecTaskTrail::className(), ['prod_elec_task_id' => 'id']);
    }

    /**
     * Gets query for [[ElecTaskCodes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getElecTaskCodes() {
        return $this->hasMany(RefProjProdTaskElec::className(), ['code' => 'elec_task_code'])->viaTable('production_elec_task_trail', ['prod_elec_task_id' => 'id']);
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
     * Gets query for [[ElecTaskCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getElecTaskCode() {
        return $this->hasOne(RefProjProdTaskElec::className(), ['code' => 'elec_task_code']);
    }

    /**
     * Gets query for [[TaskAssignElecs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignElecs() {
        return $this->hasMany(TaskAssignElec::className(), ['prod_elec_task_id' => 'id']);
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
        $taskAssignElec = TaskAssignElec::find()->where(['prod_elec_task_id' => $this->id, 'active_sts' => 1])->sum('quantity') ?? 0;
//        $taskAssignElecComplete = TaskAssignElecComplete::find()->where(['task_assign_elec_id' => $taskAssignElec->id])->sum('quantity') ?? 0;
//        return ($taskAssignElec - $taskAssignElecComplete);
        return ($taskAssignElec);
    }

    /**
     * Check if the completed qty exceeds the assigned qty
     * @return boolean
     */
    public function checkAndUpdateComplete() {
        $this->qty_completed = (float) $this->getTaskCompletedTotal();
        if ($this->qty_completed > $this->qty_assigned) {
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
     * PRIVATE FUNCTION
     * return total completed, including the new record
     * @return type
     */
    private function getTaskCompletedTotal() {
        $taskAssignElecs = TaskAssignElec::find()->where(['prod_elec_task_id' => $this->id, 'active_sts' => 1])->sum('complete_qty');
        return $taskAssignElecs;
    }

    static function getPanelTasks($panelId) {
        return ProductionElecTasks::find()
                        ->select(['production_elec_tasks.*', 'ref_proj_prod_task_elec.`name` AS taskName', 'IF(qty_total-qty_assigned>0,1,0) AS isVacantTask'])
                        ->join("INNER JOIN", "ref_proj_prod_task_elec", "ref_proj_prod_task_elec.code=production_elec_tasks.elec_task_code")
                        ->where(['proj_prod_panel_id' => $panelId])
                        ->all();
    }

    public function getElecTaskValue($panelId) {
        $elecTaskCodes = ProductionElecTasks::find()->select('elec_task_code')->where(['proj_prod_panel_id' => $panelId])->column();
        if (empty($elecTaskCodes)) {
            return null;
        } else {
            $elecTaskWeight = ProdElecTaskWeight::find()->select($elecTaskCodes)->where(['proj_prod_panel_id' => $panelId])->asArray()->one();
            return $elecTaskWeight;
        }
    }
}
