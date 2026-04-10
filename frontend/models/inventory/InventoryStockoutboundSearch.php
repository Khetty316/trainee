<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\InventoryStockoutbound;

/**
 * InventoryStockoutboundSearch represents the model behind the search form of `frontend\models\inventory\InventoryStockoutbound`.
 */
class InventoryStockoutboundSearch extends InventoryStockoutbound {

    public $inventory_brand_id;
    public $inventory_model_id;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'qty', 'dispatched_qty', 'reserve_item_id'], 'integer'],
            [['created_by', 'updated_by', 'inventory_detail_id', 'reference_id', 'inventory_model_id', 'inventory_brand_id', 'reference_type', 'created_at', 'updated_at'], 'safe'],
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
    public function search($params) {
        $query = InventoryStockoutbound::find();
        $query->joinWith([
            'inventoryDetail.model',
            'inventoryDetail.brand',
//            'createdBy',
//            'updatedBy',
            'inventoryDetail.supplier',
        ]);

        // add conditions that should always apply here

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
//            'inventory_detail_id' => $this->inventory_detail_id,
//            'reference_id' => $this->reference_id,
            'qty' => $this->qty,
            'dispatched_qty' => $this->dispatched_qty,
//            'created_by' => $this->created_by,
//            'created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'updated_at' => $this->updated_at,
//            'reserve_item_id' => $this->reserve_item_id,
        ]);

        $query->andFilterWhere(['like', 'reference_type', $this->reference_type])
                ->andFilterWhere(['like', 'inventory_model.type', $this->inventory_model_id])
                ->andFilterWhere(['like', 'inventory_brand.name', $this->inventory_brand_id])
                ->andFilterWhere(['like', 'user.fullname', $this->created_by])
                ->andFilterWhere(['like', 'inventory_supplier.name', $this->inventory_detail_id]);

        if (!empty($this->created_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->created_at);
            if ($date) {
                $query->andWhere(['between', 'inventory_stockoutbound.created_at',
                    $date->format('Y-m-d 00:00:00'),
                    $date->format('Y-m-d 23:59:59')
                ]);
            }
        }

        if (!empty($this->updated_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->updated_at);
            if ($date) {
                $query->andWhere(['between', 'inventory_stockoutbound.updated_at',
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

            // Get IDs from stock outbound path
            $stockOutboundIds = \frontend\models\bom\StockOutboundDetails::find()
                    ->alias('sod')
                    ->select('sod.id')
                    ->leftJoin('bom_details bd', 'bd.id = sod.bom_detail_id')
                    ->leftJoin('bom_master bm', 'bm.id = bd.bom_master')
                    ->leftJoin('project_production_panels ppp', 'ppp.id = bm.production_panel_id')
                    ->where(['like', 'ppp.project_production_panel_code', $searchTerm])
                    ->column();

            if (!empty($stockOutboundIds)) {
                $referenceCondition[] = [
                    'and',
                    ['inventory_stockoutbound.reference_type' => ['bom_detail', 'bomstockoutbound']],
                    ['inventory_stockoutbound.reference_id' => $stockOutboundIds]
                ];
            }

            // 2. Search in user table (for reserve type)
            $userIds = \common\models\User::find()
                    ->select('id')
                    ->where(['like', 'fullname', $searchTerm])
                    ->orWhere(['like', 'username', $searchTerm])
                    ->orWhere(['like', 'email', $searchTerm])
                    ->column();

            if (!empty($userIds)) {
                $referenceCondition[] = [
                    'and',
                    ['inventory_stockoutbound.reference_type' => 'reserve'],
                    ['inventory_stockoutbound.reference_id' => $userIds]
                ];
            }

            // 3. Search in material request table
            $materialRequestIds = InventoryMaterialRequest::find()
                    ->select('id')
                    ->where(['like', 'reference_id', $searchTerm])
                    ->column();

            if (!empty($materialRequestIds)) {
                $referenceCondition[] = [
                    'and',
                    ['inventory_stockoutbound.reference_type' => 'materialrequest'],
                    ['inventory_stockoutbound.reference_id' => $materialRequestIds]
                ];
            }

            // Apply the reference condition if any matches found
            if (count($referenceCondition) > 1) {
                $query->andWhere($referenceCondition);
            } else {
                // No matches found - return no results
//                $query->andWhere('0=1');
                $query->andWhere(['inventory_stockoutbound.reference_id' => $searchTerm]);
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
