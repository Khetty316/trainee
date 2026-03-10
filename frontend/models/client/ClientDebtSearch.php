<?php

namespace frontend\models\client;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\client\ClientDebt;

/**
 * ClientDebtSearch represents the model behind the search form of `frontend\models\client\ClientDebt`.
 */
class ClientDebtSearch extends ClientDebt
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'month', 'year', 'created_by', 'updated_by'], 'integer'],
            [['tk_group_code', 'created_at', 'updated_at'], 'safe'],
            [['balance'], 'number'],
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
        $query = ClientDebt::find();

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
            'client_id' => $this->client_id,
            'month' => $this->month,
            'year' => $this->year,
            'balance' => $this->balance,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['tk_group_code' => $this->tk_group_code]);

        return $dataProvider;
    }
}
