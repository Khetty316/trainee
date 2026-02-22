<?php

namespace frontend\models\ProjectProduction\electrical;

use Yii;

/**
 * This is the model class for table "production_elec_task_trail".
 *
 * @property int $id
 * @property int $prod_elec_task_id
 * @property string $elec_task_code
 * @property int|null $qty_partial_change
 * @property int|null $qty_complete_change
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProductionElecTasks $prodElecTask
 * @property RefProjProdTaskElec $elecTaskCode
 */
class ProductionElecTaskTrail extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'production_elec_task_trail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['prod_elec_task_id', 'elec_task_code'], 'required'],
            [['prod_elec_task_id', 'qty_partial_change', 'qty_complete_change', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['elec_task_code'], 'string', 'max' => 10],
            [['prod_elec_task_id', 'elec_task_code'], 'unique', 'targetAttribute' => ['prod_elec_task_id', 'elec_task_code']],
            [['prod_elec_task_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionElecTasks::className(), 'targetAttribute' => ['prod_elec_task_id' => 'id']],
            [['elec_task_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjProdTaskElec::className(), 'targetAttribute' => ['elec_task_code' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'prod_elec_task_id' => 'Prod Elec Task ID',
            'elec_task_code' => 'Elec Task Code',
            'qty_partial_change' => 'Qty Partial Change',
            'qty_complete_change' => 'Qty Complete Change',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ProdElecTask]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdElecTask() {
        return $this->hasOne(ProductionElecTasks::className(), ['id' => 'prod_elec_task_id']);
    }

    /**
     * Gets query for [[ElecTaskCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getElecTaskCode() {
        return $this->hasOne(RefProjProdTaskElec::className(), ['code' => 'elec_task_code']);
    }

}
