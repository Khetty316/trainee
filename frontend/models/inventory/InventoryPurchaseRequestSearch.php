<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\InventoryPurchaseRequest;

/**
 * InventoryPurchaseRequestSearch represents the model behind the search form of `frontend\models\inventory\InventoryPurchaseRequest`.
 */
class InventoryPurchaseRequestSearch extends InventoryPurchaseRequest {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'inventory_supplier_id', 'source_type', 'source_id', 'po_status'], 'integer'],
            [['quotation_no', 'quotation_date', 'quotation_filename', 'created_at', 'updated_at', 'uploaded_at', 'created_by', 'updated_by', 'uploaded_by'], 'safe'],
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
        $query = InventoryPurchaseRequest::find();

        // add conditions that should always apply here
        switch ($type) {
            case "pendingPONewItem":
                $query->where(['source_type' => 1])
                        ->andWhere(['po_status' => 1]);
                break;
            case "pendingPOReorderItem":
                $query->where(['source_type' => 2])
                        ->andWhere(['po_status' => 1]);
                break;
            default:
                // Handle unknown type or no filter
                break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user a", "inventory_purchase_request.created_by = a.id");
        $query->join("LEFT JOIN", "user b", "inventory_purchase_request.updated_by = b.id");
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'inventory_supplier_id' => $this->inventory_supplier_id,
            'source_type' => $this->source_type,
            'source_id' => $this->source_id,
            'quotation_date' => $this->quotation_date,
            'po_status' => $this->po_status,
//            'created_by' => $this->created_by,
//            'created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'updated_at' => $this->updated_at,
//            'uploaded_by' => $this->uploaded_by,
//            'uploaded_at' => $this->uploaded_at,
        ]);

        $query->andFilterWhere(['like', 'quotation_no', $this->quotation_no])
                ->andFilterWhere(['like', 'quotation_filename', $this->quotation_filename])
                ->andFilterWhere(['like', 'a.fullname', $this->created_by])
                ->andFilterWhere(['like', 'b.fullname', $this->updated_by]);

          if (!empty($this->created_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->created_at);
            if ($date) {
                $query->andFilterWhere(['between',
                    'inventory_purchase_request.created_at',
                    $date->format('Y-m-d 00:00:00'),
                    $date->format('Y-m-d 23:59:59'),
                ]);
            }
        }

        if (!empty($this->updated_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->updated_at);
            if ($date) {
                $query->andFilterWhere(['between',
                    'inventory_purchase_request.updated_at',
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
