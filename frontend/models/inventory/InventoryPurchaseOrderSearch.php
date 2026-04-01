<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\InventoryPurchaseOrder;
use Yii;

/**
 * InventoryPurchaseOrderSearch represents the model behind the search form of `frontend\models\inventory\InventoryPurchaseOrder`.
 */
class InventoryPurchaseOrderSearch extends InventoryPurchaseOrder {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'status', 'currency_id', 'total_qty', 'active_sts'], 'integer'],
            [['po_no', 'po_date', 'company_group', 'comment', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
            [['total_amount', 'total_discount', 'net_amount', 'tax_amount', 'gross_amount'], 'number'],
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
        $query = InventoryPurchaseOrder::find();

        // add conditions that should always apply here
        switch ($type) {
            case "execPendingPurchasing":
                $query->where(['inventory_purchase_order.status' => \frontend\models\RefInventoryStatus::STATUS_PoCreated, 'active_sts' => 2]);
                break;
            case "execPendingReceiving":
                $query->where(['inventory_purchase_order.status' => \frontend\models\RefInventoryStatus::STATUS_PendingPo, 'active_sts' => 2]);
                break;
            case "assistPendingPurchasing":
                $query->where(['inventory_purchase_order.status' => \frontend\models\RefInventoryStatus::STATUS_PoCreated, 'active_sts' => 2]);
                break;
            case "assistPendingReceiving":
                $query->where(['inventory_purchase_order.status' => \frontend\models\RefInventoryStatus::STATUS_PendingPo, 'active_sts' => 2]);
                break;
            case "maintenanceHeadPendingPurchasing":
                $query->where(['inventory_purchase_order.status' => \frontend\models\RefInventoryStatus::STATUS_PoCreated, 'active_sts' => 2, 'inventory_purchase_order.created_by' => Yii::$app->user->identity->id]);
                break;
            case "maintenanceHeadAllPurchasing":
                $query->where(['active_sts' => 2, 'inventory_purchase_order.created_by' => Yii::$app->user->identity->id]);
                break;
            case "maintenanceHeadPendingReceiving":
                $query->where(['inventory_purchase_order.status' => \frontend\models\RefInventoryStatus::STATUS_PendingPo, 'active_sts' => 2, 'inventory_purchase_order.created_by' => Yii::$app->user->identity->id]);
                break;
            case "maintenanceHeadAllReceiving":
                $query->where(['active_sts' => 2, 'inventory_purchase_order.created_by' => Yii::$app->user->identity->id]);
                break;
            default:
                // Handle unknown type or no filter
                break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user a", "inventory_purchase_order.created_by = a.id");
        $query->join("LEFT JOIN", "user b", "inventory_purchase_order.updated_by = b.id");
        $query->join("LEFT JOIN", "ref_currencies c", "inventory_purchase_order.currency_id = c.currency_id");

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
//            'po_date' => $this->po_date,
            'inventory_purchase_order.status' => $this->status,
            'inventory_purchase_order.active_sts' => $this->active_sts,
            'inventory_purchase_order.currency_id' => $this->currency_id,
            'total_qty' => $this->total_qty,
            'total_amount' => $this->total_amount,
            'total_discount' => $this->total_discount,
            'net_amount' => $this->net_amount,
            'tax_amount' => $this->tax_amount,
            'gross_amount' => $this->gross_amount,
//            'created_by' => $this->created_by,
//            'created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'po_no', $this->po_no])
                ->andFilterWhere(['like', 'company_group', $this->company_group])
                ->andFilterWhere(['like', 'comment', $this->comment])
                ->andFilterWhere(['like', 'a.fullname', $this->created_by])
                ->andFilterWhere(['like', 'b.fullname', $this->updated_by]);

        if (!empty($this->po_date)) {
            $poDate = \DateTime::createFromFormat('d/m/Y', $this->po_date);
            if ($poDate !== false) {
                $query->andFilterWhere(['po_date' => $poDate->format('Y-m-d')]);
            }
        }

        if (!empty($this->created_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->created_at);
            if ($date) {
                $query->andFilterWhere(['between',
                    'inventory_purchase_order.created_at',
                    $date->format('Y-m-d 00:00:00'),
                    $date->format('Y-m-d 23:59:59'),
                ]);
            }
        }

        if (!empty($this->updated_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->updated_at);
            if ($date) {
                $query->andFilterWhere(['between',
                    'inventory_purchase_order.updated_at',
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
