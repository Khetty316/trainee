<?php

namespace frontend\models\projectproduction\fabrication;

use Yii;

/**
 * This is the model class for table "ref_proj_prod_task_fab".
 *
 * @property string $code
 * @property string|null $name
 * @property int|null $sort
 * @property int $active_sts
 * @property float|null $weight
 *
 * @property ProductionFabTasks[] $prodFabTasks
 * @property ProductionFabTaskTrail[] $productionFabTaskTrails
 * @property ProductionFabTasks[] $productionFabTasks
 * @property ProjectProductionPanels[] $projProdPanels
 * @property RefTaskWeightFab[] $refTaskWeightFabs
 */
class RefProjProdTaskFab extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_proj_prod_task_fab';
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
     * Gets query for [[ProdFabTasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdFabTasks() {
        return $this->hasMany(ProductionFabTasks::class, ['id' => 'prod_fab_task_id'])->viaTable('production_fab_task_trail', ['fab_task_code' => 'code']);
    }

    /**
     * Gets query for [[ProductionFabTaskTrails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionFabTaskTrails() {
        return $this->hasMany(ProductionFabTaskTrail::class, ['fab_task_code' => 'code']);
    }

    /**
     * Gets query for [[ProductionFabTasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionFabTasks() {
        return $this->hasMany(ProductionFabTasks::class, ['fab_task_code' => 'code']);
    }

    /**
     * Gets query for [[ProjProdPanels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjProdPanels() {
        return $this->hasMany(ProjectProductionPanels::class, ['id' => 'proj_prod_panel_id'])->viaTable('production_fab_tasks', ['fab_task_code' => 'code']);
    }

    /**
     * Gets query for [[RefTaskWeightFabs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefTaskWeightFabs()
    {
        return $this->hasMany(RefTaskWeightFab::className(), ['task_code' => 'code']);
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
        $refTask = RefProjProdTaskFab::findOne(['code' => $taskCode]);
        return $refTask->name;
    }
}
