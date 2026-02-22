<?php

namespace frontend\models\inventory\cmms;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\cmms\VInventoryDetailCmms;

/**
 * VInventoryDetailCmmsSearch represents the model behind the search form of `frontend\models\inventory\cmms\VInventoryDetailCmms`.
 */
class VInventoryDetailCmmsSearch extends VInventoryDetailCmms
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inventory_id', 'supplier_id', 'brand_id', 'model_id', 'stock_level_min', 'stock_level_sts', 'stock_on_hand', 'required_quantity', 'reorder_quantity', 'pending_quantity', 'active_sts'], 'integer'],
            [['supplier_code', 'supplier_name', 'supplier_contact_name', 'supplier_contact_number', 'supplier_contact_email', 'supplier_contact_fax', 'supplier_agent_terms', 'brand_code', 'brand_name', 'model_code', 'model_description', 'unit_type', 'image', 'created_at', 'updated_at', 'created_by_fullname', 'updated_by_fullname'], 'safe'],
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
    public function search($params)
    {
        $query = VInventoryDetailCmms::find();

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
            'inventory_id' => $this->inventory_id,
            'supplier_id' => $this->supplier_id,
            'brand_id' => $this->brand_id,
            'model_id' => $this->model_id,
            'stock_level_min' => $this->stock_level_min,
            'stock_level_sts' => $this->stock_level_sts,
            'stock_on_hand' => $this->stock_on_hand,
            'required_quantity' => $this->required_quantity,
            'reorder_quantity' => $this->reorder_quantity,
            'pending_quantity' => $this->pending_quantity,
            'active_sts' => $this->active_sts,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'supplier_code', $this->supplier_code])
            ->andFilterWhere(['like', 'supplier_name', $this->supplier_name])
            ->andFilterWhere(['like', 'supplier_contact_name', $this->supplier_contact_name])
            ->andFilterWhere(['like', 'supplier_contact_number', $this->supplier_contact_number])
            ->andFilterWhere(['like', 'supplier_contact_email', $this->supplier_contact_email])
            ->andFilterWhere(['like', 'supplier_contact_fax', $this->supplier_contact_fax])
            ->andFilterWhere(['like', 'supplier_agent_terms', $this->supplier_agent_terms])
            ->andFilterWhere(['like', 'brand_code', $this->brand_code])
            ->andFilterWhere(['like', 'brand_name', $this->brand_name])
            ->andFilterWhere(['like', 'model_code', $this->model_code])
            ->andFilterWhere(['like', 'model_description', $this->model_description])
            ->andFilterWhere(['like', 'unit_type', $this->unit_type])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'created_by_fullname', $this->created_by_fullname])
            ->andFilterWhere(['like', 'updated_by_fullname', $this->updated_by_fullname]);

        return $dataProvider;
    }
}
