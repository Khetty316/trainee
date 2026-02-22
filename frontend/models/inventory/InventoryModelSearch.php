<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\InventoryModel;

/**
 * InventoryModelSearch represents the model behind the search form of `frontend\models\inventory\InventoryModel`.
 */
class InventoryModelSearch extends InventoryModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'active_sts'], 'integer'],
            [['total_stock_on_hand', 'total_stock_reserved', 'total_stock_available', 'type', 'group', 'description', 'unit_type', 'image', 'created_at', 'updated_at', 'created_by', 'updated_by', 'inventory_brand_id'], 'safe'],
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
        $query = InventoryModel::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user", "inventory_model.created_by = user.id");
        $query->join("LEFT JOIN", "inventory_brand", "inventory_model.inventory_brand_id = inventory_brand.id");

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
//            'created_by' => $this->created_by,
//            'created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'group', $this->group])
                ->andFilterWhere(['like', 'unit_type', $this->unit_type])
                ->andFilterWhere(['like', 'image', $this->image])
                ->andFilterWhere(['like', 'total_stock_on_hand', $this->total_stock_on_hand])
                ->andFilterWhere(['like', 'total_stock_reserved', $this->total_stock_reserved])
                ->andFilterWhere(['like', 'total_stock_available', $this->total_stock_available])
                ->andFilterWhere(['like', 'user.fullname', $this->created_by])
                ->andFilterWhere(['like', 'user.fullname', $this->updated_by])
                ->andFilterWhere(['like', 'inventory_brand.name', $this->inventory_brand_id]);

        return $dataProvider;
    }
}
