<?php

namespace frontend\models\inventory\cmms;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\cmms\InventoryDetailCmms;

/**
 * InventoryDetailCmmsSearch represents the model behind the search form of `frontend\models\inventory\cmms\InventoryDetailCmms`.
 */
class InventoryDetailCmmsSearch extends InventoryDetailCmms {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'stock_level_min', 'stock_level_sts', 'quantity_stock', 'quantity_required', 'quantity_pending_arrival', 'active_sts'], 'integer'],
            [['supplier_cmms_code', 'brand_cmms_code', 'model_cmms_code', 'supplier_cmms_name', 'brand_cmms_name', 'model_cmms_name', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
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
        $query = InventoryDetailCmms::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user", "inventory_detail_cmms.created_by = user.id");

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'stock_level_min' => $this->stock_level_min,
            'stock_level_sts' => $this->stock_level_sts,
            'quantity_stock' => $this->quantity_stock,
            'quantity_required' => $this->quantity_required,
            'quantity_pending_arrival' => $this->quantity_pending_arrival,
            'active_sts' => $this->active_sts,
//            'created_by' => $this->created_by,
//            'created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'supplier_cmms_code', $this->supplier_cmms_code])
                ->andFilterWhere(['like', 'brand_cmms_code', $this->brand_cmms_code])
                ->andFilterWhere(['like', 'model_cmms_code', $this->model_cmms_code])
                ->andFilterWhere(['like', 'supplier_cmms_name', $this->supplier_cmms_name])
                ->andFilterWhere(['like', 'brand_cmms_name', $this->brand_cmms_name])
                ->andFilterWhere(['like', 'model_cmms_name', $this->model_cmms_name])
                ->andFilterWhere(['like', 'user.fullname', $this->created_by])
                ->andFilterWhere(['like', 'user.fullname', $this->updated_by]);

        return $dataProvider;
    }
}
