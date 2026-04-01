<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\InventoryReserveItem;
use Yii;

/**
 * InventoryReserveItemSearch represents the model behind the search form of `frontend\models\inventory\InventoryReserveItem`.
 */
class InventoryReserveItemSearch extends InventoryReserveItem {

    public $inventory_brand_id;
    public $inventory_model_id;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['reference_id', 'reference_type', 'inventory_model_id', 'inventory_brand_id', 'created_at', 'updated_at', 'user_id', 'inventory_detail_id', 'reserved_qty', 'dispatched_qty', 'available_qty', 'created_by', 'updated_by', 'status'], 'safe'],
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
    public function search($params, $type) {
        $query = InventoryReserveItem::find();

        // Only join with necessary relations
        $query->joinWith([
            'inventoryDetail.model',
            'inventoryDetail.brand',
            'createdBy',
            'updatedBy',
            'inventoryDetail.supplier',
            'user'
        ]);

        // add conditions that should always apply here
        switch ($type) {
            case "projcoorStock":
            case "maintenanceHeadStock":
                $query->andWhere([
                    'or',
                    ['inventory_reserve_item.created_by' => Yii::$app->user->identity->id],
                    ['inventory_reserve_item.user_id' => Yii::$app->user->identity->id]
                ]);
                break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'inventory_reserve_item.id' => $this->id,
            'inventory_reserve_item.reserved_qty' => $this->reserved_qty,
            'inventory_reserve_item.dispatched_qty' => $this->dispatched_qty,
            'inventory_reserve_item.available_qty' => $this->available_qty,
//            'inventory_reserve_item.created_at' => $this->created_at,
//            'inventory_reserve_item.updated_at' => $this->updated_at,
            'inventory_reserve_item.status' => $this->status,
            'inventory_reserve_item.reference_type' => $this->reference_type,
        ]);

        $query->andFilterWhere(['like', 'inventory_model.type', $this->inventory_model_id])
                ->andFilterWhere(['like', 'inventory_brand.name', $this->inventory_brand_id])
                ->andFilterWhere(['like', 'user.fullname', $this->created_by])
                ->andFilterWhere(['like', 'user.fullname', $this->user_id])
                ->andFilterWhere(['like', 'inventory_supplier.name', $this->inventory_detail_id]);

        if (!empty($this->created_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->created_at);
            if ($date) {
                $query->andWhere(['between', 'inventory_reserve_item.created_at',
                    $date->format('Y-m-d 00:00:00'),
                    $date->format('Y-m-d 23:59:59')
                ]);
            }
        }

        if (!empty($this->updated_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->updated_at);
            if ($date) {
                $query->andWhere(['between', 'inventory_reserve_item.updated_at',
                    $date->format('Y-m-d 00:00:00'),
                    $date->format('Y-m-d 23:59:59')
                ]);
            }
        }

        // Handle reference_id filter for different reference types
        if (!empty($this->reference_id)) {
            $searchTerm = $this->reference_id;
            $referenceCondition = ['or'];

            // 1. For 'reserve' type - search by user fullname
            $userIds = \common\models\User::find()
                    ->select('id')
                    ->where(['like', 'fullname', $searchTerm])
                    ->column();

            if (!empty($userIds)) {
                $referenceCondition[] = [
                    'and',
                    ['inventory_reserve_item.reference_type' => 'reserve'],
                    ['inventory_reserve_item.reference_id' => $userIds]
                ];
            }

            // 2. For project-related types (bom_detail and bomstockoutbound)
            // Search in project production panel codes from BomDetails
            $bomDetailIds = \frontend\models\bom\BomDetails::find()
                    ->alias('bd')
                    ->select('bd.id')
                    ->leftJoin('bom_master bm', 'bm.id = bd.bom_master')
                    ->leftJoin('project_production_panels ppp', 'ppp.id = bm.production_panel_id')
                    ->where(['like', 'ppp.project_production_panel_code', $searchTerm])
                    ->column();

            if (!empty($bomDetailIds)) {
                // For bom_detail type
                $referenceCondition[] = [
                    'and',
                    ['inventory_reserve_item.reference_type' => 'bom_detail'],
                    ['inventory_reserve_item.reference_id' => $bomDetailIds]
                ];

                // For bomstockoutbound type
                $referenceCondition[] = [
                    'and',
                    ['inventory_reserve_item.reference_type' => 'bomstockoutbound'],
                    ['inventory_reserve_item.reference_id' => $bomDetailIds]
                ];
            }

            // 3. Also search for stock outbound details directly (if needed)
            $stockOutboundIds = \frontend\models\bom\StockOutboundDetails::find()
                    ->alias('sod')
                    ->select('sod.id')
                    ->leftJoin('bom_details bd', 'bd.id = sod.bom_detail_id')
                    ->leftJoin('bom_master bm', 'bm.id = bd.bom_master')
                    ->leftJoin('project_production_panels ppp', 'ppp.id = bm.production_panel_id')
                    ->where(['like', 'ppp.project_production_panel_code', $searchTerm])
                    ->column();

            if (!empty($stockOutboundIds)) {
                $referenceCondition[] = [
                    'and',
                    ['inventory_reserve_item.reference_type' => 'bomstockoutbound'],
                    ['inventory_reserve_item.reference_id' => $stockOutboundIds]
                ];
            }

            // 4. Also search directly in reference_id for partial matches
            // This will catch any direct matches regardless of type
            $referenceCondition[] = ['like', 'inventory_reserve_item.reference_id', $searchTerm];

            // Apply the condition - always apply it, even if only the direct match condition exists
            $query->andWhere($referenceCondition);
        }

        $dataProvider->setSort([
            'attributes' => array_merge($dataProvider->sort->attributes, [
                'inventory_brand_id' => [
                    'asc' => ['inventory_brand.name' => SORT_ASC],
                    'desc' => ['inventory_brand.name' => SORT_DESC],
                ],
                'inventory_model_id' => [
                    'asc' => ['inventory_model.type' => SORT_ASC],
                    'desc' => ['inventory_model.type' => SORT_DESC],
                ]
            ]),
            'defaultOrder' => [
                'id' => SORT_DESC
            ]
        ]);

        return $dataProvider;
    }
}
