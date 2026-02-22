<?php

namespace frontend\models\bom;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\bom\bomdetails;

/**
 * bomdetailSearch represents the model behind the search form of `frontend\models\bom\bomdetails`.
 */
class bomdetailSearch extends bomdetails {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'bom_master', 'created_by'], 'integer'],
            [['model_type', 'brand', 'description', 'remark', 'created_at', 'active_status'], 'safe'],
            [['qty'], 'number'],
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
        $query = bomdetails::find();

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
            'bom_master' => $this->bom_master,
            'qty' => $this->qty,
            'active_status' => $this->active_status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'model_type', $this->model_type])
                ->andFilterWhere(['like', 'brand', $this->brand])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }

}
