<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\VInventoryModel;

/**
 * VInventoryModelSearch represents the model behind the search form of `frontend\models\inventory\VInventoryModel`.
 */
class VInventoryModelSearch extends VInventoryModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'active_sts', 'inventory_brand_id', 'minimum_qty', 'stock_level_sts', 'created_by', 'updated_by'], 'integer'],
            [['departments', 'type', 'group', 'description', 'unit_type', 'image', 'brand_name', 'brand_model', 'created_by_name', 'created_at', 'updated_by_name', 'updated_at'], 'safe'],
            [['total_stock_on_hand', 'total_stock_reserved', 'total_stock_available'], 'number'],
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
        $query = VInventoryModel::find();

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
            'active_sts' => $this->active_sts,
            'inventory_brand_id' => $this->inventory_brand_id,
            'total_stock_on_hand' => $this->total_stock_on_hand,
            'total_stock_reserved' => $this->total_stock_reserved,
            'total_stock_available' => $this->total_stock_available,
            'minimum_qty' => $this->minimum_qty,
            'stock_level_sts' => $this->stock_level_sts,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'departments', $this->departments])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'group', $this->group])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'unit_type', $this->unit_type])
            ->andFilterWhere(['like', 'image', $this->image])
            ->andFilterWhere(['like', 'brand_name', $this->brand_name])
            ->andFilterWhere(['like', 'brand_model', $this->brand_model])
            ->andFilterWhere(['like', 'created_by_name', $this->created_by_name])
            ->andFilterWhere(['like', 'updated_by_name', $this->updated_by_name]);

        return $dataProvider;
    }
}
