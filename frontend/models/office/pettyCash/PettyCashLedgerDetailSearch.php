<?php

namespace frontend\models\office\pettyCash;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\office\pettyCash\PettyCashLedgerDetail;

/**
 * PettyCashLedgerDetailSearch represents the model behind the search form of `frontend\models\office\pettyCash\PettyCashLedgerDetail`.
 */
class PettyCashLedgerDetailSearch extends PettyCashLedgerDetail {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'pc_ledger_master_id', 'created_by'], 'integer'],
            [['date', 'voucher_no', 'ref_1', 'ref_2', 'description', 'created_at'], 'safe'],
            [['debit', 'credit', 'balance'], 'number'],
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
        $query = PettyCashLedgerDetail::find()->where(['created_by' => \Yii::$app->user->identity->id]);

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
            'pc_ledger_master_id' => $this->pc_ledger_master_id,
            'date' => $this->date,
            'debit' => $this->debit,
            'credit' => $this->credit,
            'balance' => $this->balance,
            'created_by' => $this->created_by,
//            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'voucher_no', $this->voucher_no])
                ->andFilterWhere(['like', 'ref_1', $this->ref_1])
                ->andFilterWhere(['like', 'ref_2', $this->ref_2])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'created_at', $this->created_at]);
        
//        $dataProvider->setSort([
//            'defaultOrder' => [
//                'id' => SORT_DESC
//            ],
//        ]);

        return $dataProvider;
    }
}
