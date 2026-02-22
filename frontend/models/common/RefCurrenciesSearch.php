<?php

namespace frontend\models\common;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\common\RefCurrencies;

/**
 * RefCurrenciesSearch represents the model behind the search form of `frontend\models\common\RefCurrencies`.
 */
class RefCurrenciesSearch extends RefCurrencies {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['currency_id', 'active'], 'integer'],
            [['currency_name', 'currency_code', 'currency_sign', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
            [['exchange_rate'], 'number'],
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
        $query = RefCurrencies::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user a", "ref_currencies.created_by = a.id");
        $query->join("LEFT JOIN", "user b", "ref_currencies.updated_by = b.id");

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'currency_id' => $this->currency_id,
            'exchange_rate' => $this->exchange_rate,
            'active' => $this->active,
//            'created_by' => $this->created_by,
//            'ref_currencies.created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'ref_currencies.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'currency_name', $this->currency_name])
                ->andFilterWhere(['like', 'currency_code', $this->currency_code])
                ->andFilterWhere(['like', 'currency_sign', $this->currency_sign])
                ->andFilterWhere(['like', 'a.fullname', $this->created_by])
                ->andFilterWhere(['like', 'b.fullname', $this->updated_by]);

        if (!empty($this->created_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->created_at);
            if ($date) {
                $query->andFilterWhere(['between',
                    'ref_currencies.created_at',
                    $date->format('Y-m-d 00:00:00'),
                    $date->format('Y-m-d 23:59:59'),
                ]);
            }
        }

        if (!empty($this->updated_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->updated_at);
            if ($date) {
                $query->andFilterWhere(['between',
                    'ref_currencies.updated_at',
                    $date->format('Y-m-d 00:00:00'),
                    $date->format('Y-m-d 23:59:59'),
                ]);
            }
        }

        return $dataProvider;
    }
}
