<?php

namespace frontend\models\working\claim;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\claim\VClaimDetail;
use Yii;

/**
 * ClaimsDetailSearch represents the model behind the search form of `frontend\models\working\claim\ClaimsDetail`.
 */
class VClaimDetailSearch extends VClaimDetail {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['claims_master_id', 'claims_mi_id', 'claims_detail_id', 'receipt_lost', 'claims_status'], 'integer'],
            [['claims_id', 'invoice_date', 'mi_idx_no', 'claimant', 'company_name', 'receipt_no', 'claims_status_name', 'detail', 'claim_type', 'claim_type_name'], 'safe'],
            [['amount'], 'number']
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
    public function search($params, $type = "", $claimType = "") {
        $query = VClaimDetail::find();

        switch ($type) {

            case "superClaimMedical":
                $query->where("claim_type = 'med'")
                        ->andWhere('claims_status in (2,3,4)');
                break;
            case "_viewClaimMedicalDetail":
                $query->where("claimant_id=$claimType[1] AND YEAR(invoice_date) = $claimType[0]")
                        ->andWhere("claim_type = 'med' AND claims_status in (4)");
                break;

            case "general":
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
            'claims_detail_id' => $this->claims_detail_id
//            'claims_master_id' => $this->claims_master_id,
//            'claims_mi_id' => $this->claims_mi_id,
//            'claims_detail_id' => $this->claims_detail_id,
//            'receipt_lost' => $this->receipt_lost,
//            'detail' => $this->detail,
//            'claims_status' => $this->claims_status,
//            'claims_id' => $this->claims_id,
//            'invoice_date' => $this->invoice_date,
//            'mi_idx_no' => $this->mi_idx_no,
//            'claimant' => $this->claimant,
//            'company_name' => $this->company_name,
//            'receipt_no' => $this->receipt_no,
//            'claims_status_name' => $this->claims_status_name,
//            'claim_type' => $this->claim_type,
//            'amount' => $this->amount,
        ]);

        $query->andFilterWhere(['like', 'claims_id', $this->claims_id])
                ->andFilterWhere(['like', 'detail', $this->detail])
                ->andFilterWhere(['like', 'claims_status_name', $this->claims_status_name])
                ->andFilterWhere(['like', 'amount', $this->amount])
                ->andFilterWhere(['like', 'claim_type_name', $this->claim_type_name])
                ->andFilterWhere(['like', 'invoice_date', $this->invoice_date]);
        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['claims_detail_id' => SORT_DESC]);
        }

        return $dataProvider;
    }

}
