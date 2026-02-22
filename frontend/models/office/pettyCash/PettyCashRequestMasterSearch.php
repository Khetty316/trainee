<?php

namespace frontend\models\office\pettyCash;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\office\pettyCash\PettyCashRequestMaster;

/**
 * PettyCashRequestMasterSearch represents the model behind the search form of `frontend\models\office\pettyCash\PettyCashRequestMaster`.
 */
class PettyCashRequestMasterSearch extends PettyCashRequestMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'status', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['ref_code', 'voucher_no', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
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
        $query = PettyCashRequestMaster::find();

        // add conditions that should always apply here
        switch ($type) {
            case "pendingPersonal":
                $query->where(['IN', 'petty_cash_request_master.status', \frontend\models\RefGeneralStatus::STATUS_Pending])
                        ->andWhere(['petty_cash_request_master.created_by' => \Yii::$app->user->identity->id])
                        ->andWhere(['petty_cash_request_master.deleted_by' => NULL]);
                break;

            case "allPersonal":
                $query->where(['petty_cash_request_master.created_by' => \Yii::$app->user->identity->id]);
                break;

            case "pendingFinance":
                $query->where(['IN', 'petty_cash_request_master.status', \frontend\models\RefGeneralStatus::STATUS_Pending_Finance])
                        ->andWhere([
                            'or',
                            ['petty_cash_request_master.finance_id' => \Yii::$app->user->identity->id],
                            ['petty_cash_request_master.finance_id' => null]
                        ])
                        ->andWhere(['petty_cash_request_master.deleted_by' => null]);

                break;

            default:
                // Handle unknown type or no filter
//                $query->andWhere(['claim_master.is_deleted' => 0]);
                break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user a", "petty_cash_request_master.created_by = a.id");

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'petty_cash_request_master.status' => $this->status,
//            'created_by' => $this->created_by,
//            'created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'updated_at' => $this->updated_at,
//            'deleted_by' => $this->deleted_by,
//            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['like', 'ref_code', $this->ref_code])
                ->andFilterWhere(['like', 'voucher_no', $this->voucher_no])
                ->andFilterWhere(['like', 'petty_cash_request_master.created_at', $this->created_at]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'updated_at' => SORT_DESC
            ],
        ]);

        return $dataProvider;
    }
}
