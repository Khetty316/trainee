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
            [['id', 'inventory_detail_id', 'required_qty', 'received_qty', 'pending_qty', 'status', 'inventory_po_item_id'], 'integer'],
            [['reference_id', 'inventory_brand_id', 'reference_type', 'requested_at', 'updated_at', 'inventory_model_id', 'requested_by', 'updated_by'], 'safe'],
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
            'bomDetail.bomMaster.productionPanel',
            'stockOutbound.bomDetail.bomMaster.productionPanel',
        ]);
        //
        // add conditions that should always apply here
        switch ($type) {
            case "projcoor":
                $query->where(['inventory_order_request.requested_by' => Yii::$app->user->identity->id]);
                break;

            case "execPending":
                $query->where(['inventory_order_request.status' => 0]);
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
            'inventory_detail_id' => $this->inventory_detail_id,
//            'inventory_model_id' => $this->inventory_model_id,
//            'reference_id' => $this->reference_id,
            'required_qty' => $this->required_qty,
            'received_qty' => $this->received_qty,
            'pending_qty' => $this->pending_qty,
            'inventory_order_request.status' => $this->status,
            'inventory_po_item_id' => $this->inventory_po_item_id,
            'requested_at' => $this->requested_at,
//            'requested_by' => $this->requested_by,
            'updated_at' => $this->updated_at,
//            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'inventory_model.type', $this->inventory_model_id])
                ->andFilterWhere(['like', 'inventory_brand.name', $this->inventory_brand_id])
                ->andFilterWhere(['like', 'user.fullname', $this->requested_by])
                ->andFilterWhere(['like', 'user.fullname', $this->updated_by]);

        if (!empty($this->reference_id)) {

            $query->andWhere([
                'or',
                // From bom_detail path
                [
                    'and',
                    ['inventory_order_request.reference_type' => 'bom_detail'],
                    ['like', 'project_production_panels.project_production_panel_code', $this->reference_id]
                ],
                // From stock outbound path
                [
                    'and',
                    ['inventory_order_request.reference_type' => 'bomstockoutbound'],
                    ['like', 'project_production_panels.project_production_panel_code', $this->reference_id]
                ],
                // For other types (no project panel)
                [
                    'and',
                    ['not in', 'inventory_order_request.reference_type', ['bom_detail', 'bomstockoutbound']],
                    ['like', 'inventory_order_request.reference_id', $this->reference_id]
                ],
            ]);
        }

        return $dataProvider;
    }
}
