<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\InventoryMaterialRequest;
use yii;

/**
 * InventoryMaterialRequestSearch represents the model behind the search form of `frontend\models\inventory\InventoryMaterialRequest`.
 */
class InventoryMaterialRequestSearch extends InventoryMaterialRequest {

    public $inventory_brand_id;
    public $inventory_model_id;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'request_qty', 'approved_qty', 'created_by', 'updated_by', 'approved_by', 'status'], 'integer'],
            [['inventory_detail_id', 'user_id', 'reference_id', 'desc', 'inventory_model_id', 'inventory_brand_id', 'reference_type', 'created_at', 'updated_at', 'approved_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $type) {
        $query = InventoryMaterialRequest::find();
        $query->joinWith([
            'inventoryDetail.model',
            'inventoryDetail.brand',
//            'createdBy',
//            'updatedBy',
            'inventoryDetail.supplier',
            'user'
        ]);

        // add conditions that should always apply here
        switch ($type) {
            case "maintenanceHeadStock":
                $query->andWhere([
                    'in',
                    'inventory_material_request.reference_type',
                    ['2', '3']]);
                break;

            case "personalStock":
                $query->andWhere([
                    'or',
                    ['inventory_material_request.created_by' => Yii::$app->user->identity->id],
                    ['inventory_material_request.user_id' => Yii::$app->user->identity->id],
                ]);
                break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
//            'user_id' => $this->user_id,
//            'reference_id' => $this->reference_id,
//            'inventory_detail_id' => $this->inventory_detail_id,
            'request_qty' => $this->request_qty,
            'approved_qty' => $this->approved_qty,
//            'created_by' => $this->created_by,
//            'created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'updated_at' => $this->updated_at,
//            'approved_by' => $this->approved_by,
//            'approved_at' => $this->approved_at,
            'inventory_material_request.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'reference_type', $this->reference_type])
                ->andFilterWhere(['like', 'desc', $this->desc])
                ->andFilterWhere(['like', 'inventory_model.type', $this->inventory_model_id])
                ->andFilterWhere(['like', 'inventory_brand.name', $this->inventory_brand_id])
                ->andFilterWhere(['like', 'user.fullname', $this->created_by])
                ->andFilterWhere(['like', 'user.fullname', $this->user_id])
                ->andFilterWhere(['like', 'inventory_supplier.name', $this->inventory_detail_id]);

        if (!empty($this->created_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->created_at);
            if ($date) {
                $query->andWhere(['between', 'inventory_material_request.created_at',
                    $date->format('Y-m-d 00:00:00'),
                    $date->format('Y-m-d 23:59:59')
                ]);
            }
        }

        if (!empty($this->updated_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->updated_at);
            if ($date) {
                $query->andWhere(['between', 'inventory_material_request.updated_at',
                    $date->format('Y-m-d 00:00:00'),
                    $date->format('Y-m-d 23:59:59')
                ]);
            }
        }

        if (!empty($this->approved_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->approved_at);
            if ($date) {
                $query->andWhere(['between', 'inventory_material_request.approved_at',
                    $date->format('Y-m-d 00:00:00'),
                    $date->format('Y-m-d 23:59:59')
                ]);
            }
        }

        // Handle reference_id filter - search based on what is DISPLAYED in the view
        if (!empty($this->reference_id)) {
            $searchTerm = $this->reference_id;

            // Build the OR condition for reference_id search based on display values
            $referenceCondition = ['or'];

            // 1. Search in project production panels (for bom_detail and bomstockoutbound)
            $projectPanelCondition = ['or'];

            // 1. Get IDs from project path
            $project = \frontend\models\ProjectProduction\ProjectProductionPanels::find()
                    ->alias('bd')
                    ->select('bd.id')
                    ->where(['like', 'bd.project_production_panel_code', $searchTerm])
                    ->column();

            if (!empty($project)) {
                $referenceCondition[] = [
                    'and',
                    ['inventory_material_request.reference_type' => ['1']],
                    ['inventory_material_request.reference_id' => $project]
                ];
            }

            // 2. Get IDs from maintrnance path (CM)
            $cm = \frontend\models\cmms\CmmsCorrectiveWorkOrderMaster::find()
                    ->alias('bd')
                    ->select('bd.id')
                    ->where(['like', 'bd.id', $searchTerm])
                    ->column();

            if (!empty($cm)) {
                $referenceCondition[] = [
                    'and',
                    ['inventory_material_request.reference_type' => ['2']],
                    ['inventory_material_request.reference_id' => $cm]
                ];
            }

            // 3. Get IDs from maintrnance path (PM)
            $pm = \frontend\models\cmms\CmmsPreventiveWorkOrderMaster::find()
                    ->alias('bd')
                    ->select('bd.id')
                    ->where(['like', 'bd.id', $searchTerm])
                    ->column();

            if (!empty($pm)) {
                $referenceCondition[] = [
                    'and',
                    ['inventory_material_request.reference_type' => ['3']],
                    ['inventory_material_request.reference_id' => $pm]
                ];
            }

            // Apply the reference condition if any matches found
            if (count($referenceCondition) > 1) {
                $query->andWhere($referenceCondition);
            } else {
                $query->andWhere(['inventory_material_request.reference_id' => $searchTerm]);
            }
        }

        $dataProvider->setSort([
            'attributes' => array_merge($dataProvider->sort->attributes, [
                'inventory_brand_id' => [
                    'asc' => ['inventory_brand.name' => SORT_ASC],
                    'desc' => ['inventory_brand.name' => SORT_DESC],
                ],
                'inventory_model_id' => [
                    'asc' => ['inventory_model.type' => SORT_ASC],
                    'desc' => ['inventory_model.type' => SORT_DESC],
                ]
            ]),
            'defaultOrder' => [
                'id' => SORT_DESC
            ]
        ]);

        return $dataProvider;
    }
}
