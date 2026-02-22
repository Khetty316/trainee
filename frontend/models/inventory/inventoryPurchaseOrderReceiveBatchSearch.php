<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\inventoryPurchaseOrderReceiveBatch;

/**
 * inventoryPurchaseOrderReceiveBatchSearch represents the model behind the search form of `frontend\models\inventory\inventoryPurchaseOrderReceiveBatch`.
 */
class inventoryPurchaseOrderReceiveBatchSearch extends inventoryPurchaseOrderReceiveBatch {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['received_at', 'received_by', 'inventory_po_id'], 'safe'],
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
        $query = inventoryPurchaseOrderReceiveBatch::find();
        

        // add conditions that should always apply here
//        switch ($type) {
//            case "byBatch":
//                $query->where(['inventory_purchase_order_receive_batch.inventory_po_id' => $id]);
//                break;
//
//            default:
//                // Handle unknown type or no filter
//                break;
//        }
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user a", "inventory_purchase_order_receive_batch.received_by = a.id");
$query->join("LEFT JOIN", "inventory_purchase_order po", "inventory_purchase_order_receive_batch.inventory_po_id = po.id");
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
//            'inventory_po_id' => $this->inventory_po_id,
//            'received_by' => $this->received_by,
//            'received_at' => $this->received_at,
        ]);

        $query->andFilterWhere(['like', 'a.fullname', $this->received_by])
                ->andFilterWhere(['like', 'po.po_no', $this->inventory_po_id]);

        if (!empty($this->received_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->received_at);
            if ($date) {
                $query->andFilterWhere(['between',
                    'inventory_purchase_order_receive_batch.received_at',
                    $date->format('Y-m-d 00:00:00'),
                    $date->format('Y-m-d 23:59:59'),
                ]);
            }
        }

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
        ]);

        return $dataProvider;
    }
}
