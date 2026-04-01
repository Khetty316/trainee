<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\VInventoryDetail;

/**
 * VInventoryDetailSearch represents the model behind the search form of `frontend\models\inventory\VInventoryDetail`.
 */
class VInventoryDetailSearch extends VInventoryDetail {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['inventory_id', 'supplier_id', 'brand_id', 'model_id', 'minimum_qty', 'stock_level_sts', 'stock_on_hand', 'required_qty', 'reorder_qty', 'qty_pending_receipt', 'active_sts'], 'integer'],
            [['stock_in', 'stock_reserved', 'stock_out', 'stock_available', 'supplier_display', 'brand_display', 'group', 'inventory_code', 'department_code', 'department_name', 'supplier_code', 'supplier_name', 'supplier_contact_name', 'supplier_contact_number', 'supplier_contact_email', 'supplier_contact_fax', 'supplier_agent_terms', 'brand_code', 'brand_name', 'model_type', 'model_description', 'unit_type', 'image', 'created_at', 'updated_at', 'created_by_fullname', 'updated_by_fullname'], 'safe'],
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
    public function search($params, $type = "") {
        $query = VInventoryDetail::find();

        // add conditions that should always apply here
        switch ($type) {
            case "itemsToReorder":
                $query->where(['active_sts' => 2]);
                break;
            default:
                // Handle unknown type or no filter
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
            'inventory_id' => $this->inventory_id,
            'supplier_id' => $this->supplier_id,
            'brand_id' => $this->brand_id,
            'model_id' => $this->model_id,
            'minimum_qty' => $this->minimum_qty,
            'stock_level_sts' => $this->stock_level_sts,
            'stock_on_hand' => $this->stock_on_hand,
            'stock_in' => $this->stock_in,
            'stock_reserved' => $this->stock_reserved,
            'stock_out' => $this->stock_out,
            'stock_available' => $this->stock_available,
//            'required_qty' => $this->required_qty,
//            'reorder_qty' => $this->reorder_qty,
            'qty_pending_receipt' => $this->qty_pending_receipt,
            'active_sts' => $this->active_sts,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'inventory_code', $this->inventory_code])
                ->andFilterWhere(['like', 'department_code', $this->department_code])
                ->andFilterWhere(['like', 'department_name', $this->department_name])
                 ->andFilterWhere(['like', 'supplier_display', $this->supplier_display])
                ->andFilterWhere(['like', 'supplier_code', $this->supplier_code])
                ->andFilterWhere(['like', 'supplier_name', $this->supplier_name])
                ->andFilterWhere(['like', 'supplier_contact_name', $this->supplier_contact_name])
                ->andFilterWhere(['like', 'supplier_contact_number', $this->supplier_contact_number])
                ->andFilterWhere(['like', 'supplier_contact_email', $this->supplier_contact_email])
                ->andFilterWhere(['like', 'supplier_contact_fax', $this->supplier_contact_fax])
                ->andFilterWhere(['like', 'supplier_agent_terms', $this->supplier_agent_terms])
                ->andFilterWhere(['like', 'brand_display', $this->brand_display])
                ->andFilterWhere(['like', 'brand_code', $this->brand_code])
                ->andFilterWhere(['like', 'brand_name', $this->brand_name])
                ->andFilterWhere(['like', 'model_type', $this->model_type])
                ->andFilterWhere(['like', 'model_description', $this->model_description])
                ->andFilterWhere(['like', 'group', $this->group])
                ->andFilterWhere(['like', 'unit_type', $this->unit_type])
                ->andFilterWhere(['like', 'image', $this->image])
                ->andFilterWhere(['like', 'created_by_fullname', $this->created_by_fullname])
                ->andFilterWhere(['like', 'updated_by_fullname', $this->updated_by_fullname]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'inventory_id' => SORT_DESC
            ],
        ]);
        
        return $dataProvider;
    }
}
