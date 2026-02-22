<?php

namespace frontend\models\ProjectProduction\fabrication;

use Yii;

/**
 * This is the model class for table "production_fab_task_trail".
 *
 * @property int $id
 * @property int $prod_fab_task_id
 * @property string $fab_task_code
 * @property int|null $qty_partial_change
 * @property int|null $qty_complete_change
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProductionFabTasks $prodFabTask
 * @property RefProjProdTaskFab $fabTaskCode
 */
class ProductionFabTaskTrail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'production_fab_task_trail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['prod_fab_task_id', 'fab_task_code'], 'required'],
            [['prod_fab_task_id', 'qty_partial_change', 'qty_complete_change', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['fab_task_code'], 'string', 'max' => 10],
            [['prod_fab_task_id', 'fab_task_code'], 'unique', 'targetAttribute' => ['prod_fab_task_id', 'fab_task_code']],
            [['prod_fab_task_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionFabTasks::className(), 'targetAttribute' => ['prod_fab_task_id' => 'id']],
            [['fab_task_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjProdTaskFab::className(), 'targetAttribute' => ['fab_task_code' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'prod_fab_task_id' => 'Prod Fab Task ID',
            'fab_task_code' => 'Fab Task Code',
            'qty_partial_change' => 'Qty Partial Change',
            'qty_complete_change' => 'Qty Complete Change',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ProdFabTask]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdFabTask()
    {
        return $this->hasOne(ProductionFabTasks::className(), ['id' => 'prod_fab_task_id']);
    }

    /**
     * Gets query for [[FabTaskCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFabTaskCode()
    {
        return $this->hasOne(RefProjProdTaskFab::className(), ['code' => 'fab_task_code']);
    }
}
