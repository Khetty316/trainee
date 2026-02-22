<?php

namespace frontend\models\office\pettyCash;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\office\pettyCash\PettyCashReplenishment;

/**
 * PettyCashReplenishmentSearch represents the model behind the search form of `frontend\models\office\pettyCash\PettyCashReplenishment`.
 */
class PettyCashReplenishmentSearch extends PettyCashReplenishment {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'superior_id', 'status', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['voucher_no', 'ref_code', 'purpose', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['amount_requested', 'amount_approved'], 'number'],
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
        $query = PettyCashReplenishment::find();

        // add conditions that should always apply here
        switch ($type) {
//            case "finance":
//                $query->where(['created_by' => \Yii::$app->user->identity->id]);
//                break;

            case "pendingDirector":
                $query->where(['status' => \frontend\models\RefGeneralStatus::STATUS_GetDirectorApproval]);
                break;

            default:
                // Handle unknown type or no filter
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
            'amount_requested' => $this->amount_requested,
            'amount_approved' => $this->amount_approved,
            'superior_id' => $this->superior_id,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'deleted_by' => $this->deleted_by,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['like', 'ref_code', $this->ref_code])
                ->andFilterWhere(['like', 'voucher_no', $this->voucher_no])
                ->andFilterWhere(['like', 'purpose', $this->purpose]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
        ]);

        return $dataProvider;
    }
}
