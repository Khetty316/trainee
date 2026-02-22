<?php

namespace frontend\models\ProjectProduction\electrical;

use Yii;

/**
 * This is the model class for table "ref_proj_prod_task_elec".
 *
 * @property string $code
 * @property string|null $name
 * @property int|null $sort
 * @property int $active_sts
 * @property float|null $weight
 *
 * @property ProductionElecTasks[] $prodElecTasks
 * @property ProductionElecTaskTrail[] $productionElecTaskTrails
 * @property ProductionElecTasks[] $productionElecTasks
 * @property ProjectProductionPanels[] $projProdPanels
 */
class RefProjProdTaskElec extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_proj_prod_task_elec';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code'], 'required'],
            [['sort', 'active_sts'], 'integer'],
            [['weight'], 'number'],
            [['code'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 255],
            [['sort'], 'unique'],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'name' => 'Name',
            'sort' => 'Sort',
            'active_sts' => 'Active Sts',
            'weight' => 'Weight',
        ];
    }

    /**
     * Gets query for [[ProdElecTasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdElecTasks() {
        return $this->hasMany(ProductionElecTasks::class, ['id' => 'prod_elec_task_id'])->viaTable('production_elec_task_trail', ['elec_task_code' => 'code']);
    }

    /**
     * Gets query for [[ProductionElecTaskTrails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionElecTaskTrails() {
        return $this->hasMany(ProductionElecTaskTrail::class, ['elec_task_code' => 'code']);
    }

    /**
     * Gets query for [[ProductionElecTasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionElecTasks() {
        return $this->hasMany(ProductionElecTasks::class, ['elec_task_code' => 'code']);
    }

    /**
     * Gets query for [[ProjProdPanels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjProdPanels() {
        return $this->hasMany(ProjectProductionPanels::class, ['id' => 'proj_prod_panel_id'])->viaTable('production_elec_tasks', ['elec_task_code' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(self::find()->orderBy(['sort' => SORT_ASC])->all(), "code", "name");
    }

    public static function getActiveDropDownList() {
        return \yii\helpers\ArrayHelper::map(self::find()->where(['active_sts' => '1'])->orderBy(['sort' => SORT_ASC])->all(), "code", "name");
    }

    public static function getActiveDropDownList1() {
        return \yii\helpers\ArrayHelper::map(self::find()->where(['active_sts' => '1'])->orderBy(['sort' => SORT_ASC])->all(), "name", "name");
    }

    public static function getAllActiveSorted() {
        return self::find()->where(['active_sts' => '1'])->orderBy(['sort' => SORT_ASC])->all();
    }
    
    public function getTaskName($taskCode) {
        $refTask = RefProjProdTaskElec::findOne(['code' => $taskCode]);
        return $refTask->name;
    }
}
