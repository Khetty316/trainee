<?php

namespace frontend\models\client;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\client\ClientDebt;
//
/**
 * ClientDebtSearch represents the model behind the search form of `frontend\models\client\ClientDebt`.
 */
class ClientDebtSearch extends ClientDebt {

    public $company_name;
    public $created_by_name;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'client_id'], 'integer'],
            [['tk_group_code', 'created_at', 'updated_at', 'month', 'company_name','created_by_name', 'year', 'updated_by'], 'safe'],
            [['balance'], 'number'],
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
        $query = ClientDebt::find()->joinWith(['companyGroup', 'createdBy']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['created_by_name'] = [
            'asc' => ['user.username' => SORT_ASC],
            'desc' => ['user.username' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'client_debt.id' => $this->id,
            'client_debt.client_id' => $this->client_id,
            'client_debt.updated_by' => $this->updated_by,
        ]);

        if (!empty($this->created_at)) {

            $date = \DateTime::createFromFormat('M d, Y', $this->created_at);

            if ($date) {
                $query->andWhere(
                        new \yii\db\Expression("DATE(client_debt.created_at) = :date"),
                        [':date' => $date->format('Y-m-d')]
                );
            }
        }
        
        if (!empty($this->updated_at)) {

            $date = \DateTime::createFromFormat('M d, Y', $this->updated_at);

            if ($date) {
                $query->andWhere(
                        new \yii\db\Expression("DATE(client_debt.updated_at) = :date"),
                        [':date' => $date->format('Y-m-d')]
                );
            }
        }

        $query->andFilterWhere(['like', 'ref_company_group_list.company_name', $this->company_name])
                ->andFilterWhere(['like', 'user.username', $this->created_by_name])
                ->andFilterWhere(['tk_group_code' => $this->tk_group_code])
                ->andFilterWhere(['month' => $this->month])
                ->andFilterWhere(['like', 'balance', $this->balance])
                ->andFilterWhere(['like', 'year', $this->year]);

        return $dataProvider;
    }
}
