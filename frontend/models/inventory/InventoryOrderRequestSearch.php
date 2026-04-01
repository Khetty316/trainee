<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\InventoryOrderRequest;
use yii;

/**
 * InventoryOrderRequestSearch represents the model behind the search form of `frontend\models\inventory\InventoryOrderRequest`.
 */
class InventoryOrderRequestSearch extends InventoryOrderRequest {

    public $inventory_brand_id;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['order_qty', 'inventory_detail_id', 'required_qty', 'received_qty', 'pending_qty', 'status', 'inventory_po_item_id', 'reference_id', 'inventory_brand_id', 'reference_type', 'requested_at', 'updated_at', 'inventory_model_id', 'requested_by', 'updated_by'], 'safe'],
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
        $query = InventoryOrderRequest::find();

        $query->joinWith([
            'inventoryModel.inventoryBrand',
            'requestedBy',
            'updatedBy',
            'inventoryDetail.supplier'
        ]);

        // add conditions that should always apply here
        switch ($type) {
            case "projcoor":
                $query->andWhere([
                    'in',
                    'inventory_order_request.reference_type',
                    ['bom_detail', 'bomstockoutbound', 'reserve']
                ])->andWhere([
                    'inventory_order_request.requested_by' => Yii::$app->user->identity->id
                ]);
                break;

            case "maintenanceHeadPending":
                $query->andWhere([
                    'in',
                    'inventory_order_request.reference_type',
                    ['cm', 'pm', 'reserve']
                ])->andWhere([
                    'inventory_order_request.requested_by' => Yii::$app->user->identity->id
                ])->andWhere(['inventory_order_request.status' => 0]);
                break;
            
            case "maintenanceHeadAll":
                $query->andWhere([
                    'in',
                    'inventory_order_request.reference_type',
                    ['cm', 'pm', 'reserve']
                ])->andWhere([
                    'inventory_order_request.requested_by' => Yii::$app->user->identity->id
                ]);
                break;

            case "execPending":
                $query->andWhere(['inventory_order_request.status' => 0]);
                break;

            case "assistPending":
                $query->andWhere(['inventory_order_request.status' => 0]);
                break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'required_qty' => $this->required_qty,
            'received_qty' => $this->received_qty,
            'pending_qty' => $this->pending_qty,
            'inventory_order_request.status' => $this->status,
            'inventory_po_item_id' => $this->inventory_po_item_id,
            'requested_at' => $this->requested_at,
            'updated_at' => $this->updated_at,
            'order_qty' => $this->order_qty,
            'reference_type' => $this->reference_type,
        ]);

        $query->andFilterWhere(['like', 'inventory_model.type', $this->inventory_model_id])
                ->andFilterWhere(['like', 'inventory_brand.name', $this->inventory_brand_id])
                ->andFilterWhere(['like', 'user.fullname', $this->requested_by])
                ->andFilterWhere(['like', 'user.fullname', $this->updated_by])
                ->andFilterWhere(['like', 'inventory_supplier.name', $this->inventory_detail_id]);

        // Handle reference_id filter - search based on what is DISPLAYED in the view
        if (!empty($this->reference_id)) {
            $searchTerm = $this->reference_id;

            // Build the OR condition for reference_id search based on display values
            $referenceCondition = ['or'];

            // 1. Search in project production panels (for bom_detail and bomstockoutbound)
            $projectPanelCondition = ['or'];

            // Get IDs from bom_detail path
            $bomDetailIds = \frontend\models\bom\BomDetails::find()
                    ->alias('bd')
                    ->select('bd.id')
                    ->leftJoin('bom_master bm', 'bm.id = bd.bom_master')
                    ->leftJoin('project_production_panels ppp', 'ppp.id = bm.production_panel_id')
                    ->where(['like', 'ppp.project_production_panel_code', $searchTerm])
                    ->column();

            // Get IDs from stock outbound path
            $stockOutboundIds = \frontend\models\bom\StockOutboundDetails::find()
                    ->alias('sod')
                    ->select('sod.id')
                    ->leftJoin('bom_details bd', 'bd.id = sod.bom_detail_id')
                    ->leftJoin('bom_master bm', 'bm.id = bd.bom_master')
                    ->leftJoin('project_production_panels ppp', 'ppp.id = bm.production_panel_id')
                    ->where(['like', 'ppp.project_production_panel_code', $searchTerm])
                    ->column();

            if (!empty($bomDetailIds) || !empty($stockOutboundIds)) {
                $allIds = array_merge($bomDetailIds, $stockOutboundIds);
                $referenceCondition[] = [
                    'and',
                    ['inventory_order_request.reference_type' => ['bom_detail', 'bomstockoutbound']],
                    ['inventory_order_request.reference_id' => $allIds]
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
                    ['inventory_order_request.reference_type' => 'reserve'],
                    ['inventory_order_request.reference_id' => $userIds]
                ];
            }

            // Apply the reference condition if any matches found
            if (count($referenceCondition) > 1) {
                $query->andWhere($referenceCondition);
            } else {
                // No matches found - return no results
                $query->andWhere('0=1');
            }
        }

        $dataProvider->setSort([
            'attributes' => array_merge($dataProvider->sort->attributes, [
                'inventory_brand_id' => [
                    'asc' => ['inventory_brand.name' => SORT_ASC],
                    'desc' => ['inventory_brand.name' => SORT_DESC],
                    'label' => 'Brand Name',
                ]
            ]),
            'defaultOrder' => [
                'id' => SORT_DESC
            ]
        ]);

        return $dataProvider;
    }
}
