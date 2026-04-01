<?php

namespace frontend\models\cmms;

use yii\base\Model;
use yii\data\ActiveDataProvider;
//use frontend\models\cmms\CmmsStockDispatchMaster;

/**
 * CmmsStockDispatchMasterSearch represents the model behind the search form of `frontend\models\cmms\CmmsStockDispatchMaster`.
 */
class CmmsStockDispatchMasterSearch extends CmmsStockDispatchMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'wo_id', 'trial_status'], 'integer'],
            [['created_by', 'received_by', 'status', 'dispatch_no', 'wo_type', 'created_at', 'status_updated_at'], 'safe'],
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
    public function search($params, $params2 = "") {
        $query = CmmsStockDispatchMaster::find();

        $query->joinWith([
            'createdBy',
            'receivedBy'
        ]);
        
        // add conditions that should always apply here
        switch ($params2) {
            case "pending":
                $query->where(['trial_status' => 0, 'received_by' => \Yii::$app->user->id]);
                break;
            case "acknowledged":
                $query->where(['trial_status' => 1, 'received_by' => \Yii::$app->user->id]);
                break;
        }

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
            'wo_id' => $this->wo_id,
//            'created_at' => $this->created_at,
//            'created_by' => $this->created_by,
//            'received_by' => $this->received_by,
            'cmms_stock_dispatch_master.status' => $this->status,
//            'status_updated_at' => $this->status_updated_at,
            'trial_status' => $this->trial_status,
        ]);

        $query->andFilterWhere(['like', 'dispatch_no', $this->dispatch_no])
                ->andFilterWhere(['like', 'wo_type', $this->wo_type])
                ->andFilterWhere(['like', 'creator.fullname', $this->created_by])
                ->andFilterWhere(['like', 'receiver.fullname', $this->received_by])
                ->andFilterWhere(['like', 'cmms_stock_dispatch_master.created_at', $this->created_at])
                ->andFilterWhere(['like', 'status_updated_at', $this->status_updated_at]);

        return $dataProvider;
    }
}
