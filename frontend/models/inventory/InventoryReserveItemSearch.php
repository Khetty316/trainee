<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\InventoryReserveItem;

/**
 * InventoryReserveItemSearch represents the model behind the search form of `frontend\models\inventory\InventoryReserveItem`.
 */
class InventoryReserveItemSearch extends InventoryReserveItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'inventory_detail_id', 'reserved_qty', 'dispatched_qty', 'available_qty', 'created_by', 'updated_by', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
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
        $query = InventoryReserveItem::find();

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
            'user_id' => $this->user_id,
            'inventory_detail_id' => $this->inventory_detail_id,
            'reserved_qty' => $this->reserved_qty,
            'dispatched_qty' => $this->dispatched_qty,
            'available_qty' => $this->available_qty,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
