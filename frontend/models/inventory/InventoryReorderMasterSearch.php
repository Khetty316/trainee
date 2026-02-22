<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\InventoryReorderMaster;

/**
 * InventoryReorderMasterSearch represents the model behind the search form of `frontend\models\inventory\InventoryReorderMaster`.
 */
class InventoryReorderMasterSearch extends InventoryReorderMaster {

    public $department_name;
    public $prf_no;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'status'], 'integer'],
            [['department_code', 'requested_at', 'created_at', 'prereq_form_master_id', 'requested_by', 'approved_by', 'created_by'], 'safe'],
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
        $query = InventoryReorderMaster::find();

        $query->joinWith([
            'department d',
            'prereqFormMaster pfm',
            'status0 sts',
            'requestedBy rb',
            'approvedBy ab',
            'createdBy cb',
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Exact filters
        $query->andFilterWhere([
            'inventory_reorder_master.status' => $this->status,
        ]);

        // LIKE filters
        $query->andFilterWhere(['like', 'd.department_name', $this->department_code])
                ->andFilterWhere(['like', 'pfm.prf_no', $this->prereq_form_master_id])
                ->andFilterWhere(['like', 'rb.fullname', $this->requested_by])
                ->andFilterWhere(['like', 'ab.fullname', $this->approved_by])
                ->andFilterWhere(['like', 'cb.fullname', $this->created_by]);

        return $dataProvider;
    }
}
