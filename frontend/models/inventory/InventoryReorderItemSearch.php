<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\InventoryReorderItem;

/**
 * InventoryReorderItemSearch represents the model behind the search form of `frontend\models\inventory\InventoryReorderItem`.
 */
class InventoryReorderItemSearch extends InventoryReorderItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'inventory_reorder_master_id', 'inventory_detail_id', 'prereq_form_item_id', 'order_qty', 'received_qty', 'remaining_qty', 'receipt_status', 'created_by', 'updated_by'], 'integer'],
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
        $query = InventoryReorderItem::find();

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
            'inventory_reorder_master_id' => $this->inventory_reorder_master_id,
            'inventory_detail_id' => $this->inventory_detail_id,
            'prereq_form_item_id' => $this->prereq_form_item_id,
            'order_qty' => $this->order_qty,
            'received_qty' => $this->received_qty,
            'remaining_qty' => $this->remaining_qty,
            'receipt_status' => $this->receipt_status,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
