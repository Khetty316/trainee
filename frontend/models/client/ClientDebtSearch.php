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
    public $updated_by_name;
    public $client_code;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['client_code', 'company_name'], 'safe'],
            [['id', 'client_id'], 'integer'],
            [['tk_group_code', 'created_at', 'updated_at', 'month', 'company_name', 'created_by_name', 'updated_by_name', 'year'], 'safe'],
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
        $query = ClientDebt::find()
                ->joinWith([
                    'companyGroup',
                    'createdBy user',
                    'updatedBy updatedUser',
                    'client'
        ]);

        $query->orderBy([]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['client_code'] = [
            'asc' => ['clients.client_code' => SORT_ASC],
            'desc' => ['clients.client_code' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['company_name'] = [
            'asc' => ['clients.company_name' => SORT_ASC],
            'desc' => ['clients.company_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['created_by_name'] = [
            'asc' => ['user.fullname' => SORT_ASC],
            'desc' => ['user.fullname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['updated_by_name'] = [
            'asc' => ['updatedUser.fullname' => SORT_ASC],
            'desc' => ['updatedUser.fullname' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'client_debt.id' => $this->id,
            'client_debt.client_id' => $this->client_id,
        ]);

        $query->andFilterWhere([
            'client_debt.id' => $this->id,
            'client_debt.client_id' => $this->client_id,
        ]);

        $query->andFilterWhere([
            'like',
            'clients.client_code',
            $this->client_code
        ]);

        $query->andFilterWhere([
            'like',
            'clients.company_name',
            $this->company_name . '%',
            false
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

        $query->andFilterWhere(['like', 'user.fullname', $this->created_by_name])
                ->andFilterWhere(['tk_group_code' => $this->tk_group_code])
                ->andFilterWhere(['month' => $this->month])
                ->andFilterWhere(['like', 'balance', $this->balance])
                ->andFilterWhere(['like', 'year', $this->year]);

        if (!empty($this->updated_by_name)) {

            $query->andWhere([
                'like',
                'updatedUser.fullname',
                $this->updated_by_name . '%',
                false
            ]);
        }

        if (array_key_exists('sort', $params)) {

            switch ($params['sort']) {

                case "balance":
                    $query->orderBy(['balance' => SORT_ASC]);
                    break;

                case "-balance":
                    $query->orderBy(['balance' => SORT_DESC]);
                    break;

                case "month":
                    $query->orderBy(['month' => SORT_ASC]);
                    break;

                case "-month":
                    $query->orderBy(['month' => SORT_DESC]);
                    break;

                case "year":
                    $query->orderBy(['year' => SORT_ASC]);
                    break;

                case "-year":
                    $query->orderBy(['year' => SORT_DESC]);
                    break;

                case "created_at":
                    $query->orderBy(['created_at' => SORT_ASC]);
                    break;

                case "-created_at":
                    $query->orderBy(['created_at' => SORT_DESC]);
                    break;

                case "created_by_name":
                    $query->orderBy(['user.fullname' => SORT_ASC]);
                    break;

                case "-created_by_name":
                    $query->orderBy(['user.fullname' => SORT_DESC]);
                    break;

                case "updated_by_name":
                    $query->orderBy(['updatedUser.fullname' => SORT_ASC]);
                    break;

                case "-updated_by_name":
                    $query->orderBy(['updatedUser.fullname' => SORT_DESC]);
                    break;
            }
        }

        return $dataProvider;
    }
}
