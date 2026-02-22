<?php

namespace frontend\models\office\claim;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\office\claim\VClaimMasterDetails;

/**
 * VClaimMasterDetailsSearch represents the model behind the search form of `frontend\models\office\claim\VClaimMasterDetails`.
 */
class VClaimMasterDetailsSearch extends VClaimMasterDetails
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['claim_master_id', 'claimant_id', 'superior_id', 'claims_status', 'master_updated_by', 'is_deleted', 'detail_id', 'detail_updated_by'], 'integer'],
            [['claim_code', 'claimant_fullname', 'claim_type', 'claim_type_name', 'superior_fullname', 'claims_status_name', 'master_created_date', 'master_updated_date', 'master_updated_by_fullname', 'ref_filename', 'ref_code', 'receipt_date', 'description', 'detail_created_date', 'detail_updated_date', 'detail_updated_by_fullname'], 'safe'],
            [['receipt_amount', 'amount_to_be_paid'], 'number'],
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
        $query = VClaimMasterDetails::find();

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
            'claim_master_id' => $this->claim_master_id,
            'claimant_id' => $this->claimant_id,
            'superior_id' => $this->superior_id,
            'claims_status' => $this->claims_status,
            'master_created_date' => $this->master_created_date,
            'master_updated_date' => $this->master_updated_date,
            'master_updated_by' => $this->master_updated_by,
            'is_deleted' => $this->is_deleted,
            'detail_id' => $this->detail_id,
            'receipt_date' => $this->receipt_date,
            'receipt_amount' => $this->receipt_amount,
            'amount_to_be_paid' => $this->amount_to_be_paid,
            'detail_created_date' => $this->detail_created_date,
            'detail_updated_date' => $this->detail_updated_date,
            'detail_updated_by' => $this->detail_updated_by,
        ]);

        $query->andFilterWhere(['like', 'claim_code', $this->claim_code])
            ->andFilterWhere(['like', 'claimant_fullname', $this->claimant_fullname])
            ->andFilterWhere(['like', 'claim_type', $this->claim_type])
            ->andFilterWhere(['like', 'claim_type_name', $this->claim_type_name])
            ->andFilterWhere(['like', 'superior_fullname', $this->superior_fullname])
            ->andFilterWhere(['like', 'claims_status_name', $this->claims_status_name])
            ->andFilterWhere(['like', 'master_updated_by_fullname', $this->master_updated_by_fullname])
            ->andFilterWhere(['like', 'ref_filename', $this->ref_filename])
            ->andFilterWhere(['like', 'ref_code', $this->ref_code])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'detail_updated_by_fullname', $this->detail_updated_by_fullname]);

        return $dataProvider;
    }
}
