<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\InventoryDetail;

/**
 * InventoryDetailSearch represents the model behind the search form of `frontend\models\inventory\InventoryDetail`.
 */
class InventoryDetailSearch extends InventoryDetail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'supplier_id', 'brand_id', 'model_id', 'minimum_qty', 'stock_level_sts', 'stock_on_hand', 'required_qty', 'reorder_qty', 'pending_receive_qty', 'active_sts', 'created_by', 'updated_by'], 'integer'],
            [['code', 'department_code', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
        $query = InventoryDetail::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user", "inventory_detail.created_by = user.id");

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'minimum_qty' => $this->minimum_qty,
            'stock_level_sts' => $this->stock_level_sts,
            'stock_on_hand' => $this->stock_on_hand,
            'required_qty' => $this->required_qty,
            'pending_receive_qty' => $this->pending_receive_qty,
            'active_sts' => $this->active_sts,
//            'created_by' => $this->created_by,
//            'created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'supplier_code', $this->supplier_code])
                ->andFilterWhere(['like', 'brand_code', $this->brand_code])
                ->andFilterWhere(['like', 'model_code', $this->model_code])
                ->andFilterWhere(['like', 'supplier_name', $this->supplier_name])
                ->andFilterWhere(['like', 'brand_name', $this->brand_name])
                ->andFilterWhere(['like', 'model_name', $this->model_name])
                ->andFilterWhere(['like', 'user.fullname', $this->created_by])
                ->andFilterWhere(['like', 'user.fullname', $this->updated_by]);

        return $dataProvider;
    }
}
